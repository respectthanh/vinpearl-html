<?php
// Include database connection and session management
include_once '../../includes/db_connect.php';
include_once '../../includes/session.php';

// Require user to be logged in
requireLogin();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $packageId = $conn->real_escape_string($_POST['package_id']);
    $startDate = $conn->real_escape_string($_POST['start_date']);
    $adults = intval($_POST['adults']);
    $children = isset($_POST['children']) ? intval($_POST['children']) : 0;
    $totalPrice = floatval($_POST['total_price']);
    $userId = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($packageId) || empty($startDate) || $adults < 1) {
        $_SESSION['booking_error'] = "All required fields must be filled";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Check if date is valid
    $startDateTime = new DateTime($startDate);
    $today = new DateTime();
    
    if ($startDateTime < $today) {
        $_SESSION['booking_error'] = "Start date cannot be in the past";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Generate UUID for booking ID
    $uuid = bin2hex(random_bytes(16));
    $uuid = substr($uuid, 0, 8) . '-' . 
            substr($uuid, 8, 4) . '-' . 
            substr($uuid, 12, 4) . '-' . 
            substr($uuid, 16, 4) . '-' . 
            substr($uuid, 20);
    
    // Set booking status
    $status = 'CONFIRMED';
    
    // Insert booking into database
    $query = "INSERT INTO package_bookings (id, user_id, package_id, start_date, adults, children, total_price, status, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssiids", $uuid, $userId, $packageId, $startDate, $adults, $children, $totalPrice, $status);
    
    if ($stmt->execute()) {
        // Booking successful
        $_SESSION['booking_success'] = "Your package has been successfully booked!";
        header("Location: /vinpearl-html/pages/profile/index.html");
        exit();
    } else {
        // Booking failed
        $_SESSION['booking_error'] = "Booking failed: " . $conn->error;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    $stmt->close();
} else {
    // Not a POST request, redirect to packages page
    header("Location: /vinpearl-html/pages/packages/index.html");
    exit();
}

$conn->close();
?>