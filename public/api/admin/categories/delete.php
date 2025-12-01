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
            'message' => 'ID hiányzik'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    // Check if category has items
    $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM menu_items WHERE category_id = ?");
    $checkStmt->execute([$input['id']]);
    $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nem törölhető! A kategóriához tartoznak termékek.'
        ]);
        exit;
    }
    
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$input['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Kategória törölve!'
    ]);
    
} catch (Exception $e) {
    error_log("Category delete error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba: ' . $e->getMessage()
    ]);
}
