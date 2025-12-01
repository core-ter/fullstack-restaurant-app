<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['name'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Kategória név hiányzik'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    $stmt = $db->prepare("
        INSERT INTO categories (name, display_order) 
        VALUES (?, ?)
    ");
    
    $stmt->execute([
        $input['name'],
        $input['display_order'] ?? 999
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Kategória sikeresen létrehozva!',
        'category_id' => $db->lastInsertId()
    ]);
    
} catch (Exception $e) {
    error_log("Category create error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba: ' . $e->getMessage()
    ]);
}
