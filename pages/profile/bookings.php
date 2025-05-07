<!DOCTYPE html>
<?php
// Include session management
require_once '../../includes/session.php';
require_once '../../includes/db_connect.php';

// Redirect to login if not logged in
requireLogin('../auth/auth.php');

// Get current user
$user = getCurrentUser();
$user_id = $user['id'];

// Set page title
$page_title = 'My Bookings - Vinpearl Nha Trang Resort';

// Include header
include_once '../../includes/header.php';

// Custom CSS for this page
$bookings_css = <<<EOT
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
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .booking-total {
        font-size: 18px;
        font-weight: 600;
    }

    .profile-sidebar {
        width: 250px;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-right: 30px;
    }

    .profile-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .profile-menu li {
        margin-bottom: 10px;
    }

    .profile-menu a {
        display: block;
        padding: 10px 15px;
        border-radius: 4px;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .profile-menu a:hover {
        background-color: #e9ecef;
    }

    .profile-menu a.active {
        background-color: #007bff;
        color: white;
    }

    .profile-content {
        flex: 1;
    }

    .profile-container {
        display: flex;
        margin-top: 30px;
    }

    .empty-bookings {
        text-align: center;
        padding: 40px 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .empty-bookings h3 {
        margin-bottom: 15px;
        color: #343a40;
    }

    .loading {
        text-align: center;
        padding: 40px 20px;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 3px solid rgba(0,0,0,0.1);
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
        margin-bottom: 15px;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
EOT;

echo $bookings_css;

// Get user's bookings
$bookings = [];
$query = "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();

// Check for any flash messages
$flash_message = getFlashMessage();
?>

<!-- Main Content -->
<main>
    <!-- Bookings Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h1 class="heading-2">My Bookings</h1>
                <p class="section-description">View and manage your bookings with Vinpearl Resort Nha Trang.</p>
            </div>

            <div class="profile-container">
                <!-- Sidebar Navigation -->
                <div class="profile-sidebar">
                    <ul class="profile-menu">
                        <li><a href="index.php">Profile Information</a></li>
                        <li><a href="bookings.php" class="active">My Bookings</a></li>
                        <li><a href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
                
                <!-- Main Content Area -->
                <div class="profile-content">
                    <?php if (empty($bookings)): ?>
                    <!-- Empty bookings message -->
                    <div class="empty-bookings">
                        <h3>No Bookings Found</h3>
                        <p>You haven't made any bookings yet. Browse our rooms, tours, and packages to start planning your perfect vacation!</p>
                        <div class="empty-actions" style="margin-top: 20px;">
                            <a href="../rooms/index.php" class="btn btn-primary">Explore Rooms</a>
                            <a href="../tours/index.php" class="btn btn-outline">Discover Tours</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Bookings list -->
                    <div id="bookingsContainer">
                        <?php foreach ($bookings as $booking): ?>
                            <div class="booking-card">
                                <div class="booking-header">
                                    <div>
                                        <h3><?php echo htmlspecialchars($booking['attraction_name']); ?></h3>
                                        <span class="booking-id">Booking ID: <?php echo htmlspecialchars($booking['id']); ?></span>
                                    </div>
                                    <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo htmlspecialchars($booking['status']); ?>
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
                                        <span class="booking-value"><?php echo htmlspecialchars($booking['number_of_people']); ?></span>
                                    </div>
                                    
                                    <?php if (!empty($booking['special_requests'])): ?>
                                    <div class="booking-row">
                                        <span class="booking-label">Special Requests:</span>
                                        <span class="booking-value"><?php echo htmlspecialchars($booking['special_requests']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="booking-footer">
                                    <div>
                                        <?php if ($booking['status'] === 'PENDING' || $booking['status'] === 'CONFIRMED'): ?>
                                        <form method="POST" action="../../api/cancel_booking.php" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="cancel_booking" class="btn btn-outline">Cancel Booking</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                    <div class="booking-total">
                                        $<?php echo number_format($booking['total_price'], 2); ?>
                                        <?php if ($booking['status'] === 'CANCELLED'): ?>
                                            (Refunded)
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../../includes/footer.php';
?>