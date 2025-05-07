<?php
// Start session
session_start();

// Include required files
require_once '../../includes/db_connect.php';
require_once '../../includes/session.php';

// Initialize variables
$mode = isset($_GET['mode']) && $_GET['mode'] === 'register' ? 'register' : 'login';
$error = '';
$success = '';

// Check if user is already logged in
if (isLoggedIn()) {
    // User is already logged in, redirect to homepage
    header('Location: ../../index.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (empty($csrf_token) || !validateCSRFToken($csrf_token, 'auth_form')) {
        $error = "Invalid form submission. Please try again.";
    } else {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'login') {
                // Process login
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';
                $remember = isset($_POST['remember']) ? true : false;
                
                // Validate input
                if (empty($email) || empty($password)) {
                    $error = "Please enter both email and password.";
                } else {
                    // Debug log for connection status
                    error_log("DB Connection status: " . ($conn->connect_errno ? "Failed: ".$conn->connect_error : "Connected"));
                    
                    // Prepare SQL statement to prevent SQL injection
                    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                    if (!$stmt) {
                        error_log("Prepare failed: " . $conn->error);
                        $error = "System error. Please try again later.";
                    } else {
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        // Debug log for query results
                        error_log("Login attempt for: $email - Found rows: " . $result->num_rows);
                        
                        if ($result->num_rows === 1) {
                            $user = $result->fetch_assoc();
                            if (password_verify($password, $user['password'])) {
                                // Password is correct, set session variables
                                setUserSession($user, $remember);
                                
                                // Log the successful login attempt
                                $ip = $_SERVER['REMOTE_ADDR'];
                                error_log("Successful login for user {$user['email']} from IP $ip");
                                
                                // Redirect to intended page or homepage
                                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '../../index.php';
                                unset($_SESSION['redirect_after_login']);
                                header("Location: $redirect");
                                exit();
                            } else {
                                // Log failed login attempt
                                $ip = $_SERVER['REMOTE_ADDR'];
                                error_log("Failed login attempt for user $email from IP $ip - incorrect password");
                                $error = "Invalid email or password.";
                            }
                        } else {
                            // Log failed login attempt
                            $ip = $_SERVER['REMOTE_ADDR'];
                            error_log("Failed login attempt for non-existent user $email from IP $ip");
                            $error = "Invalid email or password.";
                        }
                        $stmt->close();
                    }
                }
                $mode = 'login'; // Keep on login mode
            } elseif ($_POST['action'] === 'register') {
                // Process registration
                $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';
                $password_confirm = $_POST['password_confirm'] ?? '';
                
                // Validate input
                if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
                    $error = "All fields are required.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Please enter a valid email address.";
                } elseif (strlen($password) < 8) {
                    $error = "Password must be at least 8 characters long.";
                } elseif ($password !== $password_confirm) {
                    $error = "Passwords do not match.";
                } else {
                    // Check if email already exists
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    if (!$stmt) {
                        error_log("Prepare failed: " . $conn->error);
                        $error = "System error. Please try again later.";
                    } else {
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            $error = "Email address already in use. Please choose another or login.";
                        } else {
                            // Hash password
                            $password_hash = password_hash($password, PASSWORD_DEFAULT);
                            
                            // Insert new user
                            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, 'USER', NOW())");
                            $stmt->bind_param("sss", $name, $email, $password_hash);
                            
                            if ($stmt->execute()) {
                                // Log successful registration
                                $ip = $_SERVER['REMOTE_ADDR'];
                                error_log("New user registration: $email from IP $ip");
                                $success = "Registration successful! You can now login.";
                                $mode = 'login'; // Switch to login mode
                            } else {
                                error_log("Failed registration for $email: " . $conn->error);
                                $error = "Registration failed: " . $conn->error;
                            }
                        }
                        $stmt->close();
                    }
                }
                
                if (!$success) {
                    $mode = 'register'; // Keep on register mode if there was an error
                }
            }
        }
    }
}

// Generate CSRF token for the form
$csrf_token = generateCSRFToken('auth_form');

// Set page title
$page_title = ($mode === 'register') ? 'Register - Vinpearl Nha Trang Resort' : 'Login - Vinpearl Nha Trang Resort';

