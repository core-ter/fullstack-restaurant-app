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
    
    $currentPassword = $input['current_password'] ?? '';
    $newPassword = $input['new_password'] ?? '';
    $confirmPassword = $input['confirm_password'] ?? '';
    
    // Validation
    if (empty($currentPassword)) {
        throw new Exception('Jelenlegi jelszó megadása kötelező');
    }
    
    if (empty($newPassword) || strlen($newPassword) < 6) {
        throw new Exception('Az új jelszó legalább 6 karakter hosszú legyen');
    }
    
    if ($newPassword !== $confirmPassword) {
        throw new Exception('Az új jelszavak nem egyeznek');
    }
    
    $db = getDBConnection();
    
    // Get current user
    $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('Felhasználó nem található');
    }
    
    // Verify current password
    if (!password_verify($currentPassword, $user['password_hash'])) {
        throw new Exception('Hibás jelenlegi jelszó');
    }
    
    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    
    // Update password
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $userId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Jelszó sikeresen megváltoztatva'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
