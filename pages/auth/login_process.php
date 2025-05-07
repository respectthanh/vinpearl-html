<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once '../../includes/db_connect.php';
require_once '../../includes/session.php';

// Debug log function for troubleshooting
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

// Verify if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    logDebug("POST request received", $_POST);
    
    // Get and sanitize form data
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) && $_POST['remember'] == 'on';
    
    logDebug("Processing login for email", $email);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logDebug("Invalid email format");
        setFlashMessage('Invalid email format', 'error');
        header("Location: auth.php");
        exit();
    }
    
    // Check if email exists in database
    $query = "SELECT * FROM users WHERE email = ?";
    logDebug("Query", $query);
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        logDebug("Prepare statement failed", $conn->error);
        setFlashMessage('System error. Please try again later.', 'error');
        header("Location: auth.php");
        exit();
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    logDebug("Query execution complete, rows found", $result->num_rows);
    
    if ($result->num_rows === 0) {
        // No user found with that email
        logDebug("No user found with this email");
        setFlashMessage('Invalid email or password', 'error');
        header("Location: auth.php");
        exit();
    }
    
    // User exists, verify password
    $user = $result->fetch_assoc();
    logDebug("User data retrieved", $user);
    
    if (!password_verify($password, $user['password'])) {
        // Password does not match
        logDebug("Password verification failed");
        setFlashMessage('Invalid email or password', 'error');
        header("Location: auth.php");
        exit();
    }
    
    // All checks passed, user is authenticated
    // Set session variables using the function from session.php
    setUserSession($user, $remember);
    
    logDebug("Login successful, session data set", $_SESSION);
    
    // Redirect to the home page or intended destination
    if (isset($_SESSION['redirect_after_login'])) {
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
        logDebug("Redirecting to", $redirect);
        header("Location: $redirect");
    } else {
        logDebug("Redirecting to homepage");
        header("Location: ../../index.html"); // Redirect to HTML homepage
    }
    exit();
} else {
    // Not a POST request, redirect to login page
    logDebug("Not a POST request, redirecting to login page");
    header("Location: auth.php");
    exit();
}

$conn->close();
?>