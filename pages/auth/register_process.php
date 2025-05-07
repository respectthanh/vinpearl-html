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
    
    // Get form data
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    
    logDebug("Processing registration for email", $email);
    
    // Validate form data
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    // Validate password confirmation
    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match";
    }
    
    logDebug("Validation results", empty($errors) ? "Passed" : $errors);
    
    // Check if email already exists
    $query = "SELECT id FROM users WHERE email = ?";
    logDebug("Checking if email exists", $query);
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        logDebug("Prepare statement failed", $conn->error);
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email already in use";
        logDebug("Email already exists");
    }
    
    // If there are errors, redirect back to the registration form
    if (!empty($errors)) {
        logDebug("Registration failed due to validation errors", $errors);
        $_SESSION['register_errors'] = $errors;
        header("Location: /pages/auth/index.html?register_error=" . urlencode(implode(", ", $errors)));
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
        $_SESSION['register_error'] = "Registration failed: " . $conn->error;
        header("Location: /pages/auth/index.html?register_error=Database+error");
        exit();
    }
    
    $role = 'USER';
    $stmt->bind_param("sss", $name, $email, $password_hash);
    $executeResult = $stmt->execute();
    
    logDebug("Execute result", $executeResult ? "Success" : "Failed: " . $stmt->error);
    
    if ($executeResult) {
        // Registration successful
        logDebug("Registration successful, user ID", $conn->insert_id);
        $_SESSION['register_success'] = true;
        header("Location: /pages/auth/index.html?register_success=true");
        exit();
    } else {
        // Registration failed
        logDebug("Registration failed", $stmt->error);
        $_SESSION['register_error'] = "Registration failed. Please try again.";
        header("Location: /pages/auth/index.html?register_error=Registration+failed.+Please+try+again.");
        exit();
    }
} else {
    // Not a POST request, redirect to registration form
    logDebug("Not a POST request, redirecting to registration form");
    header("Location: /pages/auth/index.html");
    exit();
}

$conn->close();
?>