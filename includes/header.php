<?php 
// Include the session management
require_once __DIR__ . '/session.php';

// Get base URL for links
$current_path = $_SERVER['SCRIPT_NAME'];
$base_url = '';

// If in a subdirectory, adjust the base URL
if (strpos($current_path, '/pages/') !== false) {
    $base_url = '../../';
} else {
    $base_url = './';
}

// Check if user is logged in
$logged_in = isLoggedIn();
$user = $logged_in ? getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Vinpearl Nha Trang Resort'; ?></title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-inner">
                <a href="<?php echo $base_url; ?>index.php" class="logo">Vinpearl</a>
                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo $base_url; ?>index.php" <?php echo basename($current_path) == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/about/index.php" <?php echo strpos($current_path, '/about/') ? 'class="active"' : ''; ?>>About</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/rooms/index.php" <?php echo strpos($current_path, '/rooms/') ? 'class="active"' : ''; ?>>Rooms</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/tours/index.php" <?php echo strpos($current_path, '/tours/') ? 'class="active"' : ''; ?>>Tours</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/packages/index.php" <?php echo strpos($current_path, '/packages/') ? 'class="active"' : ''; ?>>Packages</a></li>
                        <li><a href="<?php echo $base_url; ?>pages/nearby/index.php" <?php echo strpos($current_path, '/nearby/') ? 'class="active"' : ''; ?>>Nearby</a></li>
                    </ul>
                </nav>
                <div class="header-buttons">
                    <div class="language-selector">
                        <button id="language-toggle">EN</button>
                        <div class="language-dropdown">
                            <a href="#" data-lang="en" class="active">English</a>
                            <a href="#" data-lang="vi">Vietnamese</a>
                        </div>
                    </div>
                    
                    <?php if ($logged_in): ?>
                    <!-- User is logged in, show user menu -->
                    <div class="user-menu">
                        <button class="user-menu-toggle">
                            <span class="user-initial"><?php echo substr($user['name'], 0, 1); ?></span>
                            <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                        </button>
                        <div class="user-dropdown">
                            <a href="<?php echo $base_url; ?>pages/profile/index.php">My Profile</a>
                            <a href="<?php echo $base_url; ?>pages/profile/bookings.php">My Bookings</a>
                            <?php if (isset($user['role']) && $user['role'] === 'ADMIN'): ?>
                                <a href="<?php echo $base_url; ?>pages/admin/index.php">Admin Panel</a>
                            <?php endif; ?>
                            <a href="<?php echo $base_url; ?>pages/auth/logout.php">Logout</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- User is not logged in, show sign in button -->
                    <a href="<?php echo $base_url; ?>pages/auth/login.php" class="btn btn-white">Sign In</a>
                    <?php endif; ?>
                </div>
                <button class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <?php
    // Display flash messages if any
    $message = getFlashMessage();
    if ($message): 
    ?>
    <div class="flash-message <?php echo $message[0]; ?>">
        <?php echo $message[1]; ?>
        <button class="close-message">&times;</button>
    </div>
    <?php endif; ?> 