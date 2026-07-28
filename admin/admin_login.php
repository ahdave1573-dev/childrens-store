<?php
require_once '../includes/session.php';
require_once __DIR__ . '/../config/database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {

            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['full_name'];
            $_SESSION['user_role'] = $admin['role'];

            header("Location: admin_dashboard.php");
            exit;
        }
    }

    $error = "Invalid Admin Credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Childrens-Store</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body class="login-body">

<div class="login-card">
    <div class="login-logo">
        <ion-icon name="balloon-outline"></ion-icon>
    </div>
    <h2 style="margin-bottom: 0.5rem; color: var(--text-main);">Welcome Back</h2>
    <p style="margin-bottom: 2rem; color: var(--text-muted);">Sign in to manage the store</p>

    <form method="post">
        <div style="text-align: left; margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem;">Admin Email</label>
            <input type="email" name="email" placeholder="e.g. admin@childrens-store.local" required style="margin-bottom: 0;">
        </div>
        
        <div style="text-align: left; margin-bottom: 2rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem;">Password</label>
            <input type="password" name="password" placeholder="••••••••" required style="margin-bottom: 0;">
        </div>

        <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Sign In</button>
    </form>

    <?php if ($error != "") { ?>
        <div style="margin-top: 1.5rem; padding: 0.75rem; background: #fee2e2; color: #dc2626; border-radius: var(--radius-md); font-size: 0.9rem; font-weight: 500;">
            <?php echo $error; ?>
        </div>
    <?php } ?>
</div>

</body>
</html>
