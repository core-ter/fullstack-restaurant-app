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
    
    // Get current status
    $checkStmt = $db->prepare("SELECT is_available FROM menu_items WHERE id = ?");
    $checkStmt->execute([$input['id']]);
    $item = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$item) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Termék nem található'
        ]);
        exit;
    }
    
    // Toggle status
    $newStatus = $item['is_available'] ? 0 : 1;
    
    $stmt = $db->prepare("UPDATE menu_items SET is_available = ? WHERE id = ?");
    $stmt->execute([$newStatus, $input['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => $newStatus ? 'Termék elérhetővé téve!' : 'Termék elérhetetlenné téve!',
        'is_available' => $newStatus
    ]);
    
} catch (Exception $e) {
    error_log("Menu toggle error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
