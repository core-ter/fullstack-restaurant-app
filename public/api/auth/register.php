<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['email', 'password', 'first_name', 'last_name'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Minden kötelező mező kitöltése szükséges'
            ]);
            exit;
        }
    }
    
    $email = trim($input['email']);
    $password = $input['password'];
    $firstName = trim($input['first_name']);
    $lastName = trim($input['last_name']);
    $phone = isset($input['phone']) ? trim($input['phone']) : null;
    
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
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Érvénytelen email cím formátum'
        ]);
        exit;
    }
    
    // Validate password length
    if (strlen($password) < 8) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie'
        ]);
        exit;
    }
    
    $db = getDBConnection();
    
    // Check if email already exists
    $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ez az email cím már regisztrálva van'
        ]);
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Generate email verification token
    $verificationToken = bin2hex(random_bytes(32));
    
    // Insert user
    $stmt = $db->prepare("
        INSERT INTO users (
            email, password_hash, first_name, last_name, phone,
            email_verification_token, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $email,
        $passwordHash,
        $firstName,
        $lastName,
        $phone,
        $verificationToken
    ]);
    
    $userId = $db->lastInsertId();
    
    // Send verification email (optional - won't fail registration if email fails)
    $emailSent = false;
    try {
        require_once __DIR__ . '/../helpers/email.php';
        $emailSent = sendVerificationEmail($email, $firstName, $verificationToken);
    } catch (Exception $emailError) {
        error_log("Failed to send verification email: " . $emailError->getMessage());
    }
    
    // Create session
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
    
    // Return user data (without sensitive info)
    echo json_encode([
        'success' => true,
        'message' => $emailSent 
            ? 'Sikeres regisztráció! Ellenőrizd az email fiókodat a megerősítéshez.' 
            : 'Sikeres regisztráció!',
        'user' => [
            'id' => $userId,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'email_verified' => false
        ],
        'email_sent' => $emailSent
    ]);
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba történt a regisztráció során'
    ]);
}
