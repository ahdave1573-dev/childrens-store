<?php
require_once __DIR__ . '/../middleware/require_admin.php';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'];
$cat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM categories WHERE id = $id"));

if (isset($_POST['update_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = strtolower(str_replace(' ', '-', $name));

    $image_path = mysqli_real_escape_string($conn, $_POST['cat_image_url']);
    if (isset($_FILES['cat_image_file']) && $_FILES['cat_image_file']['error'] == 0) {
        $ext = pathinfo($_FILES['cat_image_file']['name'], PATHINFO_EXTENSION);
        $filename = 'cat_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['cat_image_file']['tmp_name'], '../image/' . $filename)) {
            $image_path = 'image/' . $filename;
        }
    }

    mysqli_query($conn, "UPDATE categories SET name='$name', image='$image_path', slug='$slug' WHERE id=$id");
    header("Location: view_categories.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Category | Admin</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<?php $page = 'view_categories'; include 'sidebar.php'; ?>

<div class="main">
    <div class="topbar">
        <h1>Edit Category</h1>
        <a href="view_categories.php" class="back-link"><ion-icon name="arrow-back-outline"></ion-icon> Back</a>
    </div>

    <div class="panel">
        <form method="post" enctype="multipart/form-data">
            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Category Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($cat['name']); ?>" required style="width:100%; padding:0.8rem; margin-bottom:1.5rem; border:1px solid #ddd; border-radius:var(--radius-md);">

            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Image URL</label>
            <input type="text" name="cat_image_url" value="<?php echo htmlspecialchars($cat['image']); ?>" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:var(--radius-md);">

            <div style="text-align:center; margin:1.5rem 0; color:#aaa; font-size:0.9rem; position:relative;">
                <span style="background:white; padding:0 10px; position:relative; z-index:2;">OR</span>
                <div style="position:absolute; top:50%; left:0; width:100%; height:1px; background:#eee; z-index:1;"></div>
            </div>

            <label style="display:block; margin-bottom:0.5rem; font-weight:500;">Manual Image Upload</label>
            <input type="file" name="cat_image_file" accept="image/*" style="width:100%; padding:0.8rem; margin-bottom:1.5rem; border:1px solid #ddd; border-radius:var(--radius-md); background:#fcfcfc;">

            <div style="display:flex; justify-content:center; align-items:center; gap:20px; margin-top:30px;">
                <a href="view_categories.php" style="color:#666; font-weight:500; text-decoration:none;">Cancel</a>
                <button type="submit" name="update_category" class="btn btn-primary" style="background:#4a90e2; color:white; padding:0.8rem 2.5rem; border:none; border-radius:var(--radius-md); cursor:pointer; font-weight:600; letter-spacing:1px; box-shadow:0 4px 15px rgba(74, 144, 226, 0.3);">Update Category</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>


