<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../config/database.php';
    
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("
            SELECT id, email, first_name, last_name, phone, email_verified, is_first_order
            FROM users
            WHERE id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo json_encode([
                'success' => true,
                'logged_in' => true,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'phone' => $user['phone'],
                    'email_verified' => (bool)$user['email_verified'],
                    'is_first_order' => (bool)$user['is_first_order']
                ]
            ]);
        } else {
            session_destroy();
            echo json_encode([
                'success' => true,
                'logged_in' => false
            ]);
        }
    } catch (Exception $e) {
        error_log("Check session error: " . $e->getMessage());
        echo json_encode([
            'success' => true,
            'logged_in' => false
        ]);
    }
} else {
    echo json_encode([
        'success' => true,
        'logged_in' => false
    ]);
}
