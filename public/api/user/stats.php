<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Bejelentkezés szükséges'
    ]);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    $db = getDBConnection();
    
    // Get user statistics
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(total_amount), 0) as total_spent,
            COALESCE(AVG(total_amount), 0) as average_order
        FROM orders
        WHERE user_id = ? AND is_guest = 0
    ");
    
    $stmt->execute([$userId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_orders' => (int)$stats['total_orders'],
            'total_spent' => (float)$stats['total_spent'],
            'average_order' => (float)$stats['average_order']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("User stats fetch error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba történt az adatok lekérdezése során'
    ]);
}
