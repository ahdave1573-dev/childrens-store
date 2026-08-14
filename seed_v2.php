<?php
// seed_v2.php
require_once __DIR__ . '/config/database.php';

echo "Seeding Database...<br>";

// Truncate tables
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE `product_images`");
$conn->query("TRUNCATE TABLE `products`");
$conn->query("TRUNCATE TABLE `categories`");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Categories
$categories = [
    ['name' => 'Boys Clothing', 'slug' => 'boys-clothing', 'image' => 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?w=800'],
    ['name' => 'Girls Clothing', 'slug' => 'girls-clothing', 'image' => 'https://images.unsplash.com/photo-1621452773781-0f992ee61e67?w=800'],
    ['name' => 'Educational Toys', 'slug' => 'educational-toys', 'image' => 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=800'],
    ['name' => 'Footwear', 'slug' => 'footwear', 'image' => 'https://images.unsplash.com/photo-1515347619252-60a6bf4fffce?w=800'],
    ['name' => 'School Essentials', 'slug' => 'school-essentials', 'image' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?w=800']
];

$stmt = $conn->prepare("INSERT INTO `categories` (`name`, `slug`, `image`) VALUES (?, ?, ?)");
foreach ($categories as $cat) {
    $stmt->bind_param("sss", $cat['name'], $cat['slug'], $cat['image']);
    $stmt->execute();
    echo "Inserted Category: " . $cat['name'] . "<br>";
}

// Products
$products = [
    // Boys Clothing
    [
        'category_slug' => 'boys-clothing', 'name' => 'Classic Blue T-Shirt', 'price' => 799, 'mrp' => 1299,
        'desc' => 'Premium cotton t-shirt for boys. Durable and comfortable for everyday wear.',
        'img' => 'https://images.unsplash.com/photo-1576485089853-91147a46fa7a?w=800'
    ],
    [
        'category_slug' => 'boys-clothing', 'name' => 'Denim Joggers', 'price' => 1499, 'mrp' => 2499,
        'desc' => 'Stylish denim joggers with elastic waist. Perfect fit for active kids.',
        'img' => 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?w=800'
    ],
    [
        'category_slug' => 'boys-clothing', 'name' => 'Casual Check Shirt', 'price' => 999, 'mrp' => 1799,
        'desc' => 'Soft flannel check shirt. Ideal for casual outings or parties.',
        'img' => 'https://images.unsplash.com/photo-1580556133912-329437bce5e1?w=800'
    ],

    // Girls Clothing
    [
        'category_slug' => 'girls-clothing', 'name' => 'Floral Summer Dress', 'price' => 1299, 'mrp' => 1999,
        'desc' => 'Beautiful floral print dress made from breathable cotton fabric.',
        'img' => 'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?w=800'
    ],
    [
        'category_slug' => 'girls-clothing', 'name' => 'Pink Party Frock', 'price' => 1999, 'mrp' => 2999,
        'desc' => 'Elegant party frock with detailed embroidery. Perfect for special occasions.',
        'img' => 'https://images.unsplash.com/photo-1621452773781-0f992ee61e67?w=800'
    ],
    [
        'category_slug' => 'girls-clothing', 'name' => 'Denim Skirt', 'price' => 899, 'mrp' => 1499,
        'desc' => 'Classic blue denim skirt with adjustable waist band.',
        'img' => 'https://images.unsplash.com/photo-1622290291468-a28f7a7dc6a8?w=800'
    ],

    // Educational Toys
    [
        'category_slug' => 'educational-toys', 'name' => 'Science Lab Kit', 'price' => 1599, 'mrp' => 2499,
        'desc' => 'Complete science experiment kit for curious young minds. Safe and fun.',
        'img' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=800'
    ],
    [
        'category_slug' => 'educational-toys', 'name' => 'Wooden Building Blocks', 'price' => 999, 'mrp' => 1599,
        'desc' => 'Set of 50 wooden blocks. Enhances creativity and motor skills.',
        'img' => 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=800'
    ],
    [
        'category_slug' => 'educational-toys', 'name' => 'Solar System Globe', 'price' => 1299, 'mrp' => 1799,
        'desc' => 'Interactive globe teaches planets and space facts.',
        'img' => 'https://images.unsplash.com/photo-1614730341194-75c60740a2d3?w=800'
    ],

    // Footwear
    [
        'category_slug' => 'footwear', 'name' => 'Running Sneakers', 'price' => 1899, 'mrp' => 2799,
        'desc' => 'Lightweight running shoes with superior grip and comfort.',
        'img' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800'
    ],
    [
        'category_slug' => 'footwear', 'name' => 'Casual Sandals', 'price' => 799, 'mrp' => 1199,
        'desc' => 'Comfortable open-toe sandals for summer wear.',
        'img' => 'https://images.unsplash.com/photo-1560769622-515bab3d278f?w=800'
    ],
    [
        'category_slug' => 'footwear', 'name' => 'Winter Boots', 'price' => 2499, 'mrp' => 3499,
        'desc' => 'Warm and cozy boots with water-resistant material.',
        'img' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800'
    ],

    // School Essentials
    [
        'category_slug' => 'school-essentials', 'name' => 'Ergonomic Backpack', 'price' => 1499, 'mrp' => 2299,
        'desc' => 'Spacious backpack with padded straps for back support.',
        'img' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?w=800'
    ],
    [
        'category_slug' => 'school-essentials', 'name' => 'Insulated Lunch Box', 'price' => 899, 'mrp' => 1299,
        'desc' => 'Keeps food fresh for hours. Durable and easy to clean.',
        'img' => 'https://images.unsplash.com/photo-1512404283808-1647f3ad5b78?w=800'
    ],
    [
        'category_slug' => 'school-essentials', 'name' => 'Stainless Steel Bottle', 'price' => 699, 'mrp' => 999,
        'desc' => 'Eco-friendly water bottle. Leak-proof and stylish.',
        'img' => 'https://images.unsplash.com/photo-1602143407151-11115cdbf69c?w=800'
    ]
];

$stmt_prod = $conn->prepare("INSERT INTO `products` (`category_id`, `name`, `slug`, `short_description`, `description`, `price`, `mrp`, `stock`, `is_active`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
$stmt_img = $conn->prepare("INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES (?, ?, ?)");

foreach ($products as $prod) {
    // Get Category ID
    $res = $conn->query("SELECT id FROM categories WHERE slug = '" . $prod['category_slug'] . "'");
    $cat_id = $res->fetch_assoc()['id'];

    $slug = strtolower(str_replace(' ', '-', $prod['name']));
    $short_desc = substr($prod['desc'], 0, 100) . '...';
    $stock = rand(30, 100);

    $stmt_prod->bind_param("issssdii", $cat_id, $prod['name'], $slug, $short_desc, $prod['desc'], $prod['price'], $prod['mrp'], $stock);
    
    if ($stmt_prod->execute()) {
        $product_id = $stmt_prod->insert_id;
        echo "Inserted Product: " . $prod['name'] . "<br>";

        // Insert 4 Images
        for ($i = 0; $i < 4; $i++) {
            $img_url = $prod['img'] . "&sig=" . ($product_id * 10 + $i); // Ensure unique images
            $stmt_img->bind_param("isi", $product_id, $img_url, $i);
            $stmt_img->execute();
        }
    } else {
        echo "Error inserting product: " . $stmt_prod->error . "<br>";
    }
}

echo "<hr><strong>Data Seeding Complete!</strong>";
?>
