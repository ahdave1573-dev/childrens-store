<?php

$categories = [
    "Educational Toys", "Boys Fashion", "Girls Fashion", "Baby Care", "Footwear",
    "School Supplies", "Party Supplies", "Bedding & Decor", "Feeding & Nursing", "Outdoor Fun"
];

$products_map = [
    "Educational Toys" => ["Alphabet Blocks", "Science Kit", "Math Puzzle", "Solar System Model", "Building Bricks", "Microscope Set", "Globe", "Robot Kit", "Wooden Abacus", "Chemistry Set"],
    "Boys Fashion" => ["Denim Jeans", "Graphic T-Shirt", "Hoodie", "Cargo Shorts", "Formal Shirt", "Winter Jacket", "Sneakers", "Baseball Cap", "Sweater", "Pajama Set"],
    "Girls Fashion" => ["Floral Dress", "Leggings", "Denim Skirt", "Party Frock", "Cardigan", "Tunic Top", "Sandals", "Hair Accessories", "Princess Gown", "Jumpsuit"],
    "Baby Care" => ["Diaper Bag", "Baby Lotion", "Shampoo", "Wipes Pack", "Baby Oil", "Powder", "Teether", "Pacifier", "Bath Tub", "Nail Clipper"],
    "Footwear" => ["Running Shoes", "School Shoes", "Sandals", "Boots", "Slippers", "Loafers", "Canvas Shoes", "Flip Flops", "Party Heels", "Sports Sandals"],
    "School Supplies" => ["Backpack", "Pencil Box", "Water Bottle", "Lunch Box", "Notebook Set", "Color Pencils", "Geometry Box", "School Bag", "Crayons", "Markers"],
    "Party Supplies" => ["Balloons Pack", "Birthday Banner", "Party Hats", "Candles", "Gift Wrap", "Paper Plates", "Party Poppers", "Masks", "Decor Streamers", "Invitation Cards"],
    "Bedding & Decor" => ["Bed Sheet", "Pillow", "Curtains", "Rug", "Wall Stickers", "Blanket", "Lamp Shade", "Cushion Cover", "Quilt", "Mosquito Net"],
    "Feeding & Nursing" => ["Feeding Bottle", "Bib", "High Chair", "Breast Pump", "Sippy Cup", "Bottle Warmer", "Sterilizer", "Milk Container", "Food Feeder", "Burp Cloth"],
    "Outdoor Fun" => ["Cricket Bat", "Football", "Badminton Set", "Skateboard", "Bicycle", "Frisbee", "Jump Rope", "Tent", "Water Gun", "Scooter"]
];

$sql_content = "DROP DATABASE IF EXISTS childrens_store;
CREATE DATABASE childrens_store;
USE childrens_store;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO admin (username, password) VALUES ('admin', 'admin123');
";

// Unsplash Image IDs Mapping
$unsplash_ids = [
    "Educational Toys" => ["1587654780291-7719f42b7bd3", "1566576912327-8a7246437534", "1596461404969-9ae70f2830c1"],
    "Boys Fashion" => ["1519238263596-0643681f193f", "1519457431-44ccd64a579b", "1471286174890-9c808743015a"],
    "Girls Fashion" => ["1514090458221-65bb69cf63e6", "1621451973529-c6e12052dfb8", "1524749292158-756095483167"],
    "Baby Care" => ["1515488042361-ee00e0ddd4e4", "1555252333-9f8e92e65df9", "1544126210-951596e184cc"],
    "Footwear" => ["1511556532299-8f662fc26d06", "1595950653126-68f78aa0dae5", "1560769619308-f731d7e2e0fb"],
    "School Supplies" => ["1452860606245-08befc0ff44b", "1503676260728-1c00da094a0b", "1497633762265-9d179a990aa6"],
    "Party Supplies" => ["1530103862676-de3c9a59af38", "1504196606672-aef5c9cefc92", "1464349128770-8d079ebc6730"],
    "Bedding & Decor" => ["1513297887112-cafd9dcd483d", "1505693542198-34528fa95869", "1522771753003-bd38d1218549"],
    "Feeding & Nursing" => ["1567104523-86318ed81e3a", "1567104523-86318ed81e3a", "1610996173006-2d334517dd37"], // Duplicated intentionally if limited
    "Outdoor Fun" => ["1596464716127-f9a829be7ca5", "1517173295834-3683a37ba53e", "1485965120185-693941558211"]
];

function getUnsplashUrl($ids) {
    $id = $ids[array_rand($ids)];
    return "https://images.unsplash.com/photo-$id?auto=format&fit=crop&q=80&w=600";
}

// Insert Categories
foreach ($categories as $cat) {
    // Use the first image from the set for the category representative
    $cat_img = isset($unsplash_ids[$cat]) ? getUnsplashUrl($unsplash_ids[$cat]) : "https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?auto=format&fit=crop&q=80&w=600";
    $sql_content .= "INSERT INTO categories (name, image) VALUES ('$cat', '$cat_img');\n";
}

// Insert Products
$cat_id = 1;
foreach ($products_map as $cat => $products) {
    $i = 0;
    $cat_images = $unsplash_ids[$cat] ?? ["1596461404969-9ae70f2830c1"];
    
    foreach ($products as $prod_name) {
        // Pricing logic: Random base cost between 300 and 2000
        $base_cost = rand(300, 2000);
        // 40% profit margin logic: Price = Cost * 1.4
        $price = intval($base_cost * 1.4);
        // Psychological pricing: end in 99
        $price = floor($price / 100) * 100 + 99;
        if ($price < 499) $price = 499; // Minimum price
        
        // Cycle through images or pick random
        $img_url = getUnsplashUrl($cat_images);
        $desc = "High quality $prod_name for your kids. Safe and durable.";
        
        $sql_content .= "INSERT INTO products (category_id, name, description, price, image, stock) VALUES ($cat_id, '$prod_name', '$desc', $price, '$img_url', 50);\n";
        $i++;
    }
    $cat_id++;
}

// Save the file
$file_path = __DIR__ . "/setup_full_database.sql";
file_put_contents($file_path, $sql_content);

echo "setup_full_database.sql created successfully at $file_path";
?>
