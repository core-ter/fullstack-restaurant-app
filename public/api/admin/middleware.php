<?php
/**
 * Admin Authentication Middleware
 * Protects admin pages from unauthorized access
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if admin is logged in
 * @return bool
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin authentication
 * Redirects to login page if not authenticated
 */
function requireAuth() {
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Get logged in admin username
 * @return string|null
 */
function getAdminUsername() {
    return $_SESSION['admin_username'] ?? null;
}
