<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing require path...<br>";
echo "__DIR__ = " . __DIR__ . "<br>";
echo "Looking for: " . __DIR__ . '/../config/database.php' . "<br>";
echo "File exists: " . (file_exists(__DIR__ . '/../config/database.php') ? 'YES' : 'NO') . "<br>";

if (file_exists(__DIR__ . '/../config/database.php')) {
    echo "Attempting to require...<br>";
    require_once __DIR__ . '/../config/database.php';
    echo "Success! Database file loaded.<br>";
    
    try {
        echo "Attempting to connect...<br>";
        $pdo = getDBConnection();
        echo "Connected successfully!<br>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM menu_items");
        $result = $stmt->fetch();
        echo "Found " . $result['count'] . " menu items!<br>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "<br>Database.php not found! Checking directory structure:<br>";
    echo "Contents of " . dirname(__DIR__) . ":<br>";
    $files = scandir(dirname(__DIR__));
    echo "<pre>";
    print_r($files);
    echo "</pre>";
}
