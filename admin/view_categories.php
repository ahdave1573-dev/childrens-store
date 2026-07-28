<?php
require_once __DIR__ . '/../middleware/require_admin.php';
date_default_timezone_set("Asia/Kolkata");

require_once __DIR__ . '/../config/database.php';

$msg = "";

/* DELETE CATEGORY WITH SAFETY CHECK */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Check if products exist in this category
    $check = mysqli_query($conn, "SELECT id FROM products WHERE category_id = $id");

    if (mysqli_num_rows($check) > 0) {
        $msg = "Cannot delete category. Please remove or re-assign the products under this category first.";
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
        $msg = "Category deleted successfully.";
    }
}

if (isset($_GET['toggle_active'])) {
    $id = (int)$_GET['toggle_active'];
    $current = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_active FROM categories WHERE id=$id"));
    $new_status = $current['is_active'] ? 0 : 1;
    mysqli_query($conn, "UPDATE categories SET is_active=$new_status WHERE id=$id");
    header("Location: view_categories.php");
    exit;
}

/* FETCH CATEGORIES */
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Categories | Childrens-Store</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'view_categories'; include 'sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Category Management</h1>
        <span><?php echo date("d M Y"); ?></span>
    </div>

    <!-- ERROR/SUCCESS MSG -->
    <?php if ($msg != "") { ?>
        <div class="box" style="background: <?php echo (strpos($msg,'successfully')!==false) ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo (strpos($msg,'successfully')!==false) ? '#166534' : '#991b1b'; ?>; margin-bottom: 2rem;">
            <?php echo $msg; ?>
        </div>
    <?php } ?>

    <!-- CATEGORY LIST -->
    <div class="panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid var(--border);">
            <h3 style="margin:0; border:none; padding:0;">All Categories</h3>
            <a href="add_category.php" class="btn btn-primary" style="background:var(--primary); color:white; padding:0.5rem 1rem; border-radius:var(--radius-md); text-decoration:none;">
                <ion-icon name="add-outline"></ion-icon> Add New
            </a>
        </div>

        <?php if (mysqli_num_rows($categories) == 0) { ?>
            <div style="text-align:center; padding:2rem; color:var(--text-muted);">
                <b>No categories found.</b>
            </div>
        <?php } else { ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Action</th>
                </tr>
                <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo $c['name']; ?></td>
                    <td style="white-space: nowrap;">
                        <a href="edit_category.php?id=<?php echo $c['id']; ?>" style="color:var(--primary); display:inline-flex; align-items:center; gap:4px; margin-right:12px; font-weight:600; vertical-align: middle;">
                           <ion-icon name="create-outline" style="font-size: 1.2rem;"></ion-icon> Edit
                        </a>
                        <a href="view_categories.php?toggle_active=<?php echo $c['id']; ?>" style="color:orange; display:inline-flex; align-items:center; gap:4px; margin-right:12px; font-weight:600; vertical-align: middle;">
                           <ion-icon name="power-outline" style="font-size: 1.2rem;"></ion-icon> <?php echo $c['is_active'] ? 'Disable' : 'Enable'; ?>
                        </a>
                        <a href="view_categories.php?delete=<?php echo $c['id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this category?')"
                           style="color:red; display:inline-flex; align-items:center; vertical-align: middle;" title="Delete">
                           <ion-icon name="trash-outline" style="font-size: 1.2rem;"></ion-icon>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

</div>

</body>
</html>


