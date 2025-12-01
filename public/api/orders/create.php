<?php
/**
 * Create Order API Endpoint
 * Handles order creation and saves to database
 */

header('Content-Type: application/json; charset=UTF-8');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Verify reCAPTCHA token
    if (!empty($input['recaptcha_token']) && !empty(RECAPTCHA_SECRET_KEY)) {
        $recaptchaResponse = @file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . RECAPTCHA_SECRET_KEY . 
            '&response=' . $input['recaptcha_token']
        );
        
        if ($recaptchaResponse) {
            $recaptchaData = json_decode($recaptchaResponse);
            
            if (!$recaptchaData->success || $recaptchaData->score < RECAPTCHA_MIN_SCORE) {
                throw new Exception('reCAPTCHA verification failed. Please try again.');
            }
        }
    }
    
    // Validate required fields
    $required = ['customer_name', 'customer_phone', 'customer_email', 'delivery_address', 'items', 'total'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate items array
    if (!is_array($input['items']) || count($input['items']) === 0) {
        throw new Exception('Cart is empty');
    }
    
    // Split customer_name into first/last name
    $nameParts = explode(' ', trim($input['customer_name']), 2);
    $firstName = $nameParts[0];
    $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
    
    // Apply free delivery for orders >= 5000 Ft
    $FREE_DELIVERY_THRESHOLD = 5000;
    $deliveryFee = $input['delivery_fee'];
    
    if ($input['subtotal'] >= $FREE_DELIVERY_THRESHOLD) {
        $deliveryFee = 0;
    }
    
    // Recalculate total
    $totalAmount = $input['subtotal'] + $deliveryFee;
    
    // Get database connection
    $db = getDBConnection();
    
    // Generate unique order number
    $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Get pending status ID
    $stmt = $db->prepare("SELECT id FROM order_statuses WHERE status_code = 'pending' LIMIT 1");
    $stmt->execute();
    $statusRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $statusId = $statusRow ? $statusRow['id'] : 1;
    
    // Check if user is logged in
    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);
    $userId = $isLoggedIn ? $_SESSION['user_id'] : null;
    $isGuest = !$isLoggedIn;

    // First Order Discount Logic
    $discountAmount = 0;
    if ($isLoggedIn) {
        // Verify if user is eligible for first order discount
        $stmt = $db->prepare("SELECT is_first_order FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['is_first_order']) {
            $discountAmount = round($input['subtotal'] * 0.1); // 10% discount
        }
    }
    
    // Recalculate total
    $totalAmount = $input['subtotal'] + $deliveryFee - $discountAmount;
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Update user's is_first_order status if discount was applied
        if ($discountAmount > 0) {
            $stmt = $db->prepare("UPDATE users SET is_first_order = FALSE WHERE id = ?");
            $stmt->execute([$userId]);
        }

        // Insert order
        $stmt = $db->prepare("
            INSERT INTO orders (
                order_number,
                user_id,
                is_guest,
                guest_first_name,
                guest_last_name,
                guest_phone,
                guest_email,
                delivery_address,
                delivery_latitude,
                delivery_longitude,
                distance_km,
                subtotal,
                delivery_fee,
                discount_amount,
                total_amount,
                status_id,
                customer_notes,
                created_at
            ) VALUES (
                :order_number,
                :user_id,
                :is_guest,
                :first_name,
                :last_name,
                :phone,
                :email,
                :delivery_address,
                :delivery_lat,
                :delivery_lng,
                :distance_km,
                :subtotal,
                :delivery_fee,
                :discount_amount,
                :total_amount,
                :status_id,
                :notes,
                NOW()
            )
        ");
        
        $stmt->execute([
            ':order_number' => $orderNumber,
            ':user_id' => $userId,
            ':is_guest' => $isGuest ? 1 : 0,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':phone' => $input['customer_phone'],
            ':email' => $input['customer_email'],
            ':delivery_address' => $input['delivery_address'],
            ':delivery_lat' => $input['delivery_lat'] ?? null,
            ':delivery_lng' => $input['delivery_lng'] ?? null,
            ':distance_km' => $input['distance_km'] ?? 0,
            ':subtotal' => $input['subtotal'],
            ':delivery_fee' => $deliveryFee,
            ':discount_amount' => $discountAmount,
            ':total_amount' => $totalAmount,
            ':status_id' => $statusId,
            ':notes' => $input['notes'] ?? ''
        ]);
        
        $orderId = $db->lastInsertId();
        
        // Insert order items
        $stmt = $db->prepare("
            INSERT INTO order_items (
                order_id,
                menu_item_id,
                item_name,
                item_price,
                quantity
            ) VALUES (
                :order_id,
                :menu_item_id,
                :item_name,
                :item_price,
                :quantity
            )
        ");
        
        foreach ($input['items'] as $item) {
            $stmt->execute([
                ':order_id' => $orderId,
                ':menu_item_id' => $item['menu_item_id'],
                ':item_name' => $item['title'] ?? $item['name'] ?? 'Unknown item',
                ':item_price' => $item['price'],
                ':quantity' => $item['quantity']
            ]);
        }
        
        // Commit transaction
        $db->commit();
        
        // Send order confirmation email (optional - won't fail order if email fails)
        try {
            require_once __DIR__ . '/../helpers/email.php';
            $emailSent = sendOrderConfirmationEmail(
                $input['customer_email'],
                $firstName,
                $orderNumber,
                [
                    'items' => $input['items'],
                    'delivery_fee' => $deliveryFee,
                    'total' => $totalAmount,
                    'delivery_address' => $input['delivery_address'],
                    'delivery_time' => $input['delivery_time'] ?? 30
                ]
            );
        } catch (Exception $emailError) {
            error_log("Failed to send order confirmation email: " . $emailError->getMessage());
        }
        
        // Return success
        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'order_number' => $orderNumber,
            'message' => 'Order created successfully'
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // Rollback on error
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
