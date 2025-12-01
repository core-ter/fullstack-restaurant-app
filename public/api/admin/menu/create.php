<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['name']) || !isset($input['price']) || !isset($input['category_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Hiányzó kötelező mezők: név, ár, kategória'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    // Generate slug from name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $input['name'])));
    $slug = preg_replace('/-+/', '-', $slug); // Remove multiple dashes
    $slug = trim($slug, '-'); // Remove leading/trailing dashes
    
    // Ensure unique slug
    $baseSlug = $slug;
    $counter = 1;
    while (true) {
        $checkStmt = $db->prepare("SELECT id FROM menu_items WHERE slug = ?");
        $checkStmt->execute([$slug]);
        if (!$checkStmt->fetch()) break;
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    $stmt = $db->prepare("
        INSERT INTO menu_items (
            category_id,
            name,
            slug,
            description,
            price,
            image_url,
            ingredients,
            allergens,
            is_available
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $input['category_id'],
        $input['name'],
        $slug,
        $input['description'] ?? null,
        $input['price'],
        $input['image_url'] ?? null,
        $input['ingredients'] ?? null,
        $input['allergens'] ?? null,
        $input['is_available'] ?? 1
    ]);
    
    $itemId = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Termék sikeresen hozzáadva!',
        'item_id' => $itemId
    ]);
    
} catch (Exception $e) {
    error_log("Menu create error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
