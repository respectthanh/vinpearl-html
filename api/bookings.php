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

// Get user info
$user = getCurrentUser();
$user_id = $user['id'];

// Function to get all bookings for a user
function getUserBookings($conn, $user_id) {
    $query = "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        // Process additional data based on booking type
        if ($row['booking_type'] === 'ROOM') {
            // Get room details
            $roomQuery = "SELECT * FROM room_bookings WHERE booking_id = ?";
            $roomStmt = $conn->prepare($roomQuery);
            $roomStmt->bind_param("i", $row['id']);
            $roomStmt->execute();
            $roomResult = $roomStmt->get_result();
            
            if ($roomData = $roomResult->fetch_assoc()) {
                $row = array_merge($row, $roomData);
            }
            
            $roomStmt->close();
        } elseif ($row['booking_type'] === 'TOUR') {
            // Get tour details
            $tourQuery = "SELECT * FROM tour_bookings WHERE booking_id = ?";
            $tourStmt = $conn->prepare($tourQuery);
            $tourStmt->bind_param("i", $row['id']);
            $tourStmt->execute();
            $tourResult = $tourStmt->get_result();
            
            if ($tourData = $tourResult->fetch_assoc()) {
                $row = array_merge($row, $tourData);
            }
            
            $tourStmt->close();
        }
        
        $bookings[] = $row;
    }
    
    $stmt->close();
    return $bookings;
}

try {
    // Get user's bookings
    $bookings = getUserBookings($conn, $user_id);
    
    // Get flash message if any
    $flash_message = getFlashMessage();
    
    // Return data as JSON
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ],
        'bookings' => $bookings,
        'flash_message' => $flash_message ? [
            'type' => $flash_message[0],
            'message' => $flash_message[1]
        ] : null
    ]);
    
} catch (Exception $e) {
    // Return error
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching bookings: ' . $e->getMessage()
    ]);
}
?> 