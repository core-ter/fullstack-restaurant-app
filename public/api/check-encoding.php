<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    echo "<h1>Database Encoding Check</h1>";
    echo "<p>Connection Charset: " . $pdo->query("SELECT @@character_set_connection")->fetchColumn() . "</p>";
    echo "<p>Database Charset: " . $pdo->query("SELECT @@character_set_database")->fetchColumn() . "</p>";
    
    $stmt = $pdo->query("SELECT name, HEX(name) as hex_name FROM menu_items LIMIT 5");
    $items = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Name</th><th>Hex Dump</th><th>Analysis</th></tr>";
    
    foreach ($items as $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['name']) . "</td>";
        echo "<td>" . $item['hex_name'] . "</td>";
        echo "<td>";
        
        // Check for common issues
        if (strpos($item['name'], '?') !== false) {
            echo "Contains '?' (0x3F) - Data likely corrupted during import";
        } elseif (preg_match('/[\x80-\xFF]/', $item['name'])) {
            echo "Contains multibyte chars - Looks like UTF-8";
        } else {
            echo "ASCII only";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
