<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    $db = getDBConnection();
    
    // Get category filter if provided
    $categoryId = $_GET['category_id'] ?? null;
    
    // Build query
    $query = "
        SELECT 
            mi.id,
            mi.name,
            mi.description,
            mi.price,
            mi.category_id,
            mi.image_url,
            mi.is_available,
            mi.ingredients,
            mi.allergens,
            c.name as category_name
        FROM menu_items mi
        JOIN categories c ON mi.category_id = c.id
        WHERE mi.deleted_at IS NULL
    ";
    
    $params = [];
    
    if ($categoryId) {
        $query .= " AND mi.category_id = ?";
        $params[] = $categoryId;
    }
    
    $query .= " ORDER BY c.display_order, mi.name";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all categories for filter
    $categoriesStmt = $db->query("
        SELECT id, name, display_order 
        FROM categories 
        ORDER BY display_order
    ");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'categories' => $categories
    ]);
    
} catch (Exception $e) {
    error_log("Menu list error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
