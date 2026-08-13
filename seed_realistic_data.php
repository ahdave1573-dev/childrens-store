<?php
/**
 * seed_realistic_data.php
 * Generates realistic, analytics-ready test data for production database testing.
 *
 * Checks:
 * - 50+ users exist.
 * - 250-300 orders across last 180 days.
 * - Strict stock logic.
 * - Transaction safety.
 *
 * Usage: php seed_realistic_data.php
 */

require_once __DIR__ . '/config/database.php'; // Contains $conn: mysqli connection

// Enable error reporting for cli
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting Data Seeding...\n";
echo "-------------------------\n";

// --- 1. Users Creation ---
echo "Checking Users...\n";
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
$current_user_count = (int)$row['count'];
$users_needed = 50 - $current_user_count;

if ($users_needed > 0) {
    echo "Creating $users_needed new users...\n";
    $first_names = ['James', 'Mary', 'Robert', 'Patricia', 'John', 'Jennifer', 'Michael', 'Linda', 'David', 'Elizabeth', 'William', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica', 'Thomas', 'Sarah', 'Charles', 'Karen'];
    $last_names = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];

    $stmt_user = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $password_hash = password_hash('password123', PASSWORD_DEFAULT);

    for ($i = 0; $i < $users_needed; $i++) {
        $fn = $first_names[array_rand($first_names)];
        $ln = $last_names[array_rand($last_names)];
        $full_name = "$fn $ln";
        $email = strtolower($fn . '.' . $ln . rand(100, 9999) . '@example.com');
        
        // Ensure email uniqueness
        $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $email = strtolower($fn . '.' . $ln . rand(10000, 99999) . '@example.com');
        }

        $phone = '555-' . rand(100, 999) . '-' . rand(1000, 9999);
        $address = rand(10, 999) . " " . $last_names[array_rand($last_names)] . " St, City " . rand(1, 20);
        $created_at = date('Y-m-d H:i:s', mt_rand(strtotime('-180 days'), time()));

        $stmt_user->bind_param("ssssss", $full_name, $email, $password_hash, $phone, $address, $created_at);
        $stmt_user->execute();
    }
    $stmt_user->close();
    echo "Users created.\n";
} else {
    echo "Sufficient users already exist ($current_user_count).\n";
}

// --- 2. Load Products & Stock into Memory ---
echo "Loading products...\n";
$products = [];
// Now we can safely select is_active
$res_prod = $conn->query("SELECT id, price, stock, is_active, name FROM products WHERE is_active = 1");

if (!$res_prod || $res_prod->num_rows === 0) {
    die("Error: No active products found in DB. Cannot create orders.\n");
}
while ($p = $res_prod->fetch_assoc()) {
    $products[$p['id']] = [
        'id' => (int)$p['id'],
        'price' => (float)$p['price'],
        'stock' => (int)$p['stock'],
        'name' => $p['name']
    ];
}
echo "Loaded " . count($products) . " products.\n";

// --- 3. Generate Orders ---
// Fetch user IDs
$user_ids = [];
$res_u = $conn->query("SELECT id FROM users");
while ($r = $res_u->fetch_assoc()) {
    $user_ids[] = (int)$r['id'];
}

$orders_to_create = rand(250, 300);
echo "Generating $orders_to_create orders...\n";

$orders_created = 0;
$total_revenue_generated = 0;

/*
 * Status Distribution:
 * Delivered (60%), Processing (15%), Shipped (10%), Pending (10%), Cancelled (5%)
 */
function get_weighted_status() {
    $r = rand(1, 100);
    if ($r <= 60) return 'Delivered';
    if ($r <= 75) return 'Processing';
    if ($r <= 85) return 'Shipped';
    if ($r <= 95) return 'Pending';
    return 'Cancelled';
}

function get_payment_status($order_status) {
    if (in_array($order_status, ['Delivered', 'Shipped', 'Processing'])) return 'Paid';
    if ($order_status === 'Pending') return 'Pending';
    return (rand(0, 1) === 0) ? 'Refunded' : 'Failed';
}

$stmt_order = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, status, payment_status, shipping_name, shipping_address, shipping_phone, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
$stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

if (!$stmt_order || !$stmt_item || !$stmt_stock) {
    die("Prepare failed: " . $conn->error);
}

for ($i = 0; $i < $orders_to_create; $i++) {
    // Basic Order Data
    $user_id = $user_ids[array_rand($user_ids)];
    $status = get_weighted_status();
    $payment_status = get_payment_status($status);
    
    // Simulate Random Creation Date
    $timestamp = mt_rand(strtotime('-180 days'), time());
    $created_at = date('Y-m-d H:i:s', $timestamp);
    $updated_at = date('Y-m-d H:i:s', $timestamp + rand(60, 86400)); 
    if ($updated_at > date('Y-m-d H:i:s')) $updated_at = date('Y-m-d H:i:s'); 

    $s_name = "User $user_id Name"; 
    $s_address = "Address for User $user_id";
    $s_phone = "555-000-" . str_pad($user_id, 4, '0', STR_PAD_LEFT);

    // Select Order Items
    $num_items = rand(1, 5);
    $order_items_buffer = [];
    $order_total = 0;
    
    $should_deduct_stock = in_array($status, ['Delivered', 'Shipped', 'Processing']);

    // Check availability
    $product_keys = array_keys($products);
    shuffle($product_keys);

    foreach ($product_keys as $pid) {
        if (count($order_items_buffer) >= $num_items) break;

        $p = &$products[$pid];
        $qty = rand(1, 3);

        if ($should_deduct_stock) {
            if ($p['stock'] >= $qty) {
                // Good to go
                $order_items_buffer[] = [
                    'product_id' => $pid,
                    'quantity' => $qty,
                    'price' => $p['price']
                ];
                $order_total += ($p['price'] * $qty);
                $p['stock'] -= $qty;
            }
        } else {
            // No stock deduction needed
            $order_items_buffer[] = [
                'product_id' => $pid,
                'quantity' => $qty,
                'price' => $p['price']
            ];
            $order_total += ($p['price'] * $qty);
        }
    }

    if (empty($order_items_buffer)) {
        continue;
    }

    // --- TRANSACTION START ---
    $conn->begin_transaction();
    try {
        // 1. Insert Order with TEMP number
        $temp_num = 'TEMP-' . uniqid();
        
        $stmt_order->bind_param("isdsssssss", $user_id, $temp_num, $order_total, $status, $payment_status, $s_name, $s_address, $s_phone, $created_at, $updated_at);
        
        if (!$stmt_order->execute()) {
            throw new Exception("Order insert failed: " . $stmt_order->error);
        }
        $order_id = $conn->insert_id;

        // 2. Insert Items
        foreach ($order_items_buffer as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            if (!$stmt_item->execute()) {
                throw new Exception("Item insert failed: " . $stmt_item->error);
            }
            // 3. Update Stock in DB if needed
            if ($should_deduct_stock) {
                $stmt_stock->bind_param("ii", $item['quantity'], $item['product_id']);
                if (!$stmt_stock->execute()) {
                    throw new Exception("Stock update failed: " . $stmt_stock->error);
                }
            }
        }

        // 4. Update Order Number
        $date_str = date('Ymd', strtotime($created_at));
        $order_number = sprintf("ORD-%s-%05d", $date_str, $order_id);
        
        if (!$conn->query("UPDATE orders SET order_number = '$order_number' WHERE id = $order_id")) {
             throw new Exception("Order number update failed: " . $conn->error);
        }

        $conn->commit();
        $orders_created++;
        if ($payment_status === 'Paid') {
            $total_revenue_generated += $order_total;
        }

    } catch (Exception $e) {
        $conn->rollback();
        // Revert local stock changes
        if ($should_deduct_stock) {
            foreach ($order_items_buffer as $item) {
                $products[$item['product_id']]['stock'] += $item['quantity'];
            }
        }
    }
}

$stmt_order->close();
$stmt_item->close();
$stmt_stock->close();

echo "-------------------------\n";
echo "Seeding Completed.\n";
echo "-------------------------\n";

// --- 4. Validation & Summary ---

// Total Users
$res = $conn->query("SELECT COUNT(*) as c FROM users");
$total_users = $res->fetch_assoc()['c'];

// Total Orders
$res = $conn->query("SELECT COUNT(*) as c FROM orders");
$total_orders_db = $res->fetch_assoc()['c'];

// Revenue
$res = $conn->query("SELECT SUM(total_amount) as r FROM orders WHERE payment_status = 'Paid'");
$revenue_db = $res->fetch_assoc()['r'];

// Stock Stats
$res = $conn->query("SELECT MIN(stock) as min_s, MAX(stock) as max_s FROM products");
$stock_stats = $res->fetch_assoc();

// Status Dist
$res = $conn->query("SELECT status, COUNT(*) as c FROM orders GROUP BY status");
$status_dist = [];
while ($r = $res->fetch_assoc()) {
    $status_dist[$r['status']] = $r['c'];
}

echo "Summary:\n";
echo "Total Users: $total_users\n";
echo "Orders Created (This Run): $orders_created\n";
echo "Total Orders in DB: $total_orders_db\n";
echo "Total Revenue (Paid): $" . number_format($revenue_db, 2) . "\n";
echo "Remaining Stock: Min {$stock_stats['min_s']} | Max {$stock_stats['max_s']}\n";
echo "Status Distribution:\n";
foreach ($status_dist as $s => $c) {
    echo "  - $s: $c\n";
}

echo "\n-------------------------\n";
echo "Validation Checks:\n";

// Check 1: Negative Stock
$res = $conn->query("SELECT id, name, stock FROM products WHERE stock < 0");
if ($res->num_rows > 0) {
    echo "[FAIL] Negative stock found!\n";
    while ($r = $res->fetch_assoc()) {
        echo "  Product {$r['id']} ({$r['name']}): {$r['stock']}\n";
    }
} else {
    echo "[PASS] No negative stock found.\n";
}

// Check 2: Order Total Mismatch
$sql_check_totals = "
    SELECT o.id, o.total_amount, SUM(oi.price_at_purchase * oi.quantity) as calc_total
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    HAVING ABS(o.total_amount - calc_total) > 0.01
";
$res = $conn->query($sql_check_totals);
if ($res->num_rows > 0) {
    echo "[FAIL] Order total mismatches found: " . $res->num_rows . "\n";
} else {
    echo "[PASS] All order totals match items.\n";
}

echo "Done.\n";
?>
