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
    
    $stmt = $db->prepare("DELETE FROM delivery_zones WHERE id = ?");
    $stmt->execute([$input['id']]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Zóna nem található'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Zóna törölve!'
    ]);
    
} catch (Exception $e) {
    error_log("Zone delete error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba: ' . $e->getMessage()
    ]);
}
