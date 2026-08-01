<?php
require_once __DIR__ . '/middleware/require_login.php';
$page_title = "My Account & Orders | Childrens-Store";
$page_desc = "View your order history, account details, and shipping address on your personal account dashboard.";
$page_keywords = "my account, order history, children store account";
require_once 'includes/header.php';
require_once __DIR__ . '/config/database.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch all orders of this user
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt_orders = $conn->prepare($orders_query);
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();

function resolveProfileProductImage($path) {
    if (empty($path)) {
        return 'https://picsum.photos/600/600';
    }
    if (strpos($path, 'http') === 0) {
        return $path;
    }
    if (strpos($path, 'image/') !== 0 && strpos($path, '../') !== 0) {
        return 'image/' . basename($path);
    }
    return $path;
}

$orders = [];
while ($order = $orders_result->fetch_assoc()) {
    // Fetch items for this order
    $items_query = "SELECT oi.quantity, oi.price_at_purchase, p.name, p.id AS product_id,
                    (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image 
                    FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = ?";
    $stmt_items = $conn->prepare($items_query);
    $stmt_items->bind_param("i", $order['id']);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();
    
    $order['items'] = [];
    while ($item = $items_result->fetch_assoc()) {
        $item['image'] = resolveProfileProductImage($item['image']);
        $order['items'][] = $item;
    }
    
    $orders[] = $order;
}
?>

<style>
.profile-dashboard-layout {
    display: flex;
    gap: 2.5rem;
    margin-top: 1rem;
}

.profile-sidebar {
    flex: 1;
    min-width: 320px;
    max-width: 380px;
}

.profile-main-content {
    flex: 2;
    min-width: 350px;
}

.profile-card {
    background: #fff;
    padding: 2.5rem 2rem;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(159, 107, 85, 0.05);
    border: 2px solid var(--soft-bg);
}

.profile-avatar-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 2rem;
    text-align: center;
}

.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--kids-blue) 0%, var(--kids-blue-dark) 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-family: 'Quicksand', sans-serif;
    font-weight: 700;
    margin-bottom: 1rem;
    box-shadow: 0 8px 20px rgba(84, 180, 235, 0.2);
}

.profile-name {
    font-family: 'Quicksand', sans-serif;
    font-size: 1.4rem;
    color: var(--dark);
    margin-bottom: 0.2rem;
}

.profile-email-badge {
    font-size: 0.85rem;
    color: var(--light-grey);
    background: var(--soft-bg);
    padding: 4px 12px;
    border-radius: 20px;
}

.profile-details-table {
    width: 100%;
    border-collapse: collapse;
}

.profile-details-table td {
    padding: 12px 10px;
    border-bottom: 1px solid var(--soft-bg);
}

.profile-details-table tr:last-child td {
    border-bottom: none;
}

.profile-details-table td.label {
    font-weight: 700;
    color: var(--kids-brown);
    font-family: 'Quicksand', sans-serif;
    width: 35%;
}

.profile-details-table td.value {
    color: var(--dark);
}

.order-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 2rem;
    background: var(--soft-bg);
    padding: 6px;
    border-radius: 40px;
    width: fit-content;
}

.tab-btn {
    padding: 10px 24px;
    border: none;
    background: transparent;
    border-radius: 30px;
    font-family: 'Quicksand', sans-serif;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    color: var(--light-grey);
}

.tab-btn:hover {
    color: var(--kids-blue);
}

.tab-btn.active {
    background: #fff;
    color: var(--kids-blue);
    box-shadow: 0 4px 15px rgba(159, 107, 85, 0.08);
}

.order-card {
    background: #fff;
    border-radius: 24px;
    border: 2px solid var(--soft-bg);
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 25px rgba(159, 107, 85, 0.03);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.order-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(159, 107, 85, 0.08);
    border-color: rgba(84, 180, 235, 0.3);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--soft-bg);
    padding-bottom: 12px;
    margin-bottom: 1.2rem;
    flex-wrap: wrap;
    gap: 10px;
}

.order-number {
    font-family: 'Quicksand', sans-serif;
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 8px;
}

.order-number i {
    color: var(--kids-blue);
}

.status-badge {
    padding: 6px 16px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 700;
    font-family: 'Quicksand', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.status-pending {
    background: #FFF3CD;
    color: #856404;
}

.status-paid {
    background: #E8F4FD;
    color: var(--kids-blue-dark);
}

.status-shipped {
    background: #FFE8E8;
    color: var(--kids-pink-dark);
}

.status-delivered {
    background: #E2F6EA;
    color: var(--kids-green-dark);
}

.status-cancelled {
    background: #F8D7DA;
    color: #721C24;
}

.order-items-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.order-item-row {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    padding: 8px 0;
}

.order-item-img {
    width: 65px;
    height: 65px;
    object-fit: cover;
    border-radius: 12px;
    background: var(--soft-bg);
    border: 2px solid var(--soft-bg);
}

.order-item-info {
    flex: 1;
}

.order-item-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 1rem;
    font-family: 'Quicksand', sans-serif;
}

.order-item-meta {
    font-size: 0.85rem;
    color: var(--light-grey);
    margin-top: 2px;
}

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid var(--soft-bg);
    padding-top: 12px;
    margin-top: 1.2rem;
    flex-wrap: wrap;
    gap: 10px;
}

.order-total {
    font-weight: 700;
    color: var(--dark);
    font-size: 1.1rem;
}