// Check if there's a flash message
$flash_message = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 4px;
            position: relative;
            transition: opacity 0.5s ease-out;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-inner">
                <a href="../../index.php" class="logo">Vinpearl</a>
                <nav class="main-nav">
                    <ul>
                        <li><a href="../../index.php">Home</a></li>
                        <li><a href="../about/index.html">About</a></li>
                        <li><a href="../rooms/index.html">Rooms</a></li>
                        <li><a href="../tours/index.html">Tours</a></li>
                        <li><a href="../packages/index.html">Packages</a></li>
                        <li><a href="../nearby/index.html">Nearby</a></li>
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
                    <a href="auth.php" class="btn btn-white active">Sign In</a>
                </div>
                <button class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="auth-container">
                <div class="auth-header">
                    <h1 class="heading-2"><?php echo ($mode === 'register') ? 'Create Account' : 'Welcome Back'; ?></h1>
                    <p><?php echo ($mode === 'register') ? 'Register to access all our services' : 'Sign in to your account or create a new one'; ?></p>
                </div>
                
                <!-- Display flash message if any -->
                <?php if ($flash_message): ?>
                <div class="alert alert-<?php echo $flash_message[0]; ?>">
                    <?php echo $flash_message[1]; ?>
                </div>
                <?php endif; ?>
                
                <!-- Display error message if any -->
                <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <!-- Display success message if any -->
                <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <div class="auth-tabs">
                    <a href="auth.php" class="auth-tab <?php echo ($mode === 'login') ? 'active' : ''; ?>">Sign In</a>
                    <a href="auth.php?mode=register" class="auth-tab <?php echo ($mode === 'register') ? 'active' : ''; ?>">Register</a>
                </div>

                <?php if ($mode === 'login'): ?>
                <!-- Login Form -->
                <form class="auth-form" action="auth.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <div class="flex justify-between items-center">
                            <div class="form-check">
                                <input type="checkbox" id="remember" name="remember" class="form-check-input">
                                <label for="remember" class="form-check-label">Remember me</label>
                            </div>
                            <a href="forgot-password.php" class="text-sm">Forgot password?</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    </div>
                </form>
                <?php else: ?>
                <!-- Register Form -->
                <form class="auth-form" action="auth.php?mode=register" method="POST">
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required 
                               pattern=".{8,}" title="Password must be at least 8 characters">
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </div>
                </form>
                <?php endif; ?>

                <div class="auth-divider">Or continue with</div>

                <div class="social-login">
                    <button type="button" class="btn btn-white" disabled>
                        <span class="social-icon">G</span>
                        <span>Continue with Google</span>
                    </button>
                    <button type="button" class="btn btn-white" disabled>
                        <span class="social-icon">f</span>
                        <span>Continue with Facebook</span>
                    </button>
                </div>

                <div class="auth-footer">
                    <p>
                        <?php if ($mode === 'login'): ?>
                        Don't have an account? <a href="auth.php?mode=register">Register</a>
                        <?php else: ?>
                        Already have an account? <a href="auth.php">Sign In</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-title">Vinpearl Nha Trang</h3>
                    <p class="footer-text">Experience luxury and comfort in our beachfront resort with world-class amenities.</p>
                    <div class="social-links">
                        <a href="#" class="social-link">Facebook</a>
                        <a href="#" class="social-link">Instagram</a>
                        <a href="#" class="social-link">Twitter</a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="../../index.php">Home</a></li>
                        <li><a href="../about/index.html">About</a></li>
                        <li><a href="../rooms/index.html">Rooms</a></li>
                        <li><a href="../tours/index.html">Tours</a></li>
                        <li><a href="../packages/index.html">Packages</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-title">Contact</h3>
                    <address class="footer-contact">
                        <p>Hon Tre Island, Nha Trang</p>
                        <p>Khanh Hoa, Vietnam</p>
                        <p>Phone: +84 258 359 8888</p>
                        <p>Email: info@vinpearl.com</p>
                    </address>
                </div>
                <div class="footer-col">
                    <h3 class="footer-title">Newsletter</h3>
                    <p class="footer-text">Subscribe to our newsletter for the latest updates and special offers.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Your Email" class="newsletter-input">
                        <button type="submit" class="btn btn-primary newsletter-button">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="copyright">© 2025 Vinpearl Nha Trang. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Close alert messages after a delay
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
        
        // Password confirmation validation
        const registerForm = document.querySelector('form[action="auth.php?mode=register"]');
        if (registerForm) {
            registerForm.addEventListener('submit', function(event) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('password_confirm').value;
                
                if (password !== confirmPassword) {
                    event.preventDefault();
                    alert('Passwords do not match!');
                }
            });
        }
    });
    </script>
</body>
</html>