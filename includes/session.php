<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Set user session data after successful login
 * @param array $user User data from database
 */
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['logged_in_at'] = time();
}

/**
 * Destroy user session (logout)
 */
function destroyUserSession() {
    // Unset all session variables
    $_SESSION = [];

    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
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
function requireLogin($redirect_url = '/pages/auth/login.php') {
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
    if (!isAdmin()) {
        header("Location: $redirect_url");
        exit;
    }
}

/**
 * Get logged in user ID
 * @return string|null User ID if logged in, null otherwise
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
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
?>