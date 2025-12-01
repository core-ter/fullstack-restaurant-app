<?php
/**
 * Database Configuration
 * Loads environment variables and establishes database connection
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        die('.env file not found. Please copy .env.example to .env and configure it.');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Set environment variable
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Load .env file from project root
loadEnv(__DIR__ . '/../../../.env');

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'restaurant_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Application configuration
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// reCAPTCHA configuration
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '');
define('RECAPTCHA_SECRET_KEY', getenv('RECAPTCHA_SECRET_KEY') ?: '');
define('RECAPTCHA_MIN_SCORE', floatval(getenv('RECAPTCHA_MIN_SCORE') ?: 0.5));

/**
 * Get PDO Database Connection
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('Database connection failed. Please try again later.');
            }
        }
    }
    
    return $pdo;
}

/**
 * Execute a query and return results
 * @param string $query SQL query
 * @param array $params Query parameters
 * @return array
 */
function dbQuery($query, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Execute a query and return single row
 * @param string $query SQL query
 * @param array $params Query parameters
 * @return array|false
 */
function dbQueryOne($query, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch();
}

/**
 * Execute an INSERT/UPDATE/DELETE query
 * @param string $query SQL query
 * @param array $params Query parameters
 * @return int Number of affected rows or last insert ID
 */
function dbExecute($query, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    // Return last insert ID for INSERT queries
    if (stripos(trim($query), 'INSERT') === 0) {
        return $pdo->lastInsertId();
    }
    
    return $stmt->rowCount();
}
