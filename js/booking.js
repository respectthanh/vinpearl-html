document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const bookingModal = document.getElementById('bookingModal');
    const bookingForm = document.getElementById('bookingForm');
    const closeBtn = bookingModal.querySelector('.close');
    const cancelBtn = bookingModal.querySelector('.modal-cancel');
    
    // Book buttons
    const bookButtons = document.querySelectorAll('.book-btn');
    
    // Summary elements
    const summaryAttraction = document.getElementById('summary_attraction');
    const summaryType = document.getElementById('summary_type');
    const summaryDate = document.getElementById('summary_date');
    const summaryPeople = document.getElementById('summary_people');
    const summaryTotal = document.getElementById('summary_total');
    
    // Form inputs
    const attractionInput = document.getElementById('attraction_name');
    const typeInput = document.getElementById('booking_type');
    const dateInput = document.getElementById('booking_date');
    const peopleInput = document.getElementById('number_of_people');
    const specialRequestsInput = document.getElementById('special_requests');
    
    // Set min date to today
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
    
    // Open modal when book buttons are clicked
    bookButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get data from button
            const attraction = this.getAttribute('data-attraction');
            const type = this.getAttribute('data-type');
            const basePrice = parseFloat(this.getAttribute('data-price'));
            
            // Set form values
            attractionInput.value = attraction;
            typeInput.value = type;
            
            // Default to today's date
            dateInput.value = today;
            
            // Show modal
            bookingModal.style.display = 'block';
            
            // Update summary
            updateSummary(attraction, type, today, 1, basePrice);
        });
    });
    
    // Calculate booking price based on inputs
    function calculatePrice(basePrice, people) {
        return basePrice * people;
    }
    
    // Update booking summary
    function updateSummary(attraction, type, date, people, basePrice) {
        // Format date for display
        const displayDate = new Date(date);
        const formattedDate = displayDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Calculate total price
        const totalPrice = calculatePrice(basePrice, people);
        
        // Update summary elements
        summaryAttraction.textContent = attraction;
        summaryType.textContent = type;
        summaryDate.textContent = formattedDate;
        summaryPeople.textContent = people;
        summaryTotal.textContent = `$${totalPrice.toFixed(2)}`;
    }
    
    // Update summary when form inputs change
    dateInput.addEventListener('change', updateSummaryFromInputs);
    peopleInput.addEventListener('change', updateSummaryFromInputs);
    
    function updateSummaryFromInputs() {
        const attraction = attractionInput.value;
        const type = typeInput.value;
        const date = dateInput.value;
        const people = parseInt(peopleInput.value);
        
        // Find base price from matching book button
        let basePrice = 0;
        bookButtons.forEach(button => {
            if (button.getAttribute('data-attraction') === attraction && 
                button.getAttribute('data-type') === type) {
                basePrice = parseFloat(button.getAttribute('data-price'));
            }
        });
        
        updateSummary(attraction, type, date, people, basePrice);
    }
    
    // Close modal when X is clicked
    closeBtn.addEventListener('click', function() {
        bookingModal.style.display = 'none';
    });
    
    // Close modal when Cancel button is clicked
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            bookingModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === bookingModal) {
            bookingModal.style.display = 'none';
        }
    });
    
    // Form submission
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(bookingForm);
        
        // Add total price to form data
        const totalPriceText = summaryTotal.textContent.replace('$', '');
        formData.append('total_price', parseFloat(totalPriceText));
        
        // Send booking data to server
        fetch('/vinpearl-html/includes/process_booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Booking confirmed! Your booking ID is: ' + data.booking_id);
                // Close modal
                bookingModal.style.display = 'none';
                // Reset form
                bookingForm.reset();
                
                // Redirect to profile/bookings page
                window.location.href = '/vinpearl-html/pages/profile/bookings.html';
            } else {
                if (data.require_login) {
                    // User needs to login
                    alert('Please login to complete your booking');
                    window.location.href = '/vinpearl-html/pages/auth/index.html';
                } else {
                    // Show error message
                    alert('Error: ' + data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your booking. Please try again later.');
        });
    });
}); 