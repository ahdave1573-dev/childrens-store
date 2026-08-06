<?php
require_once __DIR__ . '/config/database.php';

echo "Starting Admin Auth Migration...\n";

// 1. Add missing columns to users table if they don't exist
$columns = [
    'password' => "VARCHAR(255)",
    'role' => "ENUM('user', 'admin') DEFAULT 'user'",
    'google_id' => "VARCHAR(255) UNIQUE",
    'phone' => "VARCHAR(20)",
    'address_line' => "TEXT",
    'city' => "VARCHAR(100)",
    'state' => "VARCHAR(100)",
    'pincode' => "VARCHAR(10)"
];

foreach ($columns as $col => $def) {
    $check = $conn->query("SHOW COLUMNS FROM users LIKE '$col'");
    if ($check->num_rows == 0) {
        echo "Adding column $col...\n";
        $sql = "ALTER TABLE users ADD COLUMN $col $def";
        if ($conn->query($sql)) {
            echo "Column $col added successfully.\n";
        } else {
            echo "Error adding column $col: " . $conn->error . "\n";
        }
    } else {
        echo "Column $col already exists.\n";
    }
}

// 2. Remove legacy admin table if it exists
$conn->query("DROP TABLE IF EXISTS admin");
echo "Legacy admin table dropped (if existed).\n";

// 3. Seed Admin User
$email = 'admin@childrens-store.local';
$password = 'admin123'; // Plain text as requested
$role = 'admin';
$full_name = 'Admin User';

// Check if admin exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo "Admin user exists. Updating role and password...\n";
    $stmt = $conn->prepare("UPDATE users SET password = ?, role = ? WHERE email = ?");
    $stmt->bind_param("sss", $password, $role, $email);
} else {
    echo "Creating new admin user...\n";
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $password, $role);
}

if ($stmt->execute()) {
    echo "Admin user seeded successfully.\n";
} else {
    echo "Error seeding admin user: " . $stmt->error . "\n";
}

echo "Migration completed.\n";
?>
