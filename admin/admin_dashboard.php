<?php
require_once __DIR__ . '/../middleware/require_admin.php';
require_once __DIR__ . '/../config/database.php';

// Basic Counts
$user  = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM users"));
$order = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM orders"));

// Advanced Stats
$revenue_query = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status IN ('Paid', 'Delivered')");
$revenue = mysqli_fetch_assoc($revenue_query)['total'] ?? 0;

$low_stock = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM products WHERE stock < 10"));

// Recent Orders
$recent_orders = mysqli_query($conn, "
    SELECT o.id, u.full_name AS user_name, o.total_amount, o.status, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC LIMIT 5
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin Dashboard | Childrens-Store</title>
<link rel="stylesheet" href="assets/dashboard.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'dashboard'; include 'sidebar.php'; ?>

<!-- MAIN -->
<div class="main">

<div class="topbar">
    <h1>Dashboard</h1>
    <span><?php echo date("d M Y, h:i A"); ?></span>
</div>

<div class="cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Revenue Card -->
    <div class="card" style="background:#fff; padding:1.5rem; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.06); border-left: 4px solid var(--primary); display:flex; justify-content:space-between; align-items:center; transition: transform 0.2s;">
        <div>
            <h2 style="font-size:2rem; font-weight:700; color:var(--dark); margin-bottom:0.2rem;">₹<?php echo number_format($revenue); ?></h2>
            <p style="color:#64748b; font-size:0.9rem; font-weight:500;">Total Revenue</p>
        </div>
        <div style="font-size:2.5rem; color:var(--primary); opacity:0.8;"><ion-icon name="cash-outline"></ion-icon></div>
    </div>
    <!-- Orders Card -->
    <div class="card" style="background:#fff; padding:1.5rem; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.06); border-left: 4px solid var(--primary); display:flex; justify-content:space-between; align-items:center; transition: transform 0.2s;">
        <div>
            <h2 style="font-size:2rem; font-weight:700; color:var(--dark); margin-bottom:0.2rem;"><?php echo $order; ?></h2>
            <p style="color:#64748b; font-size:0.9rem; font-weight:500;">Total Orders</p>
        </div>
        <div style="font-size:2.5rem; color:var(--primary); opacity:0.8;"><ion-icon name="cart-outline"></ion-icon></div>
    </div>
    <!-- Stock Alert Card -->
    <div class="card" style="background:#fff; padding:1.5rem; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.06); border-left: 4px solid #ef4444; display:flex; justify-content:space-between; align-items:center; transition: transform 0.2s;">
        <div>
            <h2 style="font-size:2rem; font-weight:700; color:var(--dark); margin-bottom:0.2rem;"><?php echo $low_stock; ?></h2>
            <p style="color:#64748b; font-size:0.9rem; font-weight:500;">Low Stock Alert</p>
        </div>
        <div style="font-size:2.5rem; color:#ef4444; opacity:0.8;"><ion-icon name="alert-circle-outline"></ion-icon></div>
    </div>
    <!-- Users Card -->
    <div class="card" style="background:#fff; padding:1.5rem; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.06); border-left: 4px solid var(--primary); display:flex; justify-content:space-between; align-items:center; transition: transform 0.2s;">
        <div>
            <h2 style="font-size:2rem; font-weight:700; color:var(--dark); margin-bottom:0.2rem;"><?php echo $user; ?></h2>
            <p style="color:#64748b; font-size:0.9rem; font-weight:500;">Active Users</p>
        </div>
        <div style="font-size:2.5rem; color:var(--primary); opacity:0.8;"><ion-icon name="people-outline"></ion-icon></div>
    </div>
</div>

<div class="panel" style="margin-top: 2rem;">
    <h3>Recent Orders</h3>
    <?php if (mysqli_num_rows($recent_orders) > 0): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php while ($r = mysqli_fetch_assoc($recent_orders)): ?>
        <tr>
            <td>#<?php echo $r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['user_name'] ?? 'Guest'); ?></td>
            <td>₹<?php echo number_format($r['total_amount']); ?></td>
            <td>
                <span class="stock <?php echo strtolower($r['status']); ?>" style="text-transform: capitalize;">
                    <?php echo $r['status']; ?>
                </span>
            </td>
            <td><?php echo date("d M Y", strtotime($r['created_at'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>No recent orders found.</p>
    <?php endif; ?>
</div>

<div class="panel" style="margin-top: 2rem;">
    <h3>System Status</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 0.5rem; color: var(--success); font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
            <ion-icon name="checkmark-circle-outline"></ion-icon> Server Running
        </li>
        <li style="margin-bottom: 0.5rem; color: var(--success); font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
            <ion-icon name="checkmark-circle-outline"></ion-icon> Database Connected
        </li>
        <li style="margin-bottom: 0.5rem; color: var(--success); font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
            <ion-icon name="checkmark-circle-outline"></ion-icon> Admin Access Granted
        </li>
    </ul>
</div>

</div>

</body>
</html>
