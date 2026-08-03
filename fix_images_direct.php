<?php
$conn = new mysqli("localhost", "root", "", "childrens_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Clear existing products to avoid duplicates during re-insertion
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE products");
$conn->query("TRUNCATE TABLE categories");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// 2. Insert Categories with URLs
$categories = [
    1 => ["name" => "Educational Toys", "image" => "https://images.unsplash.com/photo-1587654780291-7719f42b7bd3?auto=format&fit=crop&q=80&w=600"],
    2 => ["name" => "Boys Fashion", "image" => "https://images.unsplash.com/photo-1519457431-44ccd64a579b?auto=format&fit=crop&q=80&w=600"],
    3 => ["name" => "Girls Fashion", "image" => "https://images.unsplash.com/photo-1514090458221-65bb69cf63e6?auto=format&fit=crop&q=80&w=600"],
    4 => ["name" => "Baby Care", "image" => "https://images.unsplash.com/photo-1555252333-9f8e92e65df9?auto=format&fit=crop&q=80&w=600"],
    5 => ["name" => "Footwear", "image" => "https://images.unsplash.com/photo-1511556532299-8f662fc26d06?auto=format&fit=crop&q=80&w=600"],
    6 => ["name" => "School Supplies", "image" => "https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&q=80&w=600"],
    7 => ["name" => "Party Supplies", "image" => "https://images.unsplash.com/photo-1530103862676-de3c9a59af38?auto=format&fit=crop&q=80&w=600"],
    8 => ["name" => "Bedding & Decor", "image" => "https://images.unsplash.com/photo-1513297887112-cafd9dcd483d?auto=format&fit=crop&q=80&w=600"],
    9 => ["name" => "Feeding & Nursing", "image" => "https://images.unsplash.com/photo-1567104523-86318ed81e3a?auto=format&fit=crop&q=80&w=600"],
    10 => ["name" => "Outdoor Fun", "image" => "https://images.unsplash.com/photo-1596464716127-f9a829be7ca5?auto=format&fit=crop&q=80&w=600"]
];

foreach ($categories as $id => $cat) {
    $stmt = $conn->prepare("INSERT INTO categories (id, name, image) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id, $cat['name'], $cat['image']);
    $stmt->execute();
}

// 3. Insert Products with Unsplash URLs
$products = [
    // Educational Toys
    ["cat" => 1, "name" => "Alphabet Blocks", "image" => "https://images.unsplash.com/photo-1587654780291-7719f42b7bd3?auto=format&fit=crop&q=80&w=600"],
    ["cat" => 1, "name" => "Science Kit", "image" => "https://images.unsplash.com/photo-1566576912327-8a7246437534?auto=format&fit=crop&q=80&w=600"],
    ["cat" => 1, "name" => "Math Puzzle", "image" => "https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?auto=format&fit=crop&q=80&w=600"],
    ["cat" => 1, "name" => "Solar System Model", "image" => "https://images.unsplash.com/photo-1614728263952-84ea256f9679?auto=format&fit=crop&q=80&w=600"], // Better solar system
    ["cat" => 1, "name" => "Building Bricks", "image" => "https://images.unsplash.com/photo-1585366119957-e9730b6d0f60?auto=format&fit=crop&q=80&w=600"],

    // Boys Fashion
    ["cat" => 2, "name" => "Denim Jeans", "image" => "https://images.unsplash.com/photo-1519457431-44ccd64a579b?auto=format&fit=crop&q=80&w=600"],
    ["cat" => 2, "name" => "Graphic T-Shirt", "image" => "https://images.unsplash.com/photo-1519238263596-0643681f193f?auto=format&fit=crop&q=80&w=600"],
    ["cat" => 2, "name" => "Hoodie", "image" => "https://images.unsplash.com/photo-1471286174890-9c808743015a?auto=format&fit=crop&q=80&w=600"],
    ["cat" => 2, "name" => "Sneakers", "image" => "https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?auto=format&fit=crop&q=80&w=600"],

    // Girls Fashion
    ["cat" => 3, "name" => "Floral Dress", "image" => "https://images.unsplash.com/photo-1514090458221-65bb69cf63e6?auto=format&fit=crop&q=80&w=600"],
    ["cat" => 3, "name" => "Leggings", "image" => "https://images.unsplash.com/photo-1621451973529-c6e12052dfb8?auto=format&fit=crop&q=80&w=600"],
    ["cat" => 3, "name" => "Princess Gown", "image" => "https://images.unsplash.com/photo-1524749292158-756095483167?auto=format&fit=crop&q=80&w=600"]
];

$stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($products as $p) {
    $desc = "High quality " . $p['name'] . " for your kids. Safe and durable.";
    $price = rand(499, 2999);
    $stock = 50;
    $stmt->bind_param("issisi", $p['cat'], $p['name'], $desc, $price, $p['image'], $stock);
    $stmt->execute();
}

$conn->close();

echo "<html><body style='font-family:sans-serif; text-align:center; padding:50px;'>";
echo "<h1 style='color:green;'>Images Fixed Successfully! ✅</h1>";
echo "<p>All products have been updated with Unsplash URLs.</p>";
echo "<a href='index.php' style='display:inline-block; padding:15px 30px; background:#6c5ce7; color:white; text-decoration:none; border-radius:5px; margin-top:20px;'>Return to Store</a>";
echo "</body></html>";
?>
