<?php
require_once 'includes/session.php';
require_once __DIR__ . '/config/database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address_line = trim($_POST['address_line']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $pincode = trim($_POST['pincode']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All required fields must be filled.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already registered.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO users 
                (full_name, email, password, phone, address_line, city, state, pincode, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'user')");

            $insert->bind_param(
                "ssssssss",
                $full_name,
                $email,
                $hashed_password,
                $phone,
                $address_line,
                $city,
                $state,
                $pincode
            );

            if ($insert->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_role'] = 'user';
                header("Location: cart.php");
                exit;
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>
<?php
$page_title = "Register | Childrens-Store";
$page_desc = "Create your Children's Store account to easily order kids apparel, toys, baby care, and keep track of your order shipments.";
$page_keywords = "register account, children store registration, sign up kids store";
require_once 'includes/header.php';
?>
<div class="container" style="padding: 3rem 5%; max-width: 600px; margin: 0 auto;">
    <div style="background: white; padding: 2rem; border-radius: var(--radius); box-shadow: var(--shadow);">
        <h2 style="text-align: center; margin-bottom: 2rem;">Create an Account</h2>
        
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; text-align: center;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 0.5rem;">Full Name *</label>
                    <input type="text" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                
                <div style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 0.5rem;">Email Address *</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 0.5rem;">Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 0.5rem;">Address Line</label>
                    <input type="text" name="address_line" value="<?php echo htmlspecialchars($_POST['address_line'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">City</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">State</label>
                    <input type="text" name="state" value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 0.5rem;">Pincode</label>
                    <input type="text" name="pincode" value="<?php echo htmlspecialchars($_POST['pincode'] ?? ''); ?>" style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">Password *</label>
                    <input type="password" name="password" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">Confirm Password *</label>
                    <input type="password" name="confirm_password" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 5px;">
                </div>
            </div>

            <button type="submit" class="btn-buy" style="width: 100%; margin-top: 2rem; border: none; cursor: pointer;">Register</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">Already have an account? <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: bold;">Login here</a></p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
