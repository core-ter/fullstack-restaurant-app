<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/email.php';

try {
    $token = isset($_GET['token']) ? trim($_GET['token']) : '';
    
    if (empty($token)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Hiányzó verification token'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    // Find user by verification token
    $stmt = $db->prepare("
        SELECT id, email, first_name, email_verified
        FROM users
        WHERE email_verification_token = ?
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Érvénytelen vagy lejárt verification link'
        ]);
        exit;
    }
    
    if ($user['email_verified']) {
        echo json_encode([
            'success' => true,
            'message' => 'Az email cím már korábban megerősítésre került',
            'already_verified' => true
        ]);
        exit;
    }
    
    // Update user - set verified and clear token
    $updateStmt = $db->prepare("
        UPDATE users 
        SET email_verified = TRUE, 
            email_verification_token = NULL,
            phone_verified = FALSE
        WHERE id = ?
    ");
    $updateStmt->execute([$user['id']]);
    
    // Send welcome email
    sendWelcomeEmail($user['email'], $user['first_name']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Email cím sikeresen megerősítve!'
    ]);
    
} catch (Exception $e) {
    error_log("Email verification error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba történt a megerősítés során'
    ]);
}
