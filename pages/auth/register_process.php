<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once '../../includes/db_connect.php';
require_once '../../includes/session.php';

// Debug log function for troubleshooting
function logDebug($message, $data = null) {
    $logFile = 'register_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    
    if ($data !== null) {
        $logMessage .= ': ' . print_r($data, true);
    }
    
    file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
}

logDebug("Registration process started");

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    logDebug("POST request received", $_POST);
    
    // Get and sanitize form data
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    
    logDebug("Processing registration for email", $email);
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match";
    }
    
    // If there are validation errors, redirect back with error message
    if (!empty($errors)) {
        logDebug("Validation errors", $errors);
        setFlashMessage(implode(", ", $errors), 'error');
        header("Location: auth.php?mode=register");
        exit();
    }
    
    // Check if email already exists
    $query = "SELECT id FROM users WHERE email = ?";
    logDebug("Checking if email exists", $query);
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        logDebug("Prepare statement failed", $conn->error);
        setFlashMessage("Registration failed: Database error", 'error');
        header("Location: auth.php?mode=register");
        exit();
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email already in use";
        logDebug("Email already exists");
        setFlashMessage("Email already in use", 'error');
        header("Location: auth.php?mode=register");
        exit();
    }
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    logDebug("Password hashed successfully");
    
    // Insert the new user
    $query = "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'USER', NOW())";
    logDebug("Insert query", $query);
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        logDebug("Prepare statement failed", $conn->error);
        setFlashMessage("Registration failed: Database error", 'error');
        header("Location: auth.php?mode=register");
        exit();
    }
    
    $stmt->bind_param("sss", $name, $email, $password_hash);
    $executeResult = $stmt->execute();
    
    logDebug("Execute result", $executeResult ? "Success" : "Failed: " . $stmt->error);
    
    if ($executeResult) {
        // Registration successful
        logDebug("Registration successful, user ID", $conn->insert_id);
        setFlashMessage("Registration successful! You can now login.", 'success');
        header("Location: auth.php");
        exit();
    } else {
        // Registration failed
        logDebug("Registration failed", $stmt->error);
        setFlashMessage("Registration failed. Please try again.", 'error');
        header("Location: auth.php?mode=register");
        exit();
    }
} else {
    // Not a POST request, redirect to registration form
    logDebug("Not a POST request, redirecting to registration form");
    header("Location: auth.php?mode=register");
    exit();
}

$conn->close();
?>