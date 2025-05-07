<?php
// Include session management
include_once 'session.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    // Not an admin, redirect to login page
    header("Location: /vinpearl-html/pages/auth/index.html");
    exit();
}
?>