<?php
require_once __DIR__ . '/../middleware/require_admin.php';
require_once __DIR__ . '/../config/database.php';

$id = intval($_GET['id']);
$p = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM products WHERE id=$id"));
$cats = mysqli_query($conn,"SELECT * FROM categories");

if(isset($_POST['save'])){
    $raw_name = trim($_POST['name']);
    $name = mysqli_real_escape_string($conn, $raw_name);
    
    $base_slug = strtolower(str_replace(' ', '-', $raw_name));
    $slug = $base_slug;
    $counter = 1;
    while (true) {
        $esc_slug = mysqli_real_escape_string($conn, $slug);
        $check_stmt = mysqli_query($conn, "SELECT id FROM products WHERE slug = '$esc_slug' AND id != $id");
        if (mysqli_num_rows($check_stmt) > 0) {
            $slug = $base_slug . '-' . $counter;
            $counter++;
        } else {
            break;
        }
    }
    
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $cat = $_POST['category'];
    $short_desc = mysqli_real_escape_string($conn, trim($_POST['short_description']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['description']));

    $original_price = !empty($_POST['original_price']) ? $_POST['original_price'] : 0;

    $esc_slug = mysqli_real_escape_string($conn, $slug);

    mysqli_query($conn,"
    UPDATE products SET
    name='$name',
    slug='$esc_slug',
    mrp='$original_price',
    price='$price',
    stock='$stock',
    category_id='$cat',
    short_description='$short_desc',
    description='$desc'
    WHERE id=$id
    ");

    if (!empty($_FILES['product_images']['name'][0])) {
        foreach ($_FILES['product_images']['name'] as $key => $image_name) {
            $tmp = $_FILES['product_images']['tmp_name'][$key];
            if ($image_name != "") {
                $img_name = $slug . "-" . time() . "-" . $key . ".jpg";
                move_uploaded_file($tmp, "../image/" . $img_name);
                mysqli_query($conn, "INSERT INTO product_images (product_id, image_url) VALUES ($id, 'image/$img_name')");
            }
        }
    }

    header("Location: view_products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Edit Product</title>
<link rel="stylesheet" href="assets/dashboard.css">
<style>
/* Boutique Level 5 Admin UI */
body { background: #f8f9fa; }
.box {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px);
    border: 1px solid #eee;
    box-shadow: none;
    border-radius: 8px;
    padding: 2rem;
}
input, select, textarea {
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 0.8rem;
    width: 100%;
    font-family: 'Inter', sans-serif;
    margin-bottom: 1rem;
    background: #fff;
}
textarea {
    min-height: 150px;
    resize: vertical;
}
.btn-primary {
    background: #4a90e2 !important;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    background: #357abd !important;
}
</style>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'view_products'; include 'sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Edit Product</h1>
        <a href="view_products.php" class="back-link">
            <ion-icon name="arrow-back-outline"></ion-icon> Back to Products
        </a>
    </div>

    <div class="box" style="max-width:800px; margin:2rem auto;">
        <h3 style="font-family:'Playfair Display', serif; margin-bottom:1.5rem; color:#1a1a1a;">Update Product Details</h3>

        <form method="post" enctype="multipart/form-data">
            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Product Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($p['name'] ?? ''); ?>" required>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1.5rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Original Price (MRP) ₹</label>
                    <input type="number" name="original_price" value="<?php echo htmlspecialchars($p['mrp'] ?? 0); ?>" required>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Sale Price (₹)</label>
                    <input type="number" name="price" value="<?php echo htmlspecialchars($p['price'] ?? 0); ?>" required>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Stock</label>
                    <input type="number" name="stock" value="<?php echo htmlspecialchars($p['stock'] ?? 0); ?>" required>
                </div>
            </div>

            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Category</label>
            <select name="category" required>
                <?php while($c=mysqli_fetch_assoc($cats)){ ?>
                <option value="<?php echo $c['id']; ?>" <?php if($c['id']==$p['category_id']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($c['name']); ?>
                </option>
                <?php } ?>
            </select>
            
            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Short Description</label>
            <textarea name="short_description" class="form-control" required><?php echo htmlspecialchars($p['short_description'] ?? ''); ?></textarea>

            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Full Description</label>
            <textarea name="description" class="form-control" required><?php echo htmlspecialchars($p['description'] ?? ''); ?></textarea>

            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Add More Images (optional)</label>
            <input type="file" name="product_images[]" multiple accept="image/*">

            <button name="save" class="btn btn-primary" style="width:100%; margin-top:1rem;">Update Product</button>
        </form>

    </div>

</div>

</body>
</html>


