<?php
// cart_action.php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $product_id = intval($_POST['product_id'] ?? $_GET['id'] ?? 0);
    $size = $_POST['size'] ?? $_GET['size'] ?? 'One Size';
    $qty = 1;

    if ($product_id <= 0) {
        header("Location: index.php");
        exit;
    }

    // Check if item exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $product_id && $item['size'] === $size) {
            $item['qty'] += $qty;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'qty' => $qty,
            'size' => $size
        ];
    }

    header("Location: cart.php");
    exit;
}

if ($action === 'remove') {
    $index = intval($_GET['index']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'update_qty') {
    $index = intval($_POST['index']);
    $qty = intval($_POST['qty']);
    if (isset($_SESSION['cart'][$index])) {
        if ($qty < 1) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        } else {
            $_SESSION['cart'][$index]['qty'] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

header("Location: index.php");
exit;
?>
