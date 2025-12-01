<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

try {
    $orderNumber = isset($_GET['number']) ? trim($_GET['number']) : '';
    
    if (empty($orderNumber)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Hiányzó rendelésszám'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    // Get order details with delivery zone info
    $stmt = $db->prepare("
        SELECT 
            o.id,
            o.order_number,
            o.total_amount,
            o.delivery_fee,
            o.delivery_address,
            o.delivery_latitude,
            o.delivery_longitude,
            COALESCE(o.guest_phone, '') as phone,
            COALESCE(o.customer_notes, '') as special_instructions,
            o.created_at,
            o.status_id,
            dz.delivery_time_minutes
        FROM orders o
        LEFT JOIN delivery_zones dz ON (
            o.distance_km BETWEEN dz.distance_from_km AND dz.distance_to_km
        )
        WHERE o.order_number = ?
        LIMIT 1
    ");
    
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Rendelés nem található'
        ]);
        exit;
    }
    
    // Map status_id to status info
    $statusMap = [
        1 => ['code' => 'pending', 'name' => 'Fogadva', 'color' => '#FFA500'],
        2 => ['code' => 'confirmed', 'name' => 'Megerősítve', 'color' => '#00BFFF'],
        3 => ['code' => 'preparing', 'name' => 'Készítés alatt', 'color' => '#FF6347'],
        4 => ['code' => 'out_for_delivery', 'name' => 'Kiszállítás alatt', 'color' => '#32CD32'],
        5 => ['code' => 'delivered', 'name' => 'Kiszállítva', 'color' => '#228B22'],
        6 => ['code' => 'cancelled', 'name' => 'Törölve', 'color' => '#DC143C']
    ];
    
    $statusInfo = $statusMap[$order['status_id']] ?? $statusMap[1];
    $order['status_code'] = $statusInfo['code'];
    $order['status_name'] = $statusInfo['name'];
    $order['status_color'] = $statusInfo['color'];
    
    // Get order items
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
    
    // Default delivery time if no zone found
    if (!$order['delivery_time_minutes']) {
        $order['delivery_time_minutes'] = 30; // Default 30 minutes
    }
    
    echo json_encode([
        'success' => true,
        'order' => $order
    ]);
    
} catch (Exception $e) {
    error_log("Order tracking error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba történt a rendelés lekérdezése során',
        'error' => $e->getMessage() // Debug only - remove in production
    ]);
}
