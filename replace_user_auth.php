<?php
$files = ['C:\\xampp\\htdocs\\Childrens-Store\\checkout.php', 'C:\\xampp\\htdocs\\Childrens-Store\\profile.php'];
$pattern = "/require_once 'includes\/session\.php';(.*?)if \(\!isset\(\\$_SESSION\['user_id'\]\)\)\s*\{\s*header\(\"Location:\s*login\.php\"\);\s*exit;\s*\}/is";
$replacement = "require_once __DIR__ . '/middleware/require_login.php';\\1";

foreach ($files as $file) {
    if(file_exists($file)) {
        $content = file_get_contents($file);
        $new_content = preg_replace($pattern, $replacement, $content);
        file_put_contents($file, $new_content);
        echo "Processed: $file\n";
    }
}
?>
