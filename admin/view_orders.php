<?php
require_once __DIR__ . '/../middleware/require_admin.php';
require_once __DIR__ . '/../config/database.php';

$success_msg = "";

if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, trim($_POST['status']));

    // Check old status to avoid reducing stock multiple times
    $res = mysqli_query($conn, "SELECT status FROM orders WHERE id=$order_id");
    if ($res && mysqli_num_rows($res) > 0) {
        $old_row = mysqli_fetch_assoc($res);
        $old_status = $old_row['status'];

        if ($new_status === 'Delivered' && $old_status !== 'Delivered') {
            // Reduce stock
            $items_res = mysqli_query($conn, "SELECT product_id, quantity FROM order_items WHERE order_id=$order_id");
            while($item = mysqli_fetch_assoc($items_res)) {
                $pid = intval($item['product_id']);
                $qty = intval($item['quantity']);
                mysqli_query($conn, "UPDATE products SET stock = GREATEST(0, stock - $qty) WHERE id=$pid");
            }
        }

        mysqli_query($conn, "UPDATE orders SET status='$new_status' WHERE id=$order_id");
        $success_msg = "Status Updated to '{$new_status}' successfully!";
    }
}

if (isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id=$order_id");
    mysqli_query($conn, "DELETE FROM orders WHERE id=$order_id");
    $success_msg = "Order #{$order_id} deleted.";
}

// Fetch orders AFTER processing actions so the UI renders the latest state
$orders = mysqli_query($conn,"
SELECT o.*, u.full_name AS user_name
FROM orders o
JOIN users u ON o.user_id = u.id
ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Orders | Admin</title>
<link rel="stylesheet" href="assets/dashboard.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'view_orders'; include 'sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Order Management</h1>
        <span><?php echo date("d M Y"); ?></span>
    </div>

    <div class="panel">
        <?php if (!empty($success_msg)): ?>
            <div style="background: rgba(74, 222, 128, 0.2); color: #166534; border: 1px solid #4ade80; padding: 10px 15px; margin-bottom: 20px; border-radius: 4px; font-weight: 500; font-family: 'Inter', sans-serif;">
                <ion-icon name="checkmark-circle-outline" style="vertical-align: middle; font-size: 1.2rem;"></ion-icon> 
                <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>
        
        <h3>Recent Orders</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Update</th>
            </tr>

            <?php while($o=mysqli_fetch_assoc($orders)) { ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td><?php echo $o['user_name']; ?></td>
                <td>₹<?php echo $o['total_amount']; ?></td>

                <td>
                    <span class="stock <?php echo strtolower($o['status']); ?>" style="text-transform: capitalize;">
                        <?php echo $o['status']; ?>
                    </span>
                </td>

                <td>
                    <form method="post" style="display:flex; gap:0.5rem; align-items:center;">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <input type="hidden" name="update_status" value="1">
                        <select name="status" onchange="this.form.submit()" style="margin:0; padding:0.4rem; font-size:0.9rem; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                            <option <?php if($o['status']=="Pending") echo "selected"; ?>>Pending</option>
                            <option <?php if($o['status']=="Paid") echo "selected"; ?>>Paid</option>
                            <option <?php if($o['status']=="Shipped") echo "selected"; ?>>Shipped</option>
                            <option <?php if($o['status']=="Delivered") echo "selected"; ?>>Delivered</option>
                            <option <?php if($o['status']=="Cancelled") echo "selected"; ?>>Cancelled</option>
                        </select>
                    </form>
                    <form method="post" onsubmit="return confirm('Are you sure?');" style="display:inline-block; margin-top:5px;">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <button name="delete_order" style="background:red; color:white; border:none; padding:4px 8px; border-radius:4px; cursor:pointer;">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</div>

</body>
</html>


