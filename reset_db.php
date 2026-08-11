<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";

// Create connection without selecting DB
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read SQL file
$sql = file_get_contents('setup_full_database.sql');

if ($conn->multi_query($sql)) {
    echo "<h1>Database Reset Successfully! ✅</h1>";
    echo "<p>Please <a href='index.php'>click here</a> to visit your store.</p>";
    
    // consume all results to avoid "Commands out of sync" error
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
} else {
    echo "Error resetting database: " . $conn->error;
}

$conn->close();
?>
