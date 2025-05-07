<?php
// Include database connection
require_once 'db_connect.php';
require_once 'session.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: ../pages/auth/index.html');
    exit;
}

// Get user information
$user_id = $_SESSION['user_id'];

// Check if requesting a specific booking
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Function to get all bookings for a user
function getUserBookings($conn, $user_id) {
    $query = "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    $stmt->close();
    return $bookings;
}

// Function to get a specific booking
function getBooking($conn, $booking_id, $user_id) {
    $query = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $booking = $result->fetch_assoc();
    $stmt->close();
    
    return $booking;
}

// Function to cancel a booking
function cancelBooking($conn, $booking_id, $user_id) {
    $query = "UPDATE bookings SET status = 'CANCELLED' WHERE id = ? AND user_id = ? AND status != 'COMPLETED'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    
    $affected = $stmt->affected_rows;
    $stmt->close();
    
    return $affected > 0;
}

// Handle booking cancellation
if (isset($_POST['cancel_booking']) && isset($_POST['booking_id'])) {
    $booking_id_to_cancel = intval($_POST['booking_id']);
    
    if (cancelBooking($conn, $booking_id_to_cancel, $user_id)) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Booking successfully cancelled'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Failed to cancel booking or booking is already completed'
        ];
    }
    
    // Redirect to remove POST data
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Get bookings data
if ($booking_id) {
    // Get specific booking
    $booking = getBooking($conn, $booking_id, $user_id);
    if (!$booking) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Booking not found'
        ];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
} else {
    // Get all bookings for user
    $bookings = getUserBookings($conn, $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $booking_id ? 'Booking Details' : 'My Bookings'; ?> - Vinpearl Nha Trang Resort</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        .booking-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .booking-id {
            font-size: 14px;
            color: #666;
        }
        
        .booking-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #FFF8E1;
            color: #FFA000;
        }
        
        .status-confirmed {
            background-color: #E3F2FD;
            color: #1976D2;
        }
        
        .status-completed {
            background-color: #E8F5E9;
            color: #388E3C;
        }
        
        .status-cancelled {
            background-color: #FFEBEE;
            color: #D32F2F;
        }
        
        .booking-details {
            margin-bottom: 15px;
        }
        
        .booking-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .booking-label {
            font-weight: 500;
            color: #555;
        }
        
        .booking-value {
            text-align: right;
        }
        
        .booking-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .booking-total {
            font-size: 18px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main Content -->
    <main>
        <!-- Bookings Section -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h1 class="heading-2"><?php echo $booking_id ? 'Booking Details' : 'My Bookings'; ?></h1>
                    <?php if (!$booking_id): ?>
                    <p class="section-description">View and manage your bookings with Vinpearl Resort Nha Trang.</p>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="flash-message <?php echo $_SESSION['flash_message']['type']; ?>">
                    <p><?php echo $_SESSION['flash_message']['message']; ?></p>
                    <button class="close-message">&times;</button>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
                <?php endif; ?>
                
                <?php if ($booking_id && $booking): ?>
                <!-- Single Booking View -->
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <h3><?php echo htmlspecialchars($booking['attraction_name']); ?></h3>
                            <span class="booking-id">Booking ID: <?php echo $booking['id']; ?></span>
                        </div>
                        <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo $booking['status']; ?>
                        </span>
                    </div>
                    
                    <div class="booking-details">
                        <div class="booking-row">
                            <span class="booking-label">Booking Type:</span>
                            <span class="booking-value"><?php echo htmlspecialchars($booking['booking_type']); ?></span>
                        </div>
                        
                        <div class="booking-row">
                            <span class="booking-label">Date:</span>
                            <span class="booking-value"><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></span>
                        </div>
                        
                        <div class="booking-row">
                            <span class="booking-label">Number of People:</span>
                            <span class="booking-value"><?php echo $booking['number_of_people']; ?></span>
                        </div>
                        
                        <?php if (!empty($booking['special_requests'])): ?>
                        <div class="booking-row">
                            <span class="booking-label">Special Requests:</span>
                            <span class="booking-value"><?php echo htmlspecialchars($booking['special_requests']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="booking-row">
                            <span class="booking-label">Booked on:</span>
                            <span class="booking-value"><?php echo date('F j, Y', strtotime($booking['created_at'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="booking-footer">
                        <div class="booking-total">
                            Total: $<?php echo number_format($booking['total_price'], 2); ?>
                        </div>
                    </div>
                    
                    <?php if ($booking['status'] !== 'CANCELLED' && $booking['status'] !== 'COMPLETED'): ?>
                    <form method="post" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        <button type="submit" name="cancel_booking" class="btn btn-outline">Cancel Booking</button>
                    </form>
                    <?php endif; ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-link">Back to All Bookings</a>
                </div>
                
                <?php elseif (!$booking_id): ?>
                <!-- List of Bookings -->
                <?php if (empty($bookings)): ?>
                <div class="text-center py-5">
                    <p>You don't have any bookings yet.</p>
                    <a href="../pages/nearby/index.html" class="btn btn-primary mt-3">Explore Attractions</a>
                </div>
                <?php else: ?>
                
                <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <h3><?php echo htmlspecialchars($booking['attraction_name']); ?></h3>
                            <span class="booking-id">Booking ID: <?php echo $booking['id']; ?></span>
                        </div>
                        <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo $booking['status']; ?>
                        </span>
                    </div>
                    
                    <div class="booking-details">
                        <div class="booking-row">
                            <span class="booking-label">Booking Type:</span>
                            <span class="booking-value"><?php echo htmlspecialchars($booking['booking_type']); ?></span>
                        </div>
                        
                        <div class="booking-row">
                            <span class="booking-label">Date:</span>
                            <span class="booking-value"><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></span>
                        </div>
                        
                        <div class="booking-row">
                            <span class="booking-label">Number of People:</span>
                            <span class="booking-value"><?php echo $booking['number_of_people']; ?></span>
                        </div>
                    </div>
                    
                    <div class="booking-footer">
                        <div class="flex justify-between items-center w-full">
                            <div>
                                <a href="?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-link">View Details</a>
                                
                                <?php if ($booking['status'] !== 'CANCELLED' && $booking['status'] !== 'COMPLETED'): ?>
                                <form method="post" class="inline-block ml-2" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" name="cancel_booking" class="btn btn-sm btn-outline">Cancel</button>
                                </form>
                                <?php endif; ?>
                            </div>
                            
                            <div class="booking-total">
                                $<?php echo number_format($booking['total_price'], 2); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="../js/main.js"></script>
</body>
</html>