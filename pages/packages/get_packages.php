<?php
// Include database connection
include_once '../../includes/db_connect.php';

// Function to get all active packages
function getPackages() {
    global $conn;
    $packages = array();
    
    $query = "SELECT * FROM packages WHERE is_active = 1";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Parse JSON stored fields
            $row['images'] = json_decode($row['images']);
            $row['inclusions'] = json_decode($row['inclusions']);
            $packages[] = $row;
        }
    }
    
    return $packages;
}

// If this file is requested directly, return JSON data
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    echo json_encode(getPackages());
}
?>