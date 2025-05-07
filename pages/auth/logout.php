<?php
// Include session management
require_once '../../includes/session.php';

// Log the logout event (for security monitoring)
if (isLoggedIn()) {
    $user = getCurrentUser();
    $ip = $_SERVER['REMOTE_ADDR'];
    error_log("User {$user['email']} logged out from IP $ip");
}

// Set a flash message confirming logout
setFlashMessage('You have been successfully logged out.', 'success');

// Destroy the session
destroyUserSession();

// Redirect to homepage
header("Location: ../../index.php");
exit();
?>