<?php
// Include database connection
include_once '../../includes/db_connect.php';

// Function to get all active tours
function getTours() {
    global $conn;
    $tours = array();
    
    $query = "SELECT * FROM tours WHERE is_active = 1";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Parse JSON stored fields
            $row['images'] = json_decode($row['images']);
            $row['activities'] = json_decode($row['activities']);
            $tours[] = $row;
        }
    }
    
    return $tours;
}

// If this file is requested directly, return JSON data
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    echo json_encode(getTours());
}
?>