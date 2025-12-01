<?php
/**
 * List Orders API Endpoint
 * Retrieves orders for guest users by email
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

try {
    // Get database connection
    $db = getDBConnection();
    
    // For now, get all guest orders (latest 20)
    // TODO: Add email-based filtering for guest users
    $stmt = $db->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.guest_first_name,
            o.guest_last_name,
            o.guest_phone,
            o.guest_email,
            o.delivery_address,
            o.subtotal,
            o.delivery_fee,
            o.total_amount,
            o.created_at,
            os.status_code,
            os.display_name as status_name
        FROM orders o
        LEFT JOIN order_statuses os ON o.status_id = os.id
        WHERE o.is_guest = 1
        ORDER BY o.created_at DESC
        LIMIT 20
    ");
    
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch items for each order
    foreach ($orders as &$order) {
        $stmt = $db->prepare("
            SELECT 
                item_name,
                item_price,
                quantity
            FROM order_items
            WHERE order_id = :order_id
        ");
        
        $stmt->execute([':order_id' => $order['id']]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'orders' => $orders
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
