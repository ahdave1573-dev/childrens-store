<?php
require_once 'includes/session.php';
require_once __DIR__ . '/config/database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }

    $error = "Invalid Email or Password.";
}
?>
<?php
$page_title = "Login | Childrens-Store";
$page_desc = "Login to your Children's Store account to track your orders, manage your shipping information, and checkout faster.";
$page_keywords = "login childrens store, sign in, kids store account access";
require_once 'includes/header.php';
?>
<div class="container" style="padding: 3rem 5%; max-width: 500px; margin: 0 auto; min-height: 60vh; display: flex; align-items: center;">
    <div style="background: white; padding: 2.5rem; border-radius: var(--radius); box-shadow: var(--shadow); width: 100%;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Welcome Back</h2>
        
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; text-align: center;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <button type="submit" class="btn-buy" style="width: 100%; border: none; cursor: pointer; padding: 1rem; font-size: 1.1rem;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">Don't have an account? <a href="register.php" style="color: var(--primary); text-decoration: none; font-weight: bold;">Register here</a></p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
