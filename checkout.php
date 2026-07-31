<?php
require_once __DIR__ . '/includes/session.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/config/database.php';

$user_id = $_SESSION['user_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_pay'])) {
    $total_amount = floatval($_POST['total_amount'] ?? 0);
    $order_num = 'ORD' . time() . rand(100, 999);
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, order_number) VALUES (?, ?, 'Paid', ?)");
    $stmt->bind_param("ids", $user_id, $total_amount, $order_num);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        if (!empty($_SESSION['cart'])) {
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            foreach ($_SESSION['cart'] as $item) {
                $qty = $item['qty'];
                $item_stmt->bind_param("iii", $order_id, $item['id'], $qty);
                $item_stmt->execute();
            }
        }
        
        unset($_SESSION['cart']);
        header('Location: order_success.php');
        exit;
    }
}

$page_title = "Checkout | Childrens-Store";
$page_desc = "Complete your secure order at Children's Store. Review your shipping details and order total.";
$page_keywords = "checkout, complete order, secure payment children store";

require_once 'includes/header.php';
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 3. Check Cart
if (empty($_SESSION['cart'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$cart = $_SESSION['cart'];
$total = 0;
?>

<div class="container" style="padding: 3rem 5%; min-height: 70vh;">
    <h1 style="margin-bottom: 0.5rem; font-family:'Quicksand', sans-serif; font-weight:700;">Checkout</h1>
    <p style="color:var(--light-grey); margin-bottom: 2.5rem; font-weight:500;">Review your delivery details and order summary to complete checkout</p>
    
    <div class="cart-layout">
        <!-- Left: Shipping Details -->
        <div class="premium-card">
            <h2 style="font-family:'Quicksand', sans-serif; font-size:1.4rem; font-weight:700; color:var(--dark); border-bottom: 2px solid var(--soft-bg); padding-bottom:1rem; margin-bottom:1.5rem;">Shipping Address</h2>
            
            <div class="checkout-address-box">
                <p style="margin-bottom: 0.8rem; font-size:1.1rem; color:var(--dark);"><strong>Recipient:</strong> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Guest User'); ?></p>
                <p style="margin-bottom: 0.8rem; color:var(--dark);"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
                <p style="color:var(--dark);"><strong>Address:</strong> <?php echo htmlspecialchars($user['address_line'] ?? 'N/A'); ?>, <?php echo htmlspecialchars($user['city'] ?? ''); ?> - <?php echo htmlspecialchars($user['pincode'] ?? ''); ?></p>
            </div>
            
            <div style="margin-top: 2rem;">
                <p style="color:var(--light-grey); font-size:0.9rem;">Need to update your shipping information? You can update it in your <a href="profile.php" style="color:var(--kids-blue); font-weight:700;">Profile Settings</a>.</p>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="premium-card" style="height: fit-content;">
            <h2 class="summary-title">Order Summary</h2>
            <div style="max-height: 250px; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 5px;">
                <?php foreach ($cart as $item): 
                    $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
                    $stmt->bind_param("i", $item['id']);
                    $stmt->execute();
                    $prod = $stmt->get_result()->fetch_assoc();
                    $item_total = $prod['price'] * $item['qty'];
                    $total += $item_total;
                ?>
                <div class="summary-row" style="border-bottom: 1px solid var(--soft-bg); padding-bottom: 0.8rem; margin-bottom: 0.8rem;">
                    <div>
                        <span style="font-weight: 700; font-family:'Quicksand', sans-serif; display:block; color:var(--dark);"><?php echo htmlspecialchars($prod['name']); ?></span>
                        <span style="font-size: 0.85rem; color: var(--light-grey);">Qty: <?php echo $item['qty']; ?> | Size: <?php echo htmlspecialchars($item['size']); ?></span>
                    </div>
                    <span style="font-weight: 600; color:var(--dark);">₹<?php echo number_format($item_total); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="summary-row">
                <span style="color:var(--light-grey); font-weight:500;">Shipping Fee</span>
                <span style="color:var(--kids-green); font-weight:700;">FREE</span>
            </div>
            
            <div class="summary-row total-row">
                <span>Total Amount</span>
                <span>₹<?php echo number_format($total); ?></span>
            </div>

            <form action="checkout.php" method="POST" style="margin-top: 2rem;">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                <button type="submit" name="confirm_pay" class="btn-buy" style="width: 100%; padding: 15px 0;">Confirm & Pay</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
