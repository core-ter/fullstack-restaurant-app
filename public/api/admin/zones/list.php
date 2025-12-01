<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    $db = getDBConnection();
    
    $stmt = $db->query("
        SELECT 
            id,
            distance_from_km,
            distance_to_km,
            fee,
            delivery_time_minutes,
            created_at
        FROM delivery_zones
        ORDER BY distance_from_km ASC
    ");
    
    $zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'zones' => $zones
    ]);
    
} catch (Exception $e) {
    error_log("Zones list error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba: ' . $e->getMessage()
    ]);
}
