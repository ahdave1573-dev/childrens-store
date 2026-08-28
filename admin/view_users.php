<?php
require_once __DIR__ . '/../middleware/require_admin.php';
require_once __DIR__ . '/../config/database.php';

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Users | Admin</title>
<link rel="stylesheet" href="assets/dashboard.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'view_users'; include 'sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <h1>User Management</h1>
        <span><?php echo date("d M Y"); ?></span>
    </div>

    <div class="panel">
        <h3>Registered Users</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Role</th>
                <th>Registered</th>
            </tr>
            <?php while($u = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td>#<?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['phone']); ?></td>
                <td><?php echo htmlspecialchars($u['city']); ?></td>
                <td>
                    <span style="padding:4px 8px; border-radius:4px; font-size:0.8rem; background:<?php echo $u['role']=='admin'?'var(--primary)':'#eee'; ?>; color:<?php echo $u['role']=='admin'?'white':'#333'; ?>;">
                        <?php echo ucfirst($u['role']); ?>
                    </span>
                </td>
                <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>
