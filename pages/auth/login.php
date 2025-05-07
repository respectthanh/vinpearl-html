<?php
// Include the session management
require_once '../../includes/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ../../index.php');
    exit();
}

$page_title = 'Login - Vinpearl Nha Trang Resort';

// Include header
include_once '../../includes/header.php';
?>

<!-- Main Content -->
<main>
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1 class="heading-2">Welcome Back</h1>
                <p>Sign in to your account or create a new one</p>
            </div>

            <div class="auth-tabs">
                <div class="auth-tab active" data-tab="login">Sign In</div>
                <div class="auth-tab" data-tab="register">Register</div>
            </div>

            <!-- Login Form -->
            <form class="auth-form" action="login_process.php" method="POST" id="loginForm">
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
                        <a href="forgot_password.php" class="text-sm">Forgot password?</a>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">Sign In</button>
                </div>
            </form>

            <!-- Register Form (initially hidden) -->
            <form class="auth-form" action="register_process.php" method="POST" id="registerForm" style="display: none;">
                <div class="form-group">
                    <label for="reg_name" class="form-label">Full Name</label>
                    <input type="text" id="reg_name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="reg_email" class="form-label">Email</label>
                    <input type="email" id="reg_email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="reg_password" class="form-label">Password</label>
                    <input type="password" id="reg_password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="reg_confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="reg_confirm_password" name="password_confirm" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </div>
            </form>

            <div class="auth-divider">Or continue with</div>

            <div class="social-login">
                <button type="button" class="btn btn-white">
                    <span class="social-icon">G</span>
                    <span>Continue with Google</span>
                </button>
                <button type="button" class="btn btn-white">
                    <span class="social-icon">f</span>
                    <span>Continue with Facebook</span>
                </button>
            </div>

            <div class="auth-footer">
                <p id="loginToggleText">
                    Don't have an account?
                    <a href="#" id="authToggle">Register</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php
// Add custom scripts for this page
$extra_js = <<<EOT
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginTab = document.querySelector('.auth-tab[data-tab="login"]');
    const registerTab = document.querySelector('.auth-tab[data-tab="register"]');
    const authToggle = document.getElementById('authToggle');
    const loginToggleText = document.getElementById('loginToggleText');
    
    // Toggle between login and register forms
    function showLoginForm() {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        loginToggleText.innerHTML = 'Don\'t have an account? <a href="#" id="authToggle">Register</a>';
        document.getElementById('authToggle').addEventListener('click', showRegisterForm);
    }
    
    function showRegisterForm(e) {
        if (e) e.preventDefault();
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        loginTab.classList.remove('active');
        registerTab.classList.add('active');
        loginToggleText.innerHTML = 'Already have an account? <a href="#" id="authToggle">Sign In</a>';
        document.getElementById('authToggle').addEventListener('click', showLoginForm);
    }
    
    // Set up event listeners
    loginTab.addEventListener('click', showLoginForm);
    registerTab.addEventListener('click', showRegisterForm);
    authToggle.addEventListener('click', showRegisterForm);
    
    // Close flash messages
    const closeButtons = document.querySelectorAll('.close-message');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
});
</script>
EOT;

// Include footer
include_once '../../includes/footer.php';
?> 