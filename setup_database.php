<?php
// setup_database.php
require_once __DIR__ . '/config/database.php';

try {
    // Disable foreign key checks to allow dropping tables
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    $tables = ['order_items', 'orders', 'product_images', 'products', 'categories', 'users'];
    
    foreach ($tables as $table) {
        $conn->query("DROP TABLE IF EXISTS `$table`");
        echo "Dropped table: $table<br>";
    }

    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    // 1. Users Table
    $sql = "CREATE TABLE `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `full_name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `address_line` VARCHAR(255) DEFAULT NULL,
        `city` VARCHAR(100) DEFAULT NULL,
        `state` VARCHAR(100) DEFAULT NULL,
        `pincode` VARCHAR(20) DEFAULT NULL,
        `role` ENUM('user', 'admin') DEFAULT 'user',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (`email`)
    ) ENGINE=InnoDB";
    $conn->query($sql);
    echo "Created table: users<br>";

    // 2. Categories Table
    $sql = "CREATE TABLE `categories` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `image` VARCHAR(255) DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB";
    $conn->query($sql);
    echo "Created table: categories<br>";

    // 3. Products Table
    $sql = "CREATE TABLE `products` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `category_id` INT NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `short_description` VARCHAR(500) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `price` DECIMAL(10, 2) NOT NULL,
        `mrp` DECIMAL(10, 2) NOT NULL,
        `stock` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
        INDEX (`slug`)
    ) ENGINE=InnoDB";
    $conn->query($sql);
    echo "Created table: products<br>";

    // 4. Product Images Table
    $sql = "CREATE TABLE `product_images` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `product_id` INT NOT NULL,
        `image_url` VARCHAR(500) NOT NULL,
        `sort_order` INT DEFAULT 0,
        FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB";
    $conn->query($sql);
    echo "Created table: product_images<br>";

    // 5. Orders Table
    $sql = "CREATE TABLE `orders` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `order_number` VARCHAR(50) NOT NULL UNIQUE,
        `total_amount` DECIMAL(10, 2) NOT NULL,
        `status` ENUM('Pending', 'Paid', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        INDEX (`user_id`),
        INDEX (`status`)
    ) ENGINE=InnoDB";
    $conn->query($sql);
    echo "Created table: orders<br>";

    // 6. Order Items Table
    $sql = "CREATE TABLE `order_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `order_id` INT NOT NULL,
        `product_id` INT NOT NULL,
        `quantity` INT NOT NULL,
        `price_at_purchase` DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB";
    $conn->query($sql);
    echo "Created table: order_items<br>";

    echo "<hr><strong>Database Schema Rebuilt Successfully!</strong>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
