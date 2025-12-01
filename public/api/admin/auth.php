<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../../api/config/database.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// Login
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    
    // Admin credentials (hardcoded for now)
    // TODO: Move to proper config/environment setup
    $adminUsername = 'admin';
    // Regenerate hash for admin123 to be sure
    $adminPasswordHash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Debug logging (REMOVE IN PRODUCTION!)
    error_log("Login attempt - Username: $username, Password length: " . strlen($password));
    error_log("Expected username: $adminUsername");
    error_log("Password verify result: " . (password_verify($password, $adminPasswordHash) ? 'TRUE' : 'FALSE'));
    
    if ($username === $adminUsername && password_verify($password, $adminPasswordHash)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_login_time'] = time();
        
        echo json_encode([
            'success' => true,
            'message' => 'Sikeres bejelentkezés'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Hibás felhasználónév vagy jelszó'
        ]);
    }
    exit;
}

// Logout
if ($action === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    session_destroy();
    echo json_encode([
        'success' => true,
        'message' => 'Sikeres kijelentkezés'
    ]);
    exit;
}

// Check session
if ($action === 'check' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true,
        'logged_in' => isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true
    ]);
    exit;
}

http_response_code(400);
echo json_encode([
    'success' => false,
    'message' => 'Invalid action'
]);
