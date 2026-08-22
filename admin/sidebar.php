<div class="sidebar">
    <h2><ion-icon name="shield-checkmark-outline"></ion-icon> Admin</h2>
    <a href="admin_dashboard.php" class="<?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
        <ion-icon name="grid-outline"></ion-icon> Dashboard
    </a>
    <a href="add_category.php" class="<?php echo ($page == 'add_category') ? 'active' : ''; ?>">
        <ion-icon name="folder-open-outline"></ion-icon> Add Category
    </a>
    <a href="view_categories.php" class="<?php echo ($page == 'view_categories') ? 'active' : ''; ?>">
        <ion-icon name="list-outline"></ion-icon> Categories
    </a>
    <a href="add_product.php" class="<?php echo ($page == 'add_product') ? 'active' : ''; ?>">
        <ion-icon name="cube-outline"></ion-icon> Add Product
    </a>
    <a href="view_products.php" class="<?php echo ($page == 'view_products') ? 'active' : ''; ?>">
        <ion-icon name="layers-outline"></ion-icon> Products
    </a>
    <a href="view_orders.php" class="<?php echo ($page == 'view_orders') ? 'active' : ''; ?>">
        <ion-icon name="receipt-outline"></ion-icon> Orders
    </a>
    <a href="view_users.php" class="<?php echo ($page == 'view_users') ? 'active' : ''; ?>">
        <ion-icon name="people-outline"></ion-icon> Users
    </a>
    <a href="view_contacts.php" class="<?php echo ($page == 'view_contacts') ? 'active' : ''; ?>">
        <ion-icon name="mail-unread-outline"></ion-icon> Contacts
    </a>
    <a href="logout.php">
        <ion-icon name="log-out-outline"></ion-icon> Logout
    </a>
</div>


