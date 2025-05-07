<?php
// Include admin authentication check
include_once '../../includes/admin_auth.php';
include_once '../../includes/db_connect.php';

// Function to get all bookings with customer and room details
function getAllBookings() {
    global $conn;
    $bookings = array();
    
    // Get room bookings with user and room details
    $query = "SELECT b.*, u.name as user_name, u.email as user_email, 
              r.name_en as room_name, r.price as room_price 
              FROM bookings b 
              JOIN users u ON b.user_id = u.id 
              JOIN rooms r ON b.room_id = r.id 
              ORDER BY b.created_at DESC";
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $row['booking_type'] = 'room';
            $bookings[] = $row;
        }
    }
    
    // Get tour bookings with user and tour details
    $query = "SELECT tb.*, u.name as user_name, u.email as user_email, 
              t.name_en as tour_name, t.price as tour_price 
              FROM tour_bookings tb 
              JOIN users u ON tb.user_id = u.id 
              JOIN tours t ON tb.tour_id = t.id 
              ORDER BY tb.created_at DESC";
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $row['booking_type'] = 'tour';
            $bookings[] = $row;
        }
    }
    
    // Get package bookings with user and package details
    $query = "SELECT pb.*, u.name as user_name, u.email as user_email, 
              p.name_en as package_name, p.price as package_price 
              FROM package_bookings pb 
              JOIN users u ON pb.user_id = u.id 
              JOIN packages p ON pb.package_id = p.id 
              ORDER BY pb.created_at DESC";
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $row['booking_type'] = 'package';
            $bookings[] = $row;
        }
    }
    
    // Sort all bookings by created_at (newest first)
    usort($bookings, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    return $bookings;
}

// If this file is requested directly, return JSON data
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    echo json_encode(getAllBookings());
}
?>