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
    <style>
        .user-menu {
            position: relative;
            display: inline-block;
        }
        
        .user-menu-toggle {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: white;
        }
        
        .user-initial {
            width: 32px;
            height: 32px;
            background-color: #4a6f8a;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 8px;
        }
        
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
            min-width: 160px;
            z-index: 100;
        }
        
        .user-menu:hover .user-dropdown {
            display: block;
        }
        
        .user-dropdown a {
            display: block;
            padding: 10px 16px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .user-dropdown a:hover {
            background-color: #f9f9f9;
        }
        
        .user-dropdown a:last-child {
            border-bottom: none;
        }
        
        .flash-message {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 4px;
            position: relative;
            border: 1px solid;
            text-align: center;
        }
        
        .flash-message.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .flash-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .flash-message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }
        
        .close-message {
            position: absolute;
            top: 8px;
            right: 12px;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
        }
        
        .close-message:hover {
            opacity: 1;
        }
    </style>
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
                            <?php if (isset($user['role']) && strtoupper($user['role']) === 'ADMIN'): ?>
                                <a href="<?php echo $base_url; ?>pages/admin/index.php">Admin Panel</a>
                            <?php endif; ?>
                            <a href="<?php echo $base_url; ?>pages/auth/logout.php">Logout</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- User is not logged in, show sign in button -->
                    <a href="<?php echo $base_url; ?>pages/auth/auth.php" class="btn btn-white">Sign In</a>
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
    $flash_message = getFlashMessage();
    if ($flash_message): 
    ?>
    <div class="flash-message <?php echo $flash_message[0]; ?>">
        <?php echo $flash_message[1]; ?>
        <button class="close-message" onclick="this.parentElement.style.display='none'">&times;</button>
    </div>
    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            const flashMessage = document.querySelector('.flash-message');
            if (flashMessage) {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.style.display = 'none', 500);
            }
        }, 5000);
    </script>
    <?php endif; ?>