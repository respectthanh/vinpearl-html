<?php
// Include database connection and session management
include_once '../../includes/db_connect.php';
include_once '../../includes/session.php';

// Require user to be logged in
requireLogin();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roomId = $conn->real_escape_string($_POST['room_id']);
    $checkIn = $conn->real_escape_string($_POST['check_in']);
    $checkOut = $conn->real_escape_string($_POST['check_out']);
    $guests = intval($_POST['guests']);
    $totalPrice = floatval($_POST['total_price']);
    $specialRequests = isset($_POST['special_requests']) ? $conn->real_escape_string($_POST['special_requests']) : '';
    $userId = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($roomId) || empty($checkIn) || empty($checkOut) || $guests < 1) {
        $_SESSION['booking_error'] = "All required fields must be filled";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Check if dates are valid
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $today = new DateTime();
    
    if ($checkInDate < $today) {
        $_SESSION['booking_error'] = "Check-in date cannot be in the past";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    if ($checkOutDate <= $checkInDate) {
        $_SESSION['booking_error'] = "Check-out date must be after check-in date";
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
        // Insert into main bookings table first
        $queryBookings = "INSERT INTO bookings (user_id, attraction_name, booking_type, booking_date, number_of_people, special_requests, total_price, status, created_at) 
                VALUES (?, ?, 'ROOM', ?, ?, ?, ?, ?, NOW())";
        $stmtBookings = $conn->prepare($queryBookings);
        // Get room name for attraction_name
        $roomName = "Room Booking"; // Default name
        $roomQuery = "SELECT name_en FROM rooms WHERE id = ?";
        $roomStmt = $conn->prepare($roomQuery);
        $roomStmt->bind_param("s", $roomId);
        $roomStmt->execute();
        $roomResult = $roomStmt->get_result();
        if ($room = $roomResult->fetch_assoc()) {
            $roomName = $room['name_en'];
        }
        $roomStmt->close();
        
        $stmtBookings->bind_param("isssisd", $userId, $roomName, $checkIn, $guests, $specialRequests, $totalPrice, $status);
        $stmtBookings->execute();
        $bookingId = $stmtBookings->insert_id; // Get the ID of the booking we just created
        $stmtBookings->close();
        
        // Insert into room_bookings table with reference to main booking
        $queryRoom = "INSERT INTO room_bookings (id, booking_id, user_id, room_id, check_in, check_out, guests, total_price, special_requests, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmtRoom = $conn->prepare($queryRoom);
        $stmtRoom->bind_param("siisssiiss", $uuid, $bookingId, $userId, $roomId, $checkIn, $checkOut, $guests, $totalPrice, $specialRequests, $status);
        $stmtRoom->execute();
        $stmtRoom->close();
        
        // Commit transaction
        $conn->commit();
        
        // Booking successful
        $_SESSION['booking_success'] = "Your room has been successfully booked!";
        header("Location: /vinpearl-html/pages/profile/bookings.html");
        exit();
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $conn->rollback();
        
        $_SESSION['booking_error'] = "Booking failed: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    // Not a POST request, redirect to rooms page
    header("Location: /vinpearl-html/pages/rooms/index.html");
    exit();
}

$conn->close();
?>