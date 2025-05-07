<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start a session if not already started
session_start();

// Include database connection and session management
include_once '../../includes/db_connect.php';
// include_once '../../includes/session.php';

// Debug log function
function logDebug($message, $data = null) {
    $logFile = 'login_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    
    if ($data !== null) {
        $logMessage .= ': ' . print_r($data, true);
    }
    
    file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
}

logDebug("Login process started");

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    logDebug("POST request received", $_POST);
    
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    logDebug("Processing login for email", $email);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logDebug("Invalid email format");
        $_SESSION['login_error'] = "Invalid email format";
        header("Location: /pages/auth/index.html?login_error=Invalid+email+format");
        exit();
    }
    
    // Check if email exists
    $query = "SELECT * FROM users WHERE email = ?";
    logDebug("Query", $query);
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        logDebug("Prepare statement failed", $conn->error);
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    logDebug("Query execution complete, rows found", $result->num_rows);
    
    if ($result->num_rows === 0) {
        logDebug("No user found with this email");
        $_SESSION['login_error'] = "Email or password is incorrect";
        header("Location: /pages/auth/index.html?login_error=Invalid+credentials");
        exit();
    }
    
    // Get user data
    $user = $result->fetch_assoc();
    logDebug("User data retrieved", $user);
    
    // Verify password
    $passwordVerified = password_verify($password, $user['password']);
    logDebug("Password verification result", $passwordVerified ? "Success" : "Failed");
    
    if (!$passwordVerified) {
        logDebug("Password verification failed");
        $_SESSION['login_error'] = "Email or password is incorrect";
        header("Location: /pages/auth/index.html?login_error=Invalid+credentials");
        exit();
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    
    logDebug("Login successful, session data set", $_SESSION);
    
    // Redirect to homepage or intended destination
    if (isset($_SESSION['redirect_after_login'])) {
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
        logDebug("Redirecting to", $redirect);
        header("Location: $redirect?login_success=true");
    } else {
        logDebug("Redirecting to homepage");
        header("Location: /index.html?login_success=true");
    }
    exit();
} else {
    // Not a POST request, redirect to login page
    logDebug("Not a POST request, redirecting to login page");
    header("Location: /pages/auth/index.html");
    exit();
}

$conn->close();
?>