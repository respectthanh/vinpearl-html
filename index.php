<?php
// Include the session management
require_once 'includes/session.php';

// Set page title
$page_title = 'Vinpearl Nha Trang Resort';

// Include header
include_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-image">
        <img src="images/hero.jpg" alt="Vinpearl Resort" class="hero-img">
        <div class="hero-overlay"></div>
    </div>
    <div class="hero-content">
        <div class="container">
            <h1 class="hero-title heading-1 slide-up">Experience Luxury in Paradise</h1>
            <p class="hero-subtitle slide-up">Welcome to the premier beachfront resort in Nha Trang with world-class amenities and unparalleled service.</p>
            <div class="hero-buttons slide-up">
                <a href="pages/rooms/index.php" class="btn btn-primary">Book Your Stay</a>
                <a href="#about" class="btn btn-outline">Discover More</a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-section section scroll-reveal">
    <div class="container">
        <div class="about-grid">
            <div class="about-content">
                <h2 class="heading-2 slide-up">Luxury Resort Experience</h2>
                <p class="about-text slide-up">Nestled on the pristine coastline of Nha Trang, our award-winning resort offers an unforgettable blend of traditional Vietnamese hospitality and modern luxury. With breathtaking views of the ocean, exquisite dining options, and a wide range of activities, Vinpearl Resort is the perfect destination for your dream vacation.</p>
                <div class="about-features slide-up">
                    <div class="feature">
                        <div class="feature-icon">🏖️</div>
                        <div class="feature-text">Private Beach</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🍽️</div>
                        <div class="feature-text">5-Star Dining</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">💆</div>
                        <div class="feature-text">Luxury Spa</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🏊</div>
                        <div class="feature-text">Infinity Pools</div>
                    </div>
                </div>
            </div>
            <div class="about-image-container scroll-reveal">
                <img src="images/about.jpg" alt="Resort View" class="about-image">
            </div>
        </div>
    </div>
</section>

<!-- Featured Rooms Section -->
<section class="rooms-section section scroll-reveal">
    <div class="container">
        <div class="section-header">
            <h2 class="heading-2 slide-up">Luxurious Accommodations</h2>
            <p class="section-description slide-up">Choose from our selection of beautifully designed rooms and suites, each offering comfort, elegance, and stunning views.</p>
        </div>
        <div class="rooms-grid">
            <div class="room-card scroll-reveal">
                <div class="room-image">
                    <img src="images/room-1.jpg" alt="Deluxe Ocean View Room">
                    <div class="room-tag">Popular</div>
                </div>
                <div class="room-details">
                    <h3 class="room-title">Deluxe Ocean View Room</h3>
                    <div class="room-meta">
                        <span class="room-size">45m²</span>
                        <span class="room-capacity">2 Adults, 1 Child</span>
                    </div>
                    <div class="room-price">
                        <span class="price">$250</span>
                        <span class="per-night">per night</span>
                    </div>
                    <a href="pages/rooms/room-details.php?room=deluxe-ocean" class="btn btn-secondary">View Details</a>
                </div>
            </div>
            <!-- More room cards could be added here -->
        </div>
        <div class="section-footer">
            <a href="pages/rooms/index.php" class="btn btn-outline">View All Rooms</a>
        </div>
    </div>
</section>

<?php
// Include footer
include_once 'includes/footer.php';
?> 