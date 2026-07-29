<?php
require_once __DIR__ . '/../middleware/require_admin.php';
date_default_timezone_set("Asia/Kolkata");

require_once __DIR__ . '/../config/database.php';

$msg = "";

if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $category = $_POST['category'];
    $short_desc = trim($_POST['short_description']);
    $desc = trim($_POST['description']);
    $original_price = !empty($_POST['original_price']) ? trim($_POST['original_price']) : 0;
    
    if ($name == "" || $price == "" || $category == "") {
        $msg = "All fields are required";
    } else {
        $base_slug = strtolower(str_replace(' ', '-', $name));
        $slug = $base_slug;
        $counter = 1;
        while (true) {
            $esc_slug = mysqli_real_escape_string($conn, $slug);
            $check_stmt = mysqli_query($conn, "SELECT id FROM products WHERE slug = '$esc_slug'");
            if (mysqli_num_rows($check_stmt) > 0) {
                $slug = $base_slug . '-' . $counter;
                $counter++;
            } else {
                break;
            }
        }

        $esc_name = mysqli_real_escape_string($conn, $name);
        $esc_slug = mysqli_real_escape_string($conn, $slug);
        $esc_short = mysqli_real_escape_string($conn, $short_desc);
        $esc_desc = mysqli_real_escape_string($conn, $desc);

        mysqli_query($conn, "INSERT INTO products (name, slug, mrp, price, category_id, short_description, description)
        VALUES ('$esc_name', '$esc_slug', '$original_price', '$price', '$category', '$esc_short', '$esc_desc')");
        
        $product_id = mysqli_insert_id($conn);

        // Multi Image Upload
        if (!empty($_FILES['product_images']['name'][0])) {
            foreach ($_FILES['product_images']['name'] as $key => $image_name) {
                $tmp = $_FILES['product_images']['tmp_name'][$key];
                if ($image_name != "") {
                    $img_name = $esc_slug . "-" . time() . "-" . $key . ".jpg";
                    move_uploaded_file($tmp, "../image/" . $img_name);
                    mysqli_query($conn, "INSERT INTO product_images (product_id, image_url) VALUES ($product_id, 'image/$img_name')");
                }
            }
        }

        $msg = "Product added successfully";
    }
}

$cats_sql = mysqli_query($conn, "SELECT * FROM categories");

// We removed the broken image display array at bottom per user request.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Product | Childrens-Store</title>
    <link rel="stylesheet" href="assets/dashboard.css?v=<?php echo time(); ?>">
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

<?php $page = 'add_product'; include 'sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Product Management</h1>
        <span><?php echo date("d M Y"); ?></span>
    </div>

    <!-- ADD PRODUCT -->
    <div class="box" style="max-width: 800px; margin: 2rem auto;">
        <h3 style="font-family:'Playfair Display', serif; margin-bottom:1.5rem; color:#1a1a1a;">Add New Product</h3>

        <form method="post" enctype="multipart/form-data">
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1.5rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Product Name</label>
                    <input type="text" name="name" placeholder="Enter product name" required>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Original Price (MRP) ₹</label>
                    <input type="number" name="original_price" placeholder="Original Price" required>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Sale Price (₹)</label>
                    <input type="number" name="price" placeholder="Sale Price" required>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Category</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <?php while ($c = mysqli_fetch_assoc($cats_sql)) { ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo htmlspecialchars($c['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Product Images</label>
                    <input type="file" name="product_images[]" multiple accept="image/*" required>
                </div>
            </div>
            
            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Short Description</label>
            <textarea name="short_description" class="form-control" required placeholder="Brief description"></textarea>

            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Full Description</label>
            <textarea name="description" class="form-control" required placeholder="Detailed description"></textarea>

            <button type="submit" name="add" class="btn btn-primary" style="width:auto; margin-top:1rem;">Add Product</button>
        </form>

        <?php if ($msg != "") { ?>
            <div style="margin-top:1rem; padding:0.75rem; background:#dcfce7; color:#16a34a; border-radius:4px; font-weight:600;"><?php echo $msg; ?></div>
        <?php } ?>
    </div>

</div>

</body>
</html>


