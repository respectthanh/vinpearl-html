<?php
// Include database connection
require_once 'db_connect.php';
require_once 'session.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'booking_id' => null
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// If standard form submission, use $_POST
if (empty($data)) {
    $data = $_POST;
}

// Validate required fields
$required_fields = ['attraction_name', 'booking_type', 'booking_date', 'number_of_people'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        $response['message'] = "Missing required field: {$field}";
        echo json_encode($response);
        exit;
    }
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please login to book a tour';
    $response['require_login'] = true;
    echo json_encode($response);
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];

// Sanitize data
$attraction_name = $conn->real_escape_string($data['attraction_name']);
$booking_type = $conn->real_escape_string($data['booking_type']);
$booking_date = $conn->real_escape_string($data['booking_date']);
$number_of_people = intval($data['number_of_people']);
$special_requests = isset($data['special_requests']) ? $conn->real_escape_string($data['special_requests']) : '';
$total_price = isset($data['total_price']) ? floatval($data['total_price']) : 0;

// Current timestamp
$created_at = date('Y-m-d H:i:s');

// Insert booking into database
$query = "INSERT INTO bookings (user_id, attraction_name, booking_type, booking_date, number_of_people, special_requests, total_price, status, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING', ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("isssisss", $user_id, $attraction_name, $booking_type, $booking_date, $number_of_people, $special_requests, $total_price, $created_at);

if ($stmt->execute()) {
    $booking_id = $stmt->insert_id;
    
    $response['success'] = true;
    $response['message'] = 'Booking created successfully';
    $response['booking_id'] = $booking_id;
} else {
    $response['message'] = 'Error creating booking: ' . $stmt->error;
}

// Close statement
$stmt->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 