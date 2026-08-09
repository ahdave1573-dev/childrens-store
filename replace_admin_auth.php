<?php
$files = glob('C:\\xampp\\htdocs\\Childrens-Store\\admin\\*.php');
$pattern = "/require_once '\.\.\/includes\/session\.php';[\s\n]*if \(!isset\(\\$_SESSION\['user_role'\]\) \|\| \\$_SESSION\['user_role'\] !== 'admin'\) \{[\s\n]*header\(\"Location:[A-Za-z0-9\.\/ -]*login\.php\"\);[\s\n]*exit[\(\);]*[\s\n]*\}/is";
$replacement = "require_once __DIR__ . '/../middleware/require_admin.php';";

foreach ($files as $file) {
    if (strpos($file, 'admin_login.php') !== false) continue;
    
    $content = file_get_contents($file);
    $new_content = preg_replace($pattern, $replacement, $content);

    // Some files might be slightly different. We check for another common pattern
    $pattern2 = "/require_once '\.\.\/includes\/session\.php';\s*if \(\!isset\(\\$_SESSION\['user_role'\]\)\s*\|\|\s*\\$_SESSION\['user_role'\]\s*\!\=\=\s*'admin'\)\s*\{\s*header\(\"Location:\s*[^\"]*login\.php\"\);\s*exit\;?\s*\}/is";
    $new_content = preg_replace($pattern2, $replacement, $new_content);

    file_put_contents($file, $new_content);
    echo "Processed: $file\n";
}
?>
