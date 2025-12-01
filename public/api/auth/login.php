<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email és jelszó megadása kötelező'
        ]);
        exit;
    }
    
    $email = trim($input['email']);
    $password = $input['password'];
    
    // Verify reCAPTCHA token
    if (defined('RECAPTCHA_SECRET_KEY') && !empty(RECAPTCHA_SECRET_KEY)) {
        if (empty($input['recaptcha_token'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'reCAPTCHA ellenőrzés szükséges'
            ]);
            exit;
        }

        $recaptchaResponse = @file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . RECAPTCHA_SECRET_KEY . 
            '&response=' . $input['recaptcha_token']
        );
        
        if ($recaptchaResponse) {
            $recaptchaData = json_decode($recaptchaResponse);
            
            if (!$recaptchaData->success || $recaptchaData->score < RECAPTCHA_MIN_SCORE) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'reCAPTCHA ellenőrzés sikertelen. Kérjük, próbáld újra.'
                ]);
                exit;
            }
        }
    }
    
    $db = getDBConnection();
    
    // Find user by email
    $stmt = $db->prepare("
        SELECT id, email, password_hash, first_name, last_name, phone, email_verified
        FROM users
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Hibás email vagy jelszó'
        ]);
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Hibás email vagy jelszó'
        ]);
        exit;
    }
    
    // Check if email is verified
    if (!$user['email_verified']) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Kérjük, először erősítsd meg az email címed! Ellenőrizd a postafiókodat.',
            'email_not_verified' => true
        ]);
        exit;
    }
    
    // Update last login
    $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$user['id']]);
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    
    // Return user data (without password hash)
    echo json_encode([
        'success' => true,
        'message' => 'Sikeres bejelentkezés!',
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'phone' => $user['phone'],
            'email_verified' => (bool)$user['email_verified']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba történt a bejelentkezés során'
    ]);
}
