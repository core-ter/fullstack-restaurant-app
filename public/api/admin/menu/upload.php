<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    if (!isset($_FILES['image'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nincs kép feltöltve'
        ]);
        exit;
    }
    
    $file = $_FILES['image'];
    
    // Validate file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Feltöltési hiba: ' . $file['error']);
    }
    
    // Validate file size (2MB max)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'A fájl túl nagy! Maximum 2MB megengedett.'
        ]);
        exit;
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nem engedélyezett fájlformátum! Csak JPEG, PNG, WebP.'
        ]);
        exit;
    }
    
    // Get image dimensions
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Érvénytelen kép fájl'
        ]);
        exit;
    }
    
    // Generate unique filename
    $extension = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'jpg'
    };
    
    $timestamp = time();
    $random = bin2hex(random_bytes(8));
    $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $filename = "{$timestamp}_{$random}_{$safeName}.{$extension}";
    
    // Upload directory
    $uploadDir = __DIR__ . '/../../../uploads/menu/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $targetPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Nem sikerült menteni a fájlt');
    }
    
    // Return relative URL
    $imageUrl = '/uploads/menu/' . $filename;
    
    echo json_encode([
        'success' => true,
        'message' => 'Kép sikeresen feltöltve!',
        'image_url' => $imageUrl,
        'dimensions' => [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1]
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Image upload error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba: ' . $e->getMessage()
    ]);
}
