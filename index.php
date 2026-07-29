<?php
// index.php
$page_title = "Childrens-Store | Playful Kids Shop";
$page_desc = "Discover the best kids clothing, educational toys, baby care products, school supplies, and baby bedding at Children's Store. Playful, premium, and safe for your little ones.";
$page_keywords = "kids shop, children store, baby clothing, toys for kids, educational toys, school supplies, baby care";
require_once 'includes/header.php';
require_once __DIR__ . '/config/database.php';

// Fetch Categories
$cat_res = $conn->query("SELECT * FROM categories WHERE is_active = 1");

// Fetch Featured Products & Handlers
$cat_filter = "";
$cat_param = "";
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_slug = $conn->real_escape_string($_GET['category']);
    $cat_filter = " AND c.slug = '$category_slug'";
    $cat_param = "&category=" . urlencode($category_slug);
}

$search_filter = "";
$search_param = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_keyword = $conn->real_escape_string(trim($_GET['search']));
    $search_filter = " AND (p.name LIKE '%$search_keyword%' OR p.description LIKE '%$search_keyword%')";
    $search_param = "&search=" . urlencode($search_keyword);
}

// Pagination Logic
$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count Bounds
$count_res = $conn->query("
    SELECT COUNT(*) as total
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.is_active = 1 AND p.stock > 0 $cat_filter $search_filter
");
$total_rows = $count_res->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$prod_res = $conn->query("
    SELECT p.*, c.name AS cat_name,
           (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS primary_image
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.is_active = 1 AND p.stock > 0 $cat_filter $search_filter 
    ORDER BY p.updated_at DESC 
    LIMIT $limit OFFSET $offset
");
?>

<!-- Hero Section -->
<header class="hero">
    <div class="hero-content">
        <h1>Welcome to <span>Childrens</span>-Store</h1>
        <p>Discover the most adorable fashion and toys for your little ones!</p>
        <a href="#featured" class="btn-buy" style="margin-top: 1rem; display: inline-block;">Shop Now</a>
    </div>
</header>

<!-- Categories -->
<section id="categories">
    <h2 class="section-title">Shop by Category</h2>
    <div class="categories-grid">
        <?php while ($cat = $cat_res->fetch_assoc()): 
            $cat_img_db = $cat['image'];
            if (!empty($cat_img_db) && file_exists($cat_img_db)) {
                $cat_image = $cat_img_db;
            } else if (!empty($cat_img_db) && file_exists('image/' . $cat_img_db)) {
                $cat_image = 'image/' . $cat_img_db;
            } else if (!empty($cat_img_db) && filter_var($cat_img_db, FILTER_VALIDATE_URL)) {
                $cat_image = $cat_img_db;
            } else {
                $cat_image = "https://picsum.photos/seed/cat" . $cat['id'] . "/600/600";
            }
        ?>
            <div class="cat-card" onclick="window.location.href='index.php?category=<?php echo $cat['slug']; ?>#products'">
                <img src="<?php echo $cat_image; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" loading="lazy">
                <div class="cat-overlay">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Featured Products -->
<section id="featured" style="padding: 80px 0;">
<div class="container">
    <h2 class="section-title">New Arrivals</h2>
    <p class="section-subtitle">Carefully curated comfort and style for your happy children</p>
    <div class="products-grid">
        <?php while ($prod = $prod_res->fetch_assoc()): ?>
            <?php
            $display_image = (!empty($prod['primary_image'])) 
                ? $prod['primary_image'] 
                : "https://picsum.photos/seed/" . $prod['id'] . "/400/400";

            // Calculate Discount
            $discount = 0;
            $has_discount = false;
            if (!empty($prod['mrp']) && $prod['mrp'] > $prod['price']) {
                $discount = round((($prod['mrp'] - $prod['price']) / $prod['mrp']) * 100);
                $has_discount = true;
            }
            $badge = $has_discount ? "{$discount}% OFF" : 'New';
            
            // Alternating kid-friendly background colors for badges
            $badge_bg = $has_discount ? 'var(--kids-pink)' : 'var(--kids-blue)';
            if ($prod['id'] % 3 == 0) {
                $badge_bg = 'var(--kids-green)';
            } else if ($prod['id'] % 5 == 0) {
                $badge_bg = 'var(--kids-orange)';
            }
            ?>
            <div class="product-card" onclick="window.location.href='product.php?slug=<?php echo $prod['slug']; ?>'">
                <div class="product-img">
                    <div class="badge" style="background:<?php echo $badge_bg; ?>;"><?php echo $badge; ?></div>
                    <img src="<?php echo $display_image; ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" loading="lazy">
                </div>
                <div class="product-info">
                    <div class="product-cat"><?php echo htmlspecialchars($prod['cat_name']); ?></div>
                    <h3 class="product-title"><?php echo htmlspecialchars($prod['name']); ?></h3>
                    <div class="product-price">
                        ₹<?php echo number_format($prod['price']); ?>
                        <?php if ($has_discount): ?>
                            <span class="product-mrp"><del>₹<?php echo number_format($prod['mrp']); ?></del></span>
                            <span class="product-off">(<?php echo $discount; ?>% OFF)</span>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="cart_action.php?action=add&id=<?php echo $prod['id']; ?>" class="btn-add" onclick="event.stopPropagation();" style="text-decoration:none;"><i class="fas fa-shopping-basket"></i> Add to Cart</a>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination UI -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination" style="display:flex; justify-content:center; align-items:center; gap:10px; margin-top:60px;">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?><?php echo $cat_param; ?><?php echo $search_param; ?>#featured" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
</section>

<?php require_once 'includes/footer.php'; ?>
