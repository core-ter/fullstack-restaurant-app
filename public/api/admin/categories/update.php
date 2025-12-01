<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['name'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID vagy név hiányzik'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    $stmt = $db->prepare("
        UPDATE categories 
        SET name = ?, display_order = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $input['name'],
        $input['display_order'] ?? 999,
        $input['id']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Kategória frissítve!'
    ]);
    
} catch (Exception $e) {
    error_log("Category update error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba: ' . $e->getMessage()
    ]);
}
