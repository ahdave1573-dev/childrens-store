<!-- includes/header.php -->
<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/auth.php';

// Calculate cart count
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item) && isset($item['qty'])) {
            $cart_count += (int)$item['qty'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Childrens-Store | Playful Kids Shop"; ?></title>
    <meta name="description" content="<?php echo isset($page_desc) ? htmlspecialchars($page_desc) : "Discover the best kids clothing, educational toys, baby care products, school supplies, and baby bedding at Children's Store. Playful, premium, and safe for your little ones."; ?>">
    <meta name="keywords" content="<?php echo isset($page_keywords) ? htmlspecialchars($page_keywords) : "kids shop, children store, baby clothing, toys for kids, educational toys, school supplies, baby care"; ?>">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Children's Store">
    <link rel="stylesheet" href="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); ?>/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="logo">Childrens<span>-</span>Store</a>
    
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="index.php#categories">Categories</a>
        <a href="index.php#featured">New Arrivals</a>
        <a href="contact.php">Contact</a>
    </div>

    <div class="nav-icons">
        <form action="index.php" method="GET" class="search-form" style="position:relative; display:flex; align-items:center; margin-right:15px;">
            <input type="text" name="search" placeholder="Search..." style="padding:10px 40px 10px 20px; border:2px solid var(--soft-bg); border-radius:30px; outline:none; font-family:'Poppins', sans-serif; font-size:0.85rem; width:120px; transition:all 0.3s ease;">
            <button type="submit" style="position:absolute; right:12px; background:none; border:none; cursor:pointer; color:var(--kids-brown);">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="cart.php" class="cart-icon">
            <i class="fas fa-shopping-bag"></i>
            <?php if($cart_count > 0): ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
        
        <?php if (isLoggedIn()): ?>
            <div class="user-menu">
                <div class="user-trigger">
                    <i class="fas fa-user-circle" style="font-size: 1.1rem;"></i>
                    <span>Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    <i class="fas fa-chevron-down" style="font-size: 0.7rem; opacity: 0.7;"></i>
                </div>
                <div class="user-dropdown">
                    <a href="profile.php">
                        <i class="fas fa-user" style="color: var(--kids-blue); width: 14px;"></i> Profile
                    </a>
                    <?php if (isAdmin()): ?>
                        <a href="admin/admin_dashboard.php">
                            <i class="fas fa-shield-alt" style="color: var(--kids-orange); width: 14px;"></i> Admin Panel
                        </a>
                    <?php endif; ?>
                    <div class="user-dropdown-divider"></div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt" style="width: 14px;"></i> Logout
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" style="color:var(--kids-blue); font-weight:600; margin-left: 10px;">Login</a>
            <a href="register.php" style="color:var(--kids-pink); font-weight:600; margin-left: 10px;">Register</a>
        <?php endif; ?>
    </div>
</nav>
