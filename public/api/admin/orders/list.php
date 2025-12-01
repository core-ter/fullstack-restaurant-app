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

try {
    $db = getDBConnection();
    
    // Get filters
    $status = $_GET['status'] ?? null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $search = $_GET['search'] ?? null;
    
    // Build query
    $query = "
        SELECT 
            o.*,
            os.status_code,
            os.display_name as status_name
        FROM orders o
        JOIN order_statuses os ON o.status_id = os.id
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($status && $status !== 'all') {
        $query .= " AND os.status_code = ?";
        $params[] = $status;
    } else {
        // Only show active orders by default (exclude completed and cancelled)
        $query .= " AND os.status_code NOT IN ('completed', 'cancelled')";
    }
    
    if ($search) {
        $query .= " AND (o.order_number LIKE ? OR o.guest_first_name LIKE ? OR o.guest_last_name LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }
    
    $query .= " ORDER BY o.created_at DESC LIMIT ?";
    $params[] = $limit;
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get items for each order
    foreach ($orders as &$order) {
        $stmt = $db->prepare("
            SELECT 
                oi.id,
                oi.order_id,
                oi.menu_item_id,
                oi.quantity,
                oi.item_price as unit_price,
                COALESCE(mi.name, oi.item_name) as item_name
            FROM order_items oi
            LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);
    
} catch (Exception $e) {
    error_log("Orders list error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
