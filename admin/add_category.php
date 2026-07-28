<?php
require_once __DIR__ . '/../middleware/require_admin.php';
date_default_timezone_set("Asia/Kolkata");

require_once __DIR__ . '/../config/database.php';

// Add category logic
$msg = "";
if (isset($_POST['add'])) {
    $category = trim($_POST['category']);
    $slug = strtolower(str_replace(' ', '-', $category));

    $image_path = "";
    if (isset($_FILES['cat_image_file']) && $_FILES['cat_image_file']['error'] == 0) {
        $ext = pathinfo($_FILES['cat_image_file']['name'], PATHINFO_EXTENSION);
        $filename = 'cat_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['cat_image_file']['tmp_name'], '../image/' . $filename)) {
            $image_path = 'image/' . $filename;
        }
    } else if (!empty(trim($_POST['cat_image_url']))) {
        $image_path = trim($_POST['cat_image_url']);
    }

    if ($category == "") {
        $msg = "Category name cannot be empty";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM categories WHERE name='$category'");
        if (mysqli_num_rows($check) > 0) {
            $msg = "Category already exists";
        } else {
            mysqli_query($conn, "INSERT INTO categories (name, slug, image, is_active) VALUES ('$category', '$slug', '$image_path', 1)");
            $msg = "Category added successfully";
        }
    }
}

// Fetch categories
$result = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Category | Childrens-Store</title>
    <link rel="stylesheet" href="assets/dashboard.css?v=<?php echo time(); ?>">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'add_category'; include 'sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Category Management</h1>
        <span><?php echo date("d M Y"); ?></span>
    </div>

    <!-- ADD CATEGORY -->
    <div class="box">
        <h3>Add New Category</h3>
        <form method="post" enctype="multipart/form-data">
            <label style="margin-bottom:0.5rem; display:block; font-weight:500;">Category Name</label>
            <input type="text" name="category" placeholder="Enter category name" style="width:100%; padding:0.8rem; margin-bottom:1.5rem; border:1px solid #ddd; border-radius:var(--radius-md);">

            <label style="margin-bottom:0.5rem; display:block; font-weight:500;">Image URL</label>
            <input type="text" name="cat_image_url" placeholder="https://..." style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:var(--radius-md);">

            <div style="text-align:center; margin:1.5rem 0; color:#aaa; font-size:0.9rem; position:relative;">
                <span style="background:white; padding:0 10px; position:relative; z-index:2;">OR</span>
                <div style="position:absolute; top:50%; left:0; width:100%; height:1px; background:#eee; z-index:1;"></div>
            </div>

            <label style="margin-bottom:0.5rem; display:block; font-weight:500;">Manual Image Upload</label>
            <input type="file" name="cat_image_file" accept="image/*" style="width:100%; padding:0.8rem; margin-bottom:1.5rem; border:1px solid #ddd; border-radius:var(--radius-md); background:#fcfcfc;">

            <div style="margin-top:30px;">
                <button type="submit" name="add" class="btn btn-primary" style="background:var(--primary); color:white; border:none; padding:0.8rem 1.5rem; border-radius:var(--radius-md); cursor:pointer; width:100%; font-weight:600; letter-spacing:1px;">Add Category</button>
            </div>
        </form>

        <?php if ($msg != "") { ?>
            <div style="margin-top:1rem; padding:0.75rem; background:#dcfce7; color:#16a34a; border-radius:var(--radius-md); font-weight:600;"><?php echo $msg; ?></div>
        <?php } ?>
    </div>

    <!-- CATEGORY LIST -->
    <div class="panel">
        <h3>Existing Categories</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Created Date</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo date("d-m-Y h:i A"); ?></td>
            </tr>
            <?php } ?>

        </table>
    </div>

</div>

</body>
</html>


