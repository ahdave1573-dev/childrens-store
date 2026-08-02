<?php
// checkout_process.php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$total_amount = 0;

try {
    $conn->begin_transaction();

    // 1. Calculate Total & Validate Stock
    foreach ($cart as $item) {
        $stmt = $conn->prepare("SELECT price, stock FROM products WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $item['id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $prod = $res->fetch_assoc();

        if ($prod['stock'] < $item['qty']) {
            throw new Exception("Product out of stock: " . $item['id']);
        }
        $total_amount += $prod['price'] * $item['qty'];
    }

    // 2. Generate Order Number
    $dateStr = date('Ymd');
    $countSql = "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()";
    $countRes = $conn->query($countSql);
    $count = $countRes->fetch_assoc()['count'] + 1;
    $order_number = 'ORD-' . $dateStr . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

    // 3. Insert Order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, status) VALUES (?, ?, ?, 'Paid')");
    $stmt->bind_param("isd", $user_id, $order_number, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // 4. Insert Items & Deduct Stock
    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
    $stmtStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    foreach ($cart as $item) {
        $prodParams = $conn->query("SELECT price FROM products WHERE id = " . $item['id'])->fetch_assoc();
        $price = $prodParams['price'];
        
        $stmtItem->bind_param("iiid", $order_id, $item['id'], $item['qty'], $price);
        $stmtItem->execute();

        $stmtStock->bind_param("ii", $item['qty'], $item['id']);
        $stmtStock->execute();
    }

    $conn->commit();
    
    // 5. Clear Cart & Redirect
    $_SESSION['cart'] = [];
    header("Location: order_success.php?order=" . $order_number);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Order Failed: " . $e->getMessage());
}
?>
