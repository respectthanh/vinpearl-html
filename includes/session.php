<?php
/**
 * Session management functions for Vinpearl Nha Trang Resort
 * 
 * This file provides session management functions for authentication,
 * user management, and security features.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters
    $secure = isset($_SERVER['HTTPS']); // Only send cookies over HTTPS when available
    $httponly = true; // Prevent JavaScript access to session cookie
    
    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 86400, // 24 hours
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Lax' // Help prevent CSRF attacks
    ]);
    
    // Start session
    session_start();
    
    // Regenerate session ID periodically to prevent session fixation
    if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration']) > 1800) {
        // Regenerate session ID every 30 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Check if user is logged in
 * @return bool Whether the user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is an admin
 * @return bool Whether the user is an admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && strtoupper($_SESSION['user_role']) === 'ADMIN';
}

/**
 * Set user session data after successful login
 * @param array $user User data from database
 * @param bool $remember Whether to use remember me functionality
 */
function setUserSession($user, $remember = false) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in_at'] = time();
    
    // Set remember me cookie if requested
    if ($remember) {
        // Create a secure token (this should be stored in the database for real applications)
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        setcookie('remember_me', $token, [
            'expires' => $expires,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        // In a real application, store the token in the database
        // associated with this user with the expiration date
    }
}

/**
 * Destroy user session (logout)
 */
function destroyUserSession() {
    // Unset all session variables
    $_SESSION = [];
    
    // Delete the remember me cookie if it exists
    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', [
            'expires' => time() - 42000,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    // Delete the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax'
            ]
        );
    }

    // Destroy the session
    session_destroy();
}

/**
 * Redirect user based on login state
 * @param string $redirect_if_logged_in URL to redirect if logged in
 * @param string $redirect_if_not_logged_in URL to redirect if not logged in
 */
function redirectBasedOnLoginState($redirect_if_logged_in = null, $redirect_if_not_logged_in = null) {
    if (isLoggedIn() && !is_null($redirect_if_logged_in)) {
        header("Location: $redirect_if_logged_in");
        exit;
    } elseif (!isLoggedIn() && !is_null($redirect_if_not_logged_in)) {
        header("Location: $redirect_if_not_logged_in");
        exit;
    }
}

/**
 * Require user to be logged in, redirect to login page if not
 * @param string $redirect_url URL to redirect if not logged in
 */
function requireLogin($redirect_url = '/pages/auth/auth.php') {
    if (!isLoggedIn()) {
        // Save current URL for redirect back after login
        if (!empty($_SERVER['REQUEST_URI'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        header("Location: $redirect_url");
        exit;
    }
}

/**
 * Require user to be admin, redirect if not
 * @param string $redirect_url URL to redirect if not admin
 */
function requireAdmin($redirect_url = '/index.php') {
    requireLogin();
    
    if (!isAdmin()) {
        header("Location: $redirect_url");
        exit;
    }
}

/**
 * Get logged in user ID
 * @return int|null User ID if logged in, null otherwise
 */
function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current user information
 * @return array|null User information if logged in, null otherwise
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}

/**
 * Set a flash message to be displayed once
 * @param string $message The message to flash
 * @param string $type The type of message (success, error, info, warning)
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get and clear flash message
 * @return array|null The flash message array [message, type] or null if no message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = [$_SESSION['flash_message']['type'], $_SESSION['flash_message']['message']];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * CSRF token generation and validation
 */

/**
 * Generate a CSRF token and store it in the session
 * @param string $form_name Form name to tie the token to (optional)
 * @return string Generated CSRF token
 */
function generateCSRFToken($form_name = 'default') {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_tokens'][$form_name] = [
        'token' => $token,
        'time' => time()
    ];
    return $token;
}

/**
 * Validate a CSRF token
 * @param string $token Token to validate
 * @param string $form_name Form name the token is tied to (optional)
 * @return bool Whether the token is valid
 */
function validateCSRFToken($token, $form_name = 'default') {
    // Token lifetime is 1 hour
    $lifetime = 3600;
    
    if (!isset($_SESSION['csrf_tokens'][$form_name])) {
        return false;
    }
    
    $stored = $_SESSION['csrf_tokens'][$form_name];
    
    // Check if token has expired
    if (time() - $stored['time'] > $lifetime) {
        unset($_SESSION['csrf_tokens'][$form_name]);
        return false;
    }
    
    // Check if token matches
    if (hash_equals($stored['token'], $token)) {
        // Consume the token (single use)
        unset($_SESSION['csrf_tokens'][$form_name]);
        return true;
    }
    
    return false;
}
?>