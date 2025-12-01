<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Termék ID hiányzik'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    // Check if item exists
    $checkStmt = $db->prepare("SELECT id FROM menu_items WHERE id = ?");
    $checkStmt->execute([$input['id']]);
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Termék nem található'
        ]);
        exit;
    }
    
    // Generate slug from name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $input['name'])));
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Ensure unique slug (excluding current item)
    $baseSlug = $slug;
    $counter = 1;
    while (true) {
        $checkStmt = $db->prepare("SELECT id FROM menu_items WHERE slug = ? AND id != ?");
        $checkStmt->execute([$slug, $input['id']]);
        if (!$checkStmt->fetch()) break;
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    $stmt = $db->prepare("
        UPDATE menu_items SET
            category_id = ?,
            name = ?,
            slug = ?,
            description = ?,
            price = ?,
            image_url = ?,
            ingredients = ?,
            allergens = ?,
            is_available = ?
        WHERE id = ?
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
        $input['is_available'] ?? 1,
        $input['id']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Termék sikeresen frissítve!'
    ]);
    
} catch (Exception $e) {
    error_log("Menu update error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
