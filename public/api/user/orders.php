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
    
    // Get all orders for this user
    $stmt = $db->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.total_amount,
            o.delivery_fee,
            o.delivery_address,
            COALESCE(o.guest_phone, '') as phone,
            COALESCE(o.customer_notes, '') as special_instructions,
            o.created_at,
            o.status_id
        FROM orders o
        WHERE o.user_id = ? AND o.is_guest = 0
        ORDER BY o.created_at DESC
    ");
    
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Status mapping (same as track.php)
    $statusMap = [
        1 => ['code' => 'pending', 'name' => 'Fogadva', 'color' => '#FFA500'],
        2 => ['code' => 'confirmed', 'name' => 'Megerősítve', 'color' => '#00BFFF'],
        3 => ['code' => 'preparing', 'name' => 'Készítés alatt', 'color' => '#FF6347'],
        4 => ['code' => 'out_for_delivery', 'name' => 'Kiszállítás alatt', 'color' => '#32CD32'],
        5 => ['code' => 'delivered', 'name' => 'Kiszállítva', 'color' => '#228B22'],
        6 => ['code' => 'cancelled', 'name' => 'Törölve', 'color' => '#DC143C']
    ];
    
    // Get order items for each order and add status info
    foreach ($orders as &$order) {
        // Add status info
        $statusInfo = $statusMap[$order['status_id']] ?? $statusMap[1];
        $order['status_code'] = $statusInfo['code'];
        $order['status_name'] = $statusInfo['name'];
        $order['status_color'] = $statusInfo['color'];
        
        // Get items
        $itemsStmt = $db->prepare("
            SELECT 
                oi.quantity,
                oi.item_price as price,
                oi.item_name,
                mi.image_url
            FROM order_items oi
            LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE oi.order_id = ?
        ");
        $itemsStmt->execute([$order['id']]);
        $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate item count
        $order['item_count'] = array_sum(array_column($order['items'], 'quantity'));
    }
    
    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);
    
} catch (Exception $e) {
    error_log("User orders fetch error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba történt a rendelések lekérdezése során',
        'error' => $e->getMessage() // Debug only
    ]);
}
