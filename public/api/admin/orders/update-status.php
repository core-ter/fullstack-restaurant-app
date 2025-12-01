<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

// Check authentication
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$orderId = $input['order_id'] ?? null;
$statusCode = $input['status_code'] ?? null;

if (!$orderId || !$statusCode) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $db = getDBConnection();
    
    // Get status ID from code
    $stmt = $db->prepare("SELECT id FROM order_statuses WHERE status_code = :status_code");
    $stmt->execute([':status_code' => $statusCode]);
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$status) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid status code']);
        exit;
    }
    
    // Update order status
    $stmt = $db->prepare("
        UPDATE orders 
        SET status_id = :status_id,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :order_id
    ");
    
    $stmt->execute([
        ':status_id' => $status['id'],
        ':order_id' => $orderId
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Status update error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
