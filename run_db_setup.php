<?php
// Script to execute the SQL file
require_once __DIR__ . '/config/database.php';

$file = 'setup_production_db.sql';

if (!file_exists($file)) {
    die("Error: SQL file not found.");
}

$sql = file_get_contents($file);

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Multi_query to execute all statements
    if ($conn->multi_query($sql)) {
        do {
            // Consume all results
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        
        echo "<h1 style='color: green;'>Database Setup Successful!</h1>";
        echo "<p>Tables created, constraints added, and default data inserted.</p>";
    } else {
        echo "Error executing SQL: " . $conn->error;
    }
} catch (Exception $e) {
    echo "<h1 style='color: red;'>Database Setup Failed</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
