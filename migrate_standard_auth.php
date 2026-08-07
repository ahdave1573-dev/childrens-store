<?php
require_once __DIR__ . '/config/database.php';

echo "Starting DB Migration...\n";

// Ensure foreign key checks are disabled temporarily
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// 1. Drop existing admin table as per rule "Remove any dependency on old admin table"
$conn->query("DROP TABLE IF EXISTS admin");
echo "Admin table dropped (if existed).\n";

// 2. Adjust users table fields
$queries = [
    "ALTER TABLE users DROP COLUMN IF EXISTS google_id",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS password VARCHAR(255) NOT NULL AFTER email",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER password",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS address_line VARCHAR(255) AFTER phone",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS city VARCHAR(100) AFTER address_line",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS state VARCHAR(100) AFTER city",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS pincode VARCHAR(20) AFTER state",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user','admin') DEFAULT 'user' AFTER pincode",
    "ALTER TABLE users MODIFY COLUMN email VARCHAR(255) NOT NULL UNIQUE",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
];

foreach ($queries as $sql) {
    if ($conn->query($sql)) {
        echo "Success: $sql\n";
    } else {
        echo "Skipped/Error ($sql): " . $conn->error . "\n";
    }
}

// Ensure the first admin user exists in the users table
$email = 'admin@childrens-store.local';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$role = 'admin';
$full_name = 'Master Admin';

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $ins = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $ins->bind_param("ssss", $full_name, $email, $password, $role);
    $ins->execute();
    echo "Default Admin created successfully!\n";
} else {
    // Update existing admin password to use hashing just in case
    $upd = $conn->prepare("UPDATE users SET password = ?, role = ? WHERE email = ?");
    $upd->bind_param("sss", $password, $role, $email);
    $upd->execute();
    echo "Default Admin updated to use hashed password.\n";
}

$conn->query("SET FOREIGN_KEY_CHECKS = 1");
echo "Migration Complete.\n";
?>
