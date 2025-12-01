<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Bejelentkezés szükséges']);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate inputs
    $firstName = trim($input['first_name'] ?? '');
    $lastName = trim($input['last_name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    
    if (empty($firstName) || strlen($firstName) < 2) {
        throw new Exception('Keresztnév legalább 2 karakter hosszú legyen');
    }
    
    if (empty($lastName) || strlen($lastName) < 2) {
        throw new Exception('Vezetéknév legalább 2 karakter hosszú legyen');
    }
    
    $db = getDBConnection();
    
    // Update user
    $stmt = $db->prepare("
        UPDATE users 
        SET first_name = ?, last_name = ?, phone = ?
        WHERE id = ?
    ");
    
    $stmt->execute([$firstName, $lastName, $phone, $userId]);
    
    // Update session
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    
    echo json_encode([
        'success' => true,
        'message' => 'Profil sikeresen frissítve'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