.order-date {
    font-size: 0.85rem;
    color: var(--light-grey);
    display: flex;
    align-items: center;
    gap: 6px;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 24px;
    border: 2px dashed var(--soft-bg);
}

.empty-state i {
    font-size: 3.5rem;
    color: var(--kids-pink);
    margin-bottom: 1.5rem;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

.empty-state h3 {
    font-family: 'Quicksand', sans-serif;
    font-size: 1.3rem;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--light-grey);
    margin-bottom: 1.5rem;
}

@media (max-width: 991px) {
    .profile-dashboard-layout {
        flex-direction: column;
    }
    .profile-sidebar {
        max-width: 100%;
    }
}
</style>

<div class="container" style="padding: 3rem 5%; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 2.5rem; color: var(--dark); font-family: 'Quicksand', sans-serif; font-size: 2.5rem; text-align: center; position: relative;">
        My Dashboard
        <span style="display: block; width: 60px; height: 5px; background: var(--kids-pink); margin: 10px auto 0; border-radius: 10px;"></span>
    </h1>
    
    <div class="profile-dashboard-layout">
        <!-- Sidebar profile details -->
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <span class="profile-email-badge"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                
                <table class="profile-details-table">
                    <tr>
                        <td class="label"><i class="fas fa-phone" style="margin-right: 6px; color: var(--kids-blue);"></i> Phone</td>
                        <td class="value"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label"><i class="fas fa-map-marker-alt" style="margin-right: 6px; color: var(--kids-pink);"></i> Address</td>
                        <td class="value">
                            <?php 
                                $address_parts = array_filter([
                                    $user['address_line'] ?? null, 
                                    $user['city'] ?? null, 
                                    $user['state'] ?? null, 
                                    $user['pincode'] ?? null
                                ]);
                                echo htmlspecialchars(!empty($address_parts) ? implode(', ', $address_parts) : ($user['address'] ?? 'N/A')); 
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Main profile details / orders list -->
        <div class="profile-main-content">
            <div class="order-tabs">
                <button class="tab-btn active" onclick="switchTab(event, 'all')">All Orders (<?php echo count($orders); ?>)</button>
                <button class="tab-btn" onclick="switchTab(event, 'pending')">Pending (<?php 
                    $pending_count = 0;
                    foreach ($orders as $o) {
                        if (in_array($o['status'], ['Pending', 'Paid', 'Shipped'])) $pending_count++;
                    }
                    echo $pending_count;
                ?>)</button>
                <button class="tab-btn" onclick="switchTab(event, 'delivered')">Delivered (<?php 
                    $delivered_count = 0;
                    foreach ($orders as $o) {
                        if ($o['status'] === 'Delivered') $delivered_count++;
                    }
                    echo $delivered_count;
                ?>)</button>
            </div>
            
            <div class="orders-container">
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>No orders placed yet!</h3>
                        <p>Explore our cute kids collection and place your first order today.</p>
                        <a href="index.php" class="btn btn-primary" style="padding: 10px 24px; font-size: 0.9rem;">Shop Now</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card" data-status="<?php echo htmlspecialchars($order['status']); ?>">
                            <div class="order-header">
                                <div class="order-number">
                                    <i class="fas fa-shopping-bag"></i> 
                                    Order Number: <?php echo htmlspecialchars($order['order_number'] ?? '#ORD-'.$order['id']); ?>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </div>
                            
                            <div class="order-items-list">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="order-item-row">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="order-item-img">
                                        <div class="order-item-info">
                                            <div class="order-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <div class="order-item-meta">Qty: <?php echo $item['quantity']; ?> x ₹<?php echo number_format($item['price_at_purchase'], 2); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="order-footer">
                                <div class="order-date">
                                    <i class="far fa-calendar-alt"></i> Ordered on: <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?>
                                </div>
                                <div class="order-total">
                                    Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Dynamic empty state template for empty tabs -->
                    <div id="tab-empty-state" class="empty-state" style="display: none;">
                        <i class="fas fa-box-open"></i>
                        <h3 id="empty-state-title">No orders found!</h3>
                        <p id="empty-state-desc">There are no orders matching this status filter.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(event, tabName) {
    // Update active tab button style
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.currentTarget.classList.add('active');

    let visibleCount = 0;
    const cards = document.querySelectorAll('.order-card');
    
    cards.forEach(card => {
        const status = card.getAttribute('data-status');
        let show = false;
        
        if (tabName === 'all') {
            show = true;
        } else if (tabName === 'pending') {
            // Show Pending, Paid, Shipped
            if (status === 'Pending' || status === 'Paid' || status === 'Shipped') {
                show = true;
            }
        } else if (tabName === 'delivered') {
            if (status === 'Delivered') {
                show = true;
            }
        }
        
        if (show) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Toggle empty state if no visible cards in tab
    const emptyState = document.getElementById('tab-empty-state');
    if (emptyState) {
        if (visibleCount === 0 && cards.length > 0) {
            emptyState.style.display = 'block';
            if (tabName === 'pending') {
                document.getElementById('empty-state-title').innerText = "No pending orders!";
                document.getElementById('empty-state-desc').innerText = "You do not have any pending or in-progress orders.";
            } else if (tabName === 'delivered') {
                document.getElementById('empty-state-title').innerText = "No delivered orders!";
                document.getElementById('empty-state-desc').innerText = "None of your orders have been delivered yet.";
            }
        } else {
            emptyState.style.display = 'none';
        }
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

