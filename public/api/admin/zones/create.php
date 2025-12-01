<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['distance_from_km']) || !isset($input['distance_to_km']) || !isset($input['fee'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Hiányzó kötelező mezők'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    // Validate ranges
    if ($input['distance_from_km'] < 0 || $input['distance_to_km'] <= $input['distance_from_km']) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Érvénytelen távolság intervallum'
        ]);
        exit;
    }
    
    // Check for overlaps - any zone where ranges intersect
    $checkStmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM delivery_zones 
        WHERE NOT (? >= distance_to_km OR ? <= distance_from_km)
    ");
    $checkStmt->execute([$input['distance_from_km'], $input['distance_to_km']]);
    $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Átfedő zóna! Ez a távolság intervallum már létezik.'
        ]);
        exit;
    }
    
    $stmt = $db->prepare("
        INSERT INTO delivery_zones (distance_from_km, distance_to_km, fee, delivery_time_minutes)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $input['distance_from_km'],
        $input['distance_to_km'],
        $input['fee'],
        $input['delivery_time_minutes'] ?? 30
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Zóna sikeresen létrehozva!',
        'zone_id' => $db->lastInsertId()
    ]);
    
} catch (Exception $e) {
    error_log("Zone create error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba: ' . $e->getMessage()
    ]);
}
