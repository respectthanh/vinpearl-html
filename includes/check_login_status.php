<?php
// Include session management
include_once 'session.php';

// Set appropriate headers for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (isLoggedIn()) {
    // Return logged in status and user info
    echo json_encode([
        'isLoggedIn' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ]
    ]);
} else {
    // Return not logged in status
    echo json_encode([
        'isLoggedIn' => false
    ]);
}
?>