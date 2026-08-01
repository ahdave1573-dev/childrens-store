<?php
// product.php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// Fetch Product
$stmt = $conn->prepare("SELECT * FROM products WHERE slug = ? AND is_active = 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$prod = $stmt->get_result()->fetch_assoc();

if (!$prod) {
    $page_title = "Product Not Found | Childrens-Store";
    require_once 'includes/header.php';
    echo "<div class='container' style='padding:5rem 5%; text-align:center;'><h1>Product Not Found</h1><a href='index.php' class='btn btn-primary' style='margin-top:20px;'>Return to Store</a></div>";
    require_once 'includes/footer.php';
    exit;
}

// Dynamic SEO
$page_title = $prod['name'] . " | Childrens-Store";
$page_desc = substr(strip_tags($prod['description']), 0, 150) . "... Buy online at Children's Store.";
$page_keywords = $prod['name'] . ", kids clothing, toys, baby care";

require_once 'includes/header.php';

// Fetch Images
$img_stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
$img_stmt->bind_param("i", $prod['id']);
$img_stmt->execute();
$all_images = $img_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (!empty($all_images)) {
    $main_url = $all_images[0]['image_url'];
} else {
    $main_url = 'https://picsum.photos/seed/' . $prod['id'] . '/600/600';
}

function resolveImageUrl($path) {
    if (empty($path)) return '';
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }
    // If it doesn't already start with image/ or ../
    if (strpos($path, 'image/') !== 0 && strpos($path, '../') !== 0) {
        return 'image/' . basename($path);
    }
    return $path;
}

$main_image_src = resolveImageUrl($main_url);

$discount = 0;
$has_discount = false;
if (!empty($prod['mrp']) && $prod['mrp'] > $prod['price']) {
    $discount = round((($prod['mrp'] - $prod['price']) / $prod['mrp']) * 100);
    $has_discount = true;
}

// Determine Category for Sizes
$cat_sql = "SELECT slug FROM categories WHERE id = " . $prod['category_id'];
$cat_slug = $conn->query($cat_sql)->fetch_assoc()['slug'];
$is_clothing = strpos($cat_slug, 'clothing') !== false || strpos($cat_slug, 'footwear') !== false;
?>

<div class="product-container">
    <!-- Left: Gallery -->
    <div class="gallery">
        <div class="gallery-main">
            <img id="mainImg" src="<?php echo htmlspecialchars($main_image_src); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>">
        </div>
        
        <?php if (count($all_images) > 1): ?>
        <div class="gallery-thumbs">
            <?php foreach ($all_images as $key => $img): 
                 $thumb_src = resolveImageUrl($img['image_url']);
            ?>
                <img class="thumb <?php echo $key === 0 ? 'active' : ''; ?>" 
                     src="<?php echo htmlspecialchars($thumb_src); ?>" 
                     onclick="changeImage(this.src, this)">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right: Details -->
    <div class="details">
        <div class="product-cat"><?php echo strtoupper(str_replace('-', ' ', $cat_slug)); ?></div>
        <h1 class="details-title"><?php echo htmlspecialchars($prod['name']); ?></h1>
        <div class="details-price">
            ₹<?php echo number_format($prod['price']); ?>
            <?php if ($has_discount): ?>
                <span class="product-mrp" style="color:#888; margin-left:12px; font-size:0.8em;"><del>₹<?php echo number_format($prod['mrp']); ?></del></span>
                <span class="product-off" style="color:#ef4444; font-weight:bold; font-size:0.7em; margin-left:6px;">(<?php echo $discount; ?>% OFF)</span>
            <?php endif; ?>
        </div>
        <p class="details-desc"><?php echo nl2br(htmlspecialchars($prod['description'])); ?></p>

        <form action="cart_action.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
            <input type="hidden" name="action" value="add">
            
            <?php if ($is_clothing): ?>
                <div class="size-selector">
                    <p><strong>Select Size:</strong></p>
                    <div id="size-options">
                        <?php 
                        $sizes = strpos($cat_slug, 'footwear') !== false ? ['6', '7', '8', '9', '10'] : ['S', 'M', 'L', 'XL'];
                        foreach ($sizes as $size): ?>
                            <input type="radio" name="size" value="<?php echo $size; ?>" id="size-<?php echo $size; ?>" required style="display:none">
                            <label for="size-<?php echo $size; ?>" class="size-btn" onclick="selectSize(this)"><?php echo $size; ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="size" value="One Size">
            <?php endif; ?>

            <div class="action-buttons">
                <?php if ($prod['stock'] > 0): ?>
                    <button type="submit" class="btn-buy"><i class="fas fa-shopping-bag"></i> Add to Cart</button>
                    <!-- <button type="button" class="btn-cart"><i class="far fa-heart"></i> Wishlist</button> -->
                <?php else: ?>
                    <button type="button" class="btn-buy" style="background:gray; cursor:not-allowed;" disabled>Out of Stock</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
function changeImage(src, el) {
    let main = document.getElementById('mainImg');
    main.style.transition = "opacity 0.2s ease-in-out";
    main.style.opacity = 0;
    setTimeout(() => {
        main.src = src;
        main.style.opacity = 1;
    }, 200);
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

function selectSize(el) {
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.classList.remove('selected');
        btn.style.background = 'white';
        btn.style.color = 'var(--dark)';
    });
    el.classList.add('selected');
    el.style.background = 'var(--dark)';
    el.style.color = 'white';
}
</script>

<?php require_once 'includes/footer.php'; ?>
