<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Test 1: Check if .env file exists
$envPath = __DIR__ . '/../.env';
echo "<h2>Test 1: .env file</h2>";
if (file_exists($envPath)) {
    echo "✓ .env file found at: " . realpath($envPath) . "<br>";
    echo "<pre>";
    $envContent = file_get_contents($envPath);
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (strpos($line, 'DB_') === 0) {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "✗ .env file NOT found at: $envPath<br>";
}

// Test 2: Try to connect to database
echo "<h2>Test 2: Database Connection</h2>";
try {
    $host = 'localhost';
    $port = '3306';
    $dbname = 'restaurant_db';
    $username = 'root';
    $password = '';
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    echo "Connecting with DSN: $dsn<br>";
    echo "User: $username<br>";
    echo "Password: " . (empty($password) ? '(empty)' : '***') . "<br>";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✓ Database connection successful!<br>";
    
    // Test 3: Check tables
    echo "<h2>Test 3: Check Tables</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "✓ Found " . count($tables) . " tables:<br>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Test 4: Check menu_items
        echo "<h2>Test 4: Check menu_items data</h2>";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM menu_items");
        $result = $stmt->fetch();
        echo "✓ menu_items table has " . $result['count'] . " rows<br>";
        
        if ($result['count'] > 0) {
            $stmt = $pdo->query("SELECT id, name, price FROM menu_items LIMIT 5");
            $items = $stmt->fetchAll();
            echo "<h3>Sample items:</h3><ul>";
            foreach ($items as $item) {
                echo "<li>{$item['id']}: {$item['name']} - {$item['price']} Ft</li>";
            }
            echo "</ul>";
        }
        
        // Test 5: Check categories
        echo "<h2>Test 5: Check categories data</h2>";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
        $result = $stmt->fetch();
        echo "✓ categories table has " . $result['count'] . " rows<br>";
        
        if ($result['count'] > 0) {
            $stmt = $pdo->query("SELECT id, name FROM categories");
            $cats = $stmt->fetchAll();
            echo "<h3>Categories:</h3><ul>";
            foreach ($cats as $cat) {
                echo "<li>{$cat['id']}: {$cat['name']}</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "✗ No tables found in database<br>";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed:<br>";
    echo "<pre style='color: red;'>";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "</pre>";
}
