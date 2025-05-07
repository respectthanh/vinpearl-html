<?php
// Include database connection and session management
include_once '../../includes/db_connect.php';
include_once '../../includes/session.php';

// Require user to be logged in
requireLogin();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tourId = $conn->real_escape_string($_POST['tour_id']);
    $tourDate = $conn->real_escape_string($_POST['tour_date']);
    $participants = intval($_POST['participants']);
    $totalPrice = floatval($_POST['total_price']);
    $userId = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($tourId) || empty($tourDate) || $participants < 1) {
        $_SESSION['booking_error'] = "All required fields must be filled";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Check if date is valid
    $tourDateTime = new DateTime($tourDate);
    $today = new DateTime();
    
    if ($tourDateTime < $today) {
        $_SESSION['booking_error'] = "Tour date cannot be in the past";
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
    
    // Insert booking into database using a transaction
    $conn->begin_transaction();
    
    try {
        // Insert into tour_bookings table
        $queryTour = "INSERT INTO tour_bookings (id, user_id, tour_id, tour_date, participants, total_price, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmtTour = $conn->prepare($queryTour);
        $stmtTour->bind_param("ssssiis", $uuid, $userId, $tourId, $tourDate, $participants, $totalPrice, $status);
        $stmtTour->execute();
        $stmtTour->close();
        
        // Commit transaction
        $conn->commit();
        
        // Booking successful
        $_SESSION['booking_success'] = "Your tour has been successfully booked!";
        header("Location: /vinpearl-html/pages/profile/index.html");
        exit();
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $conn->rollback();
        
        $_SESSION['booking_error'] = "Booking failed: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    // Not a POST request, redirect to tours page
    header("Location: /vinpearl-html/pages/tours/index.html");
    exit();
}

$conn->close();
?>