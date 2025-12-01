<?php
/**
 * Menu API Endpoint
 * Returns menu items with optional category filtering
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDBConnection();
    
    // Get category filter from query string
    $categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;
    
    // Build query
    $query = "
        SELECT 
            mi.id,
            mi.name,
            mi.description,
            mi.ingredients,
            mi.allergens,
            mi.price,
            mi.image_url,
            mi.is_available,
            c.id as category_id,
            c.name as category_name
        FROM menu_items mi
        LEFT JOIN categories c ON mi.category_id = c.id
        WHERE mi.deleted_at IS NULL
    ";
    
    $params = [];
    
    if ($categoryId) {
        $query .= " AND mi.category_id = :category_id";
        $params['category_id'] = $categoryId;
    }
    
    $query .= " ORDER BY c.display_order ASC, mi.name ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $menuItems = $stmt->fetchAll();
    
    // Get all categories
    $categoriesQuery = "
        SELECT id, name, display_order
        FROM categories
        ORDER BY display_order ASC
    ";
    $categoriesStmt = $pdo->query($categoriesQuery);
    $categories = $categoriesStmt->fetchAll();
    
    // Send response
    echo json_encode([
        'success' => true,
        'data' => [
            'items' => $menuItems,
            'categories' => $categories
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Hiba történt az adatok lekérésekor.',
        'debug' => $e->getMessage()
    ]);
}
