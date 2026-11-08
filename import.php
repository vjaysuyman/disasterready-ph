<?php
// import.php - Temporary file para ma-import ang database sa Aiven
require_once 'config/database.php';

echo "<h2>Starting Database Import...</h2>";

$sqlFile = 'database/disasterready.sql';

if (!file_exists($sqlFile)) {
    die("Error: SQL file not found at " . $sqlFile);
}

$sql = file_get_contents($sqlFile);

try {
    // I-run ang SQL query
    $pdo->exec($sql);
    echo "<h3 style='color: green;'>✅ Database imported successfully!</h3>";
    echo "<p><strong>IMPORTANTE:</strong> I-delete na dayon kini nga file (import.php) sa imong GitHub ug Render.</p>";
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error importing database:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>