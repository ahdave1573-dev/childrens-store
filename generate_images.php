<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Image Prompts</title>
    <style>
        body { font-family: sans-serif; padding: 20px; line-height: 1.6; }
        .prompt-block { background: #f4f4f4; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .copy-btn { margin-left: 10px; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>

<h1>Image Generation Prompts</h1>
<p>Copy and paste these prompts into your preferred AI image generator (Midjourney, DALL-E, etc.).</p>
<p>Save the files to <code>c:\xampp\htdocs\Childrens-Store\images\</code> (create the folder if it doesn't exist).</p>
<hr>

<?php

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

$base_prompt = "Bright, Studio Lighting, Pastel Backgrounds, 3D Render style, high quality product photography of ";

foreach ($products_map as $cat => $products) {
    echo "<h3>Category: $cat</h3>";
    foreach ($products as $i => $prod_name) {
        $filename = strtolower(str_replace(' ', '_', $cat)) . "_" . ($i + 1) . ".jpg";
        $prompt = $base_prompt . $prod_name;
        
        echo "<div class='prompt-block'>";
        echo "<strong>File:</strong> $filename<br>";
        echo "<strong>Prompt:</strong> $prompt";
        echo "</div>";
    }
}
?>

</body>
</html>
