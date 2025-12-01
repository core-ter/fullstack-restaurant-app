<?php
/**
 * Calculate Delivery Fee API
 * Calculate delivery fee based on distance from restaurant
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/database.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get latitude and longitude from query params
    $lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
    $lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
    
    if ($lat === null || $lng === null) {
        throw new Exception('Missing latitude or longitude');
    }
    
    // Get database connection
    $db = getDBConnection();
    
    // Get restaurant location
    $stmt = $db->prepare("SELECT latitude, longitude FROM restaurant_settings LIMIT 1");
    $stmt->execute();
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$restaurant) {
        throw new Exception('Restaurant settings not found');
    }
    
    // Calculate distance using Haversine formula (in kilometers)
    $earthRadius = 6371; // Earth radius in kilometers
    
    $latFrom = deg2rad($restaurant['latitude']);
    $lonFrom = deg2rad($restaurant['longitude']);
    $latTo = deg2rad($lat);
    $lonTo = deg2rad($lng);
    
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;
    
    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos($latFrom) * cos($latTo) *
         sin($lonDelta / 2) * sin($lonDelta / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    $distance = $earthRadius * $c; // Distance in kilometers
    
    // Get delivery fee based on distance
    $stmt = $db->prepare("
        SELECT fee, delivery_time_minutes 
        FROM delivery_zones 
        WHERE :distance1 >= distance_from_km 
          AND :distance2 <= distance_to_km
        LIMIT 1
    ");
    $stmt->execute([
        ':distance1' => $distance,
        ':distance2' => $distance
    ]);
    $zone = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$zone) {
        // If no zone found, use the highest zone
        $stmt = $db->prepare("
            SELECT fee, delivery_time_minutes 
            FROM delivery_zones 
            ORDER BY distance_to_km DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $zone = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'distance_km' => round($distance, 2),
        'fee' => intval($zone['fee']),
        'delivery_time' => intval($zone['delivery_time_minutes'])
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Delivery fee error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => $e->getTraceAsString()
    ], JSON_UNESCAPED_UNICODE);
}
