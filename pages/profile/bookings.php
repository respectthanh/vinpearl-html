<!DOCTYPE html>
<?php
    // Include session management
    require_once '../../includes/session.php';
    
    // Redirect to login if not logged in
    requireLogin('../auth/index.html');
    
    // Get current user
    $user = getCurrentUser();
    $userInitial = substr($user['name'], 0, 1);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Vinpearl Nha Trang Resort</title>
    <link rel="stylesheet" href="../../css/main.css">
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

        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            position: relative;
        }

        .flash-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .close-message {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: inherit;
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
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-inner">
                <a href="../../index.html" class="logo">Vinpearl</a>
                <nav class="main-nav">
                    <ul>
                        <li><a href="../../index.html" data-i18n="nav.home">Home</a></li>
                        <li><a href="../about/index.html" data-i18n="nav.about">About</a></li>
                        <li><a href="../rooms/index.html" data-i18n="nav.rooms">Rooms</a></li>
                        <li><a href="../tours/index.html" data-i18n="nav.tours">Tours</a></li>
                        <li><a href="../packages/index.html" data-i18n="nav.packages">Packages</a></li>
                        <li><a href="../nearby/index.html" data-i18n="nav.nearby">Nearby</a></li>
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
                    
                    <!-- User menu (logged in state) -->
                    <div class="user-menu">
                        <button class="user-menu-toggle">
                            <span class="user-initial"><?php echo $userInitial; ?></span>
                            <span class="user-name" id="userName"><?php echo $user['name']; ?></span>
                        </button>
                        <div class="user-dropdown">
                            <a href="index.html">My Profile</a>
                            <a href="bookings.html" class="active">My Bookings</a>
                            <a href="../auth/logout.php">Logout</a>
                        </div>
                    </div>
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
        <!-- Bookings Section -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h1 class="heading-2">My Bookings</h1>
                    <p class="section-description">View and manage your bookings with Vinpearl Resort Nha Trang.</p>
                </div>
                
                <!-- Flash Message -->
                <div class="flash-message success" style="display: none;">
                    <p id="flashMessageText">Booking successfully cancelled.</p>
                    <button class="close-message">&times;</button>
                </div>
                
                <div class="profile-container">
                    <!-- Sidebar Navigation -->
                    <div class="profile-sidebar">
                        <ul class="profile-menu">
                            <li><a href="index.html">Profile Information</a></li>
                            <li><a href="bookings.html" class="active">My Bookings</a></li>
                            <li><a href="settings.html">Account Settings</a></li>
                            <li><a href="preferences.html">Preferences</a></li>
                        </ul>
                    </div>
                    
                    <!-- Main Content Area -->
                    <div class="profile-content">
                        <!-- Loading indicator -->
                        <div id="loadingIndicator" class="loading">
                            <div class="loading-spinner"></div>
                            <p>Loading your bookings...</p>
                        </div>
                        
                        <!-- Bookings container - will be populated with JS -->
                        <div id="bookingsContainer" style="display: none;"></div>
                        
                        <!-- Empty bookings message -->
                        <div id="emptyBookings" class="empty-bookings" style="display: none;">
                            <h3>No Bookings Found</h3>
                            <p>You haven't made any bookings yet. Browse our rooms, tours, and packages to start planning your perfect vacation!</p>
                            <div class="empty-actions" style="margin-top: 20px;">
                                <a href="../rooms/index.html" class="btn btn-primary">Explore Rooms</a>
                                <a href="../tours/index.html" class="btn btn-outline">Discover Tours</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
                        <li><a href="../../index.html" data-i18n="nav.home">Home</a></li>
                        <li><a href="../about/index.html" data-i18n="nav.about">About</a></li>
                        <li><a href="../rooms/index.html" data-i18n="nav.rooms">Rooms</a></li>
                        <li><a href="../tours/index.html" data-i18n="nav.tours">Tours</a></li>
                        <li><a href="../packages/index.html" data-i18n="nav.packages">Packages</a></li>
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
                    <p class="footer-text" data-i18n="footer.newsletter">Subscribe to our newsletter for the latest updates and special offers.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Your Email" class="newsletter-input" data-i18n="footer.email" data-i18n-attr="placeholder">
                        <button type="submit" class="btn btn-primary newsletter-button" data-i18n="footer.subscribe">Subscribe</button>
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

    <script src="../../js/main.js"></script>
    <script>
        // DOM elements
        const loadingIndicator = document.getElementById('loadingIndicator');
        const bookingsContainer = document.getElementById('bookingsContainer');
        const emptyBookings = document.getElementById('emptyBookings');
        const flashMessage = document.querySelector('.flash-message');
        const flashMessageText = document.getElementById('flashMessageText');
        const userName = document.getElementById('userName');
        
        // Format currency
        function formatCurrency(amount, currency = 'USD') {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency
            }).format(amount);
        }
        
        // Format date
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }
        
        // Create booking card HTML
        function createBookingCard(booking) {
            // Determine booking title based on type
            let bookingTitle = '';
            if (booking.booking_type === 'ROOM') {
                bookingTitle = booking.room_type || 'Room Booking';
            } else if (booking.booking_type === 'TOUR') {
                bookingTitle = booking.tour_name || 'Tour Booking';
            } else {
                bookingTitle = booking.attraction_name || 'Booking';
            }
            
            // Create a card element
            const card = document.createElement('div');
            card.className = 'booking-card';
            
            // Create the card header
            const headerDiv = document.createElement('div');
            headerDiv.className = 'booking-header';
            
            // Title and booking ID
            const titleDiv = document.createElement('div');
            const title = document.createElement('h3');
            title.textContent = bookingTitle;
            const bookingId = document.createElement('span');
            bookingId.className = 'booking-id';
            bookingId.textContent = `Booking ID: ${booking.id}`;
            titleDiv.appendChild(title);
            titleDiv.appendChild(bookingId);
            
            // Status badge
            const statusBadge = document.createElement('span');
            statusBadge.className = `booking-status status-${booking.status.toLowerCase()}`;
            statusBadge.textContent = booking.status;
            
            headerDiv.appendChild(titleDiv);
            headerDiv.appendChild(statusBadge);
            
            // Create booking details
            const detailsDiv = document.createElement('div');
            detailsDiv.className = 'booking-details';
            
            // Add booking type
            addDetailRow(detailsDiv, 'Booking Type:', booking.booking_type);
            
            // Add date info based on booking type
            if (booking.booking_type === 'ROOM') {
                addDetailRow(detailsDiv, 'Check-in Date:', formatDate(booking.check_in_date));
                addDetailRow(detailsDiv, 'Check-out Date:', formatDate(booking.check_out_date));
                addDetailRow(detailsDiv, 'Guests:', `${booking.adults} Adult${booking.adults !== 1 ? 's' : ''}${booking.children > 0 ? `, ${booking.children} Child${booking.children !== 1 ? 'ren' : ''}` : ''}`);
                addDetailRow(detailsDiv, 'Room Type:', booking.room_type);
            } else if (booking.booking_type === 'TOUR') {
                addDetailRow(detailsDiv, 'Date:', formatDate(booking.booking_date));
                addDetailRow(detailsDiv, 'Number of People:', booking.number_of_people);
                addDetailRow(detailsDiv, 'Tour Type:', booking.tour_name);
                addDetailRow(detailsDiv, 'Duration:', booking.duration);
            } else {
                addDetailRow(detailsDiv, 'Date:', formatDate(booking.booking_date));
                addDetailRow(detailsDiv, 'Number of People:', booking.number_of_people);
            }
            
            // Add cancellation date if cancelled
            if (booking.status === 'CANCELLED' && booking.cancelled_at) {
                addDetailRow(detailsDiv, 'Cancellation Date:', formatDate(booking.cancelled_at));
            }
            
            // Create footer
            const footerDiv = document.createElement('div');
            footerDiv.className = 'booking-footer';
            
            // Add action buttons based on status
            const actionsDiv = document.createElement('div');
            
            if (booking.status === 'PENDING' || booking.status === 'CONFIRMED') {
                // Cancel button for pending or confirmed bookings
                const cancelForm = document.createElement('form');
                cancelForm.className = 'cancel-form';
                cancelForm.setAttribute('data-booking-id', booking.id);
                
                const cancelButton = document.createElement('button');
                cancelButton.type = 'button';
                cancelButton.className = 'btn btn-outline';
                cancelButton.textContent = 'Cancel Booking';
                cancelButton.onclick = function() { confirmCancel(booking.id); };
                
                cancelForm.appendChild(cancelButton);
                actionsDiv.appendChild(cancelForm);
            } else if (booking.status === 'COMPLETED') {
                // Review button for completed bookings
                const reviewButton = document.createElement('a');
                reviewButton.href = `#review-${booking.id}`;
                reviewButton.className = 'btn btn-primary';
                reviewButton.textContent = 'Review Experience';
                actionsDiv.appendChild(reviewButton);
            }
            
            // Add total price
            const totalDiv = document.createElement('div');
            totalDiv.className = 'booking-total';
            totalDiv.textContent = formatCurrency(booking.total_price);
            
            // Add "Refunded" text for cancelled bookings
            if (booking.status === 'CANCELLED') {
                totalDiv.textContent += ' (Refunded)';
            }
            
            footerDiv.appendChild(actionsDiv);
            footerDiv.appendChild(totalDiv);
            
            // Assemble the card
            card.appendChild(headerDiv);
            card.appendChild(detailsDiv);
            card.appendChild(footerDiv);
            
            return card;
        }
        
        // Helper to create a detail row
        function addDetailRow(parent, label, value) {
            const row = document.createElement('div');
            row.className = 'booking-row';
            
            const labelSpan = document.createElement('span');
            labelSpan.className = 'booking-label';
            labelSpan.textContent = label;
            
            const valueSpan = document.createElement('span');
            valueSpan.className = 'booking-value';
            valueSpan.textContent = value;
            
            row.appendChild(labelSpan);
            row.appendChild(valueSpan);
            parent.appendChild(row);
        }
        
        // Load user bookings
        async function loadBookings() {
            try {
                // Show loading indicator
                loadingIndicator.style.display = 'block';
                bookingsContainer.style.display = 'none';
                emptyBookings.style.display = 'none';
                
                // Fetch bookings from API
                const response = await fetch('../../api/bookings.php');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Update user name if available
                if (data.user && data.user.name) {
                    userName.textContent = data.user.name;
                    document.querySelector('.user-initial').textContent = data.user.name.charAt(0);
                }
                
                // Process bookings
                if (data.bookings && data.bookings.length > 0) {
                    // Clear container
                    bookingsContainer.innerHTML = '';
                    
                    // Sort bookings by date (newest first)
                    data.bookings.sort((a, b) => {
                        return new Date(b.created_at) - new Date(a.created_at);
                    });
                    
                    // Create booking cards
                    data.bookings.forEach(booking => {
                        const card = createBookingCard(booking);
                        bookingsContainer.appendChild(card);
                    });
                    
                    // Show bookings
                    bookingsContainer.style.display = 'block';
                    emptyBookings.style.display = 'none';
                } else {
                    // Show empty state
                    bookingsContainer.style.display = 'none';
                    emptyBookings.style.display = 'block';
                }
                
                // Check for flash message
                if (data.flash_message) {
                    showFlashMessage(data.flash_message.message, data.flash_message.type);
                }
                
            } catch (error) {
                console.error('Error loading bookings:', error);
                showFlashMessage('Failed to load bookings. Please try again later.', 'error');
            } finally {
                // Hide loading indicator
                loadingIndicator.style.display = 'none';
            }
        }
        
        // Show flash message
        function showFlashMessage(message, type = 'success') {
            flashMessageText.textContent = message;
            flashMessage.className = `flash-message ${type}`;
            flashMessage.style.display = 'block';
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                flashMessage.style.display = 'none';
            }, 5000);
        }
        
        // Cancel booking
        async function cancelBooking(bookingId) {
            try {
                loadingIndicator.style.display = 'block';
                
                const formData = new FormData();
                formData.append('booking_id', bookingId);
                formData.append('cancel_booking', true);
                
                const response = await fetch('../../api/cancel_booking.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showFlashMessage(result.message || 'Booking successfully cancelled.');
                    // Reload bookings to update the list
                    loadBookings();
                } else {
                    showFlashMessage(result.message || 'Failed to cancel booking.', 'error');
                }
                
            } catch (error) {
                console.error('Error cancelling booking:', error);
                showFlashMessage('An error occurred while cancelling the booking. Please try again.', 'error');
            } finally {
                loadingIndicator.style.display = 'none';
            }
        }
        
        // Confirm cancel booking
        function confirmCancel(bookingId) {
            if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                cancelBooking(bookingId);
            }
        }
        
        // Close flash message
        document.addEventListener('DOMContentLoaded', function() {
            // Set up flash message close buttons
            const closeButtons = document.querySelectorAll('.close-message');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.parentElement.style.display = 'none';
                });
            });
            
            // Load bookings when page loads
            loadBookings();
            
            // Check URL parameters for flash messages
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('message')) {
                showFlashMessage(
                    decodeURIComponent(urlParams.get('message')),
                    urlParams.get('type') || 'success'
                );
            }
        });
    </script>
</body>
</html>