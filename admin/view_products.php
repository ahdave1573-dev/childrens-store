<?php
require_once __DIR__ . '/../middleware/require_admin.php';

require_once __DIR__ . '/../config/database.php';

// Handle Actions
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: view_products.php");
    exit;
}

if (isset($_GET['toggle_active'])) {
    $id = intval($_GET['toggle_active']);
    $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_active FROM products WHERE id=$id"));
    $new_status = $current['is_active'] ? 0 : 1;
    mysqli_query($conn, "UPDATE products SET is_active=$new_status WHERE id=$id");
    header("Location: view_products.php");
    exit;
}

$q = mysqli_query($conn,"
SELECT p.*, c.name AS cat_name,
       (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS primary_image
FROM products p 
JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Products | Admin</title>
<link rel="stylesheet" href="assets/dashboard.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'view_products'; include 'sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>All Products</h1>
        <span><?php echo date("d M Y"); ?></span>
    </div>

    <div class="panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; border-bottom:1px solid var(--border); padding-bottom:1rem;">
            <h3>Product List</h3>
            <a href="add_product.php" class="btn btn-primary" style="background:var(--primary); color:white; padding:0.5rem 1rem; border-radius:var(--radius-md); text-decoration:none;">
                <ion-icon name="add-outline"></ion-icon> Add Product
            </a>
        </div>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Product</th>
                <th>Sale Price</th>
                <th>MRP</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while($row=mysqli_fetch_assoc($q)) { 
                if (!empty($row['primary_image'])) {
                    if (filter_var($row['primary_image'], FILTER_VALIDATE_URL)) {
                        $display_image = $row['primary_image'];
                    } else {
                        $display_image = '../' . $row['primary_image'];
                    }
                } else {
                    $display_image = "https://picsum.photos/seed/" . $row['id'] . "/400/400";
                }
            ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><img src="<?php echo $display_image; ?>" loading="lazy" style="width:50px; height:50px; object-fit:contain; background:#f8f8f8; border-radius:var(--radius-sm);"></td>
                <td><?php echo $row['name']; ?></td>
                <td>₹<?php echo $row['price']; ?></td>
                <td>
                    <?php if(!empty($row['mrp']) && $row['mrp'] > $row['price']): ?>
                        <div style="font-size:0.9rem; color:#888;">
                            <del>₹<?php echo $row['mrp']; ?></del>
                            <br>
                            <span style="color:#ef4444; font-weight:600;">
                                <?php echo round((($row['mrp'] - $row['price']) / $row['mrp']) * 100); ?>% OFF
                            </span>
                        </div>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row['stock'] >= 10){ ?>
                        <span class="stock in" style="color: #16a34a; font-weight: 600;"><?php echo $row['stock']; ?> Available</span>
                    <?php } elseif($row['stock'] > 0){ ?>
                        <span style="color: #ef4444; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                            <?php echo $row['stock']; ?> Available 
                            <span style="background: #ef4444; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 800; letter-spacing: 0.5px;">LOW</span>
                        </span>
                    <?php } else { ?>
                        <span style="background: #ef4444; color: white; font-weight: 800; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; display: inline-block; letter-spacing: 0.5px; border: 2px solid #b91c1c;">SOLD OUT</span>
                    <?php } ?>
                </td>
                <td><?php echo $row['cat_name']; ?></td>
                <td>
                    <?php if($row['is_active']): ?>
                        <span style="color:green; font-weight:bold;">Active</span>
                    <?php else: ?>
                        <span style="color:red; font-weight:bold;">Inactive</span>
                    <?php endif; ?>
                </td>
                <td style="white-space: nowrap;">
                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" style="color:var(--primary); font-weight:600; display:inline-flex; align-items:center; gap:4px; margin-right:12px; vertical-align: middle;">
                        <ion-icon name="create-outline" style="font-size: 1.2rem;"></ion-icon> Edit
                    </a>
                    <a href="view_products.php?toggle_active=<?php echo $row['id']; ?>" style="color:orange; font-weight:600; display:inline-flex; align-items:center; gap:4px; margin-right:12px; vertical-align: middle;">
                        <ion-icon name="power-outline" style="font-size: 1.2rem;"></ion-icon> <?php echo $row['is_active'] ? 'Disable' : 'Enable'; ?>
                    </a>
                    <a href="view_products.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?')" style="color:red; font-weight:600; display:inline-flex; align-items:center; vertical-align: middle;" title="Delete">
                        <ion-icon name="trash-outline" style="font-size: 1.2rem;"></ion-icon>
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</div>

</body>
</html>


