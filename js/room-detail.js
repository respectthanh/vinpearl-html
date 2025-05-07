document.addEventListener('DOMContentLoaded', () => {
    // Image Gallery Functionality
    initGallery();
    
    // Booking Form Functionality
    initBookingCalculator();
    
    // Reviews Loading
    initReviewsLoader();
});

// Gallery Navigation
function initGallery() {
    const mainImage = document.querySelector('.gallery-main .main-image');
    const thumbnails = document.querySelectorAll('.gallery-thumbnails .thumbnail');
    const prevButton = document.querySelector('.gallery-nav.prev');
    const nextButton = document.querySelector('.gallery-nav.next');
    
    let currentIndex = 0;
    
    // Set up thumbnail clicks
    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => {
            mainImage.src = thumbnail.dataset.src;
            updateActiveThumb(index);
            currentIndex = index;
        });
    });
    
    // Set up navigation buttons
    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
        mainImage.src = thumbnails[currentIndex].dataset.src;
        updateActiveThumb(currentIndex);
    });
    
    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % thumbnails.length;
        mainImage.src = thumbnails[currentIndex].dataset.src;
        updateActiveThumb(currentIndex);
    });
    
    // Update active thumbnail
    function updateActiveThumb(activeIndex) {
        thumbnails.forEach((thumb, i) => {
            if (i === activeIndex) {
                thumb.classList.add('active');
            } else {
                thumb.classList.remove('active');
            }
        });
    }
}

// Booking Calculator
function initBookingCalculator() {
    const checkInInput = document.getElementById('check-in');
    const checkOutInput = document.getElementById('check-out');
    const nightsCount = document.getElementById('nights-count');
    const taxesAmount = document.getElementById('taxes-amount');
    const totalAmount = document.getElementById('total-amount');
    
    const roomRate = 250; // $250 per night
    const taxRate = 0.1; // 10%
    
    // Set minimum dates (today and tomorrow)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    const todayFormatted = formatDate(today);
    const tomorrowFormatted = formatDate(tomorrow);
    
    checkInInput.min = todayFormatted;
    checkInInput.value = todayFormatted;
    
    checkOutInput.min = tomorrowFormatted;
    checkOutInput.value = tomorrowFormatted;
    
    // Calculate initial values
    calculateBooking();
    
    // Add event listeners
    checkInInput.addEventListener('change', handleCheckInChange);
    checkOutInput.addEventListener('change', calculateBooking);
    
    function handleCheckInChange() {
        // Ensure check-out is always after check-in
        const newCheckIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (newCheckIn >= checkOut) {
            const nextDay = new Date(newCheckIn);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOutInput.value = formatDate(nextDay);
        }
        
        // Update minimum check-out date
        const minCheckOut = new Date(checkInInput.value);
        minCheckOut.setDate(minCheckOut.getDate() + 1);
        checkOutInput.min = formatDate(minCheckOut);
        
        calculateBooking();
    }
    
    function calculateBooking() {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        // Calculate number of nights
        const nights = Math.max(1, Math.round((checkOut - checkIn) / (1000 * 60 * 60 * 24)));
        
        // Update the display
        nightsCount.textContent = nights;
        
        // Calculate costs
        const roomCost = nights * roomRate;
        const taxesFees = roomCost * taxRate;
        const totalCost = roomCost + taxesFees;
        
        // Update displays
        taxesAmount.textContent = `$${taxesFees.toFixed(0)}`;
        totalAmount.textContent = `$${totalCost.toFixed(0)}`;
    }
    
    // Helper function to format dates as YYYY-MM-DD
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    // Handle form submission
    document.querySelector('.booking-form').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Thank you for your booking! This is a demo. In a real application, this would connect to a booking system.');
    });
}

// Reviews Loading
function initReviewsLoader() {
    const loadMoreButton = document.querySelector('.load-more-reviews');
    
    // Sample additional reviews data (in a real app, this would be loaded from a server)
    const additionalReviews = [
        {
            name: 'Emily T.',
            avatar: 'ET',
            date: 'February 2025',
            rating: 4.7,
            content: 'We had a wonderful stay in the Deluxe Ocean View room. The view was spectacular, especially at sunset. The room was clean and spacious. The staff was very accommodating to all our requests.'
        },
        {
            name: 'Michael W.',
            avatar: 'MW',
            date: 'January 2025',
            rating: 5.0,
            content: 'One of the best hotel experiences I\'ve had. The ocean view is absolutely worth it - waking up to that sight every morning was incredible. The room is modern and luxurious, and the bed is extremely comfortable. Will definitely return!'
        }
    ];
    
    let reviewsLoaded = false;
    
    loadMoreButton.addEventListener('click', () => {
        if (!reviewsLoaded) {
            const reviewsList = document.querySelector('.reviews-list');
            
            // Create and append new review elements before the button
            additionalReviews.forEach(review => {
                const reviewElement = createReviewElement(review);
                reviewsList.insertBefore(reviewElement, loadMoreButton);
            });
            
            // Update button text
            loadMoreButton.textContent = 'No More Reviews';
            reviewsLoaded = true;
        }
    });
    
    function createReviewElement(review) {
        const reviewCard = document.createElement('div');
        reviewCard.className = 'review-card';
        
        // Generate stars based on rating
        const fullStars = Math.floor(review.rating);
        const hasHalfStar = review.rating % 1 >= 0.5;
        let starsHTML = '★'.repeat(fullStars);
        if (hasHalfStar) {
            starsHTML += '✩';
        }
        
        reviewCard.innerHTML = `
            <div class="review-header">
                <div class="reviewer-info">
                    <div class="reviewer-avatar">${review.avatar}</div>
                    <div class="reviewer-details">
                        <h4 class="reviewer-name">${review.name}</h4>
                        <div class="review-date">${review.date}</div>
                    </div>
                </div>
                <div class="review-rating">
                    <div class="rating-stars">${starsHTML}</div>
                    <div class="rating-score">${review.rating.toFixed(1)}</div>
                </div>
            </div>
            <div class="review-content">
                <p>${review.content}</p>
            </div>
        `;
        
        return reviewCard;
    }
}