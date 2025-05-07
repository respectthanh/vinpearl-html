<?php
// Include database connection and session management
require_once '../includes/db_connect.php';
require_once '../includes/session.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    // Return error if not logged in
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required',
        'redirect' => '../pages/auth/login.php'
    ]);
    exit;
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Check if required parameters are provided
if (!isset($_POST['booking_id']) || !isset($_POST['cancel_booking'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

// Get user info
$user = getCurrentUser();
$user_id = $user['id'];
$booking_id = intval($_POST['booking_id']);

// Function to cancel a booking
function cancelBooking($conn, $booking_id, $user_id) {
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Check if booking exists and belongs to user
        $checkQuery = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $booking_id, $user_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            // No booking found
            $checkStmt->close();
            $conn->rollback();
            return [
                'success' => false,
                'message' => 'Booking not found or not owned by current user'
            ];
        }
        
        $booking = $result->fetch_assoc();
        $checkStmt->close();
        
        // Check if booking can be cancelled
        if ($booking['status'] === 'COMPLETED') {
            $conn->rollback();
            return [
                'success' => false,
                'message' => 'Cannot cancel a completed booking'
            ];
        }
        
        if ($booking['status'] === 'CANCELLED') {
            $conn->rollback();
            return [
                'success' => false,
                'message' => 'Booking is already cancelled'
            ];
        }
        
        // Update booking status to CANCELLED
        $updateQuery = "UPDATE bookings SET status = 'CANCELLED', cancelled_at = NOW() WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $booking_id);
        $updateStmt->execute();
        $updateStmt->close();
        
        // Based on booking type, handle specific cancellation logic
        if ($booking['booking_type'] === 'ROOM') {
            // Release the room inventory
            $releaseQuery = "UPDATE room_inventory SET is_booked = 0 WHERE booking_id = ?";
            $releaseStmt = $conn->prepare($releaseQuery);
            $releaseStmt->bind_param("i", $booking_id);
            $releaseStmt->execute();
            $releaseStmt->close();
        } elseif ($booking['booking_type'] === 'TOUR') {
            // Release tour seats
            $releaseQuery = "UPDATE tour_inventory SET available_seats = available_seats + (SELECT number_of_people FROM tour_bookings WHERE booking_id = ?) WHERE tour_id = (SELECT tour_id FROM tour_bookings WHERE booking_id = ?)";
            $releaseStmt = $conn->prepare($releaseQuery);
            $releaseStmt->bind_param("ii", $booking_id, $booking_id);
            $releaseStmt->execute();
            $releaseStmt->close();
        }
        
        // Create refund record if applicable
        $refundQuery = "INSERT INTO refunds (booking_id, refund_amount, refund_date, status) VALUES (?, ?, NOW(), 'PROCESSED')";
        $refundStmt = $conn->prepare($refundQuery);
        $refundAmount = $booking['total_price']; // In a real system, this would apply cancellation policy rules
        $refundStmt->bind_param("id", $booking_id, $refundAmount);
        $refundStmt->execute();
        $refundStmt->close();
        
        // Commit the transaction
        $conn->commit();
        
        return [
            'success' => true,
            'message' => 'Booking successfully cancelled and refund processed'
        ];
    } catch (Exception $e) {
        // Roll back transaction on error
        $conn->rollback();
        return [
            'success' => false,
            'message' => 'Error cancelling booking: ' . $e->getMessage()
        ];
    }
}

try {
    // Cancel the booking
    $result = cancelBooking($conn, $booking_id, $user_id);
    
    // Set flash message for page reload
    if ($result['success']) {
        setFlashMessage('success', $result['message']);
    } else {
        setFlashMessage('error', $result['message']);
    }
    
    // Return result as JSON
    echo json_encode($result);
    
} catch (Exception $e) {
    // Return error
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?> 