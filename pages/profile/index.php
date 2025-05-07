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
$page_title = 'My Profile - Vinpearl Nha Trang Resort';

// Include header
include_once '../../includes/header.php';

// Custom CSS for this page
$profile_css = <<<EOT
<style>
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

    .profile-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 30px;
    }

    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        background-color: #4a6f8a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        margin-right: 20px;
    }

    .profile-info h2 {
        margin: 0 0 5px 0;
    }

    .profile-role {
        display: inline-block;
        padding: 3px 10px;
        background-color: #e3f2fd;
        color: #1976d2;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    .info-group {
        margin-bottom: 30px;
    }

    .info-group h3 {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .info-row {
        display: flex;
        margin-bottom: 15px;
    }

    .info-label {
        width: 150px;
        font-weight: 500;
        color: #555;
    }

    .info-value {
        flex: 1;
    }
</style>
EOT;

echo $profile_css;

// Get user data from database to ensure it's up-to-date
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Check for any flash messages
$flash_message = getFlashMessage();
?>

<!-- Main Content -->
<main>
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h1 class="heading-2">My Profile</h1>
                <p class="section-description">View and manage your personal information.</p>
            </div>

            <div class="profile-container">
                <!-- Sidebar Navigation -->
                <div class="profile-sidebar">
                    <ul class="profile-menu">
                        <li><a href="index.php" class="active">Profile Information</a></li>
                        <li><a href="bookings.php">My Bookings</a></li>
                        <li><a href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
                
                <!-- Main Content Area -->
                <div class="profile-content">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="profile-avatar">
                                <?php echo substr($user_data['name'], 0, 1); ?>
                            </div>
                            <div class="profile-info">
                                <h2><?php echo htmlspecialchars($user_data['name']); ?></h2>
                                <p><?php echo htmlspecialchars($user_data['email']); ?></p>
                                <span class="profile-role"><?php echo htmlspecialchars($user_data['role']); ?></span>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <h3>Account Information</h3>
                            <div class="info-row">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_data['name']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Account Type</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_data['role']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Member Since</div>
                                <div class="info-value"><?php echo date('F j, Y', strtotime($user_data['created_at'])); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="actions" style="margin-top: 20px; text-align: right;">
                        <a href="#" class="btn btn-outline">Edit Profile</a>
                        <a href="#" class="btn btn-outline">Change Password</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../../includes/footer.php';
?>