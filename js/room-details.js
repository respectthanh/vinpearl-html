/**
 * Vinpearl Resort - Room Details Page JavaScript
 * Handles displaying the details of a specific room and booking functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Room details page translations
    const roomDetailsTranslations = {
        'en': {
            'roomDetails.notFound': 'Room Not Found',
            'roomDetails.backToRooms': 'View All Rooms',
            'roomDetails.description': 'Description',
            'roomDetails.amenities': 'Amenities',
            'roomDetails.policies': 'Policies',
            'roomDetails.bookThisRoom': 'Book This Room',
            'roomDetails.checkIn': 'Check In',
            'roomDetails.checkOut': 'Check Out',
            'roomDetails.guests': 'Guests',
            'roomDetails.roomRate': 'Room Rate',
            'roomDetails.taxesFees': 'Taxes & Fees (10%)',
            'roomDetails.total': 'Total',
            'roomDetails.bookNow': 'Book Now',
            'roomDetails.paymentNote': 'No payment required today. You\'ll pay during your stay.',
            'roomDetails.needHelp': 'Need Help?',
            'roomDetails.helpText': 'Have questions or need assistance with your booking?',
            'roomDetails.callUs': 'Call Us',
            'roomDetails.emailUs': 'Email Us',
            'roomDetails.similarRooms': 'Similar Rooms You May Like'
        },
        'vi': {
            'roomDetails.notFound': 'Không Tìm Thấy Phòng',
            'roomDetails.backToRooms': 'Xem Tất Cả Phòng',
            'roomDetails.description': 'Mô Tả',
            'roomDetails.amenities': 'Tiện Nghi',
            'roomDetails.policies': 'Chính Sách',
            'roomDetails.bookThisRoom': 'Đặt Phòng Này',
            'roomDetails.checkIn': 'Nhận Phòng',
            'roomDetails.checkOut': 'Trả Phòng',
            'roomDetails.guests': 'Số Khách',
            'roomDetails.roomRate': 'Giá Phòng',
            'roomDetails.taxesFees': 'Thuế & Phí (10%)',
            'roomDetails.total': 'Tổng Cộng',
            'roomDetails.bookNow': 'Đặt Ngay',
            'roomDetails.paymentNote': 'Không cần thanh toán hôm nay. Bạn sẽ thanh toán khi lưu trú.',
            'roomDetails.needHelp': 'Cần Hỗ Trợ?',
            'roomDetails.helpText': 'Có câu hỏi hoặc cần trợ giúp với đặt phòng của bạn?',
            'roomDetails.callUs': 'Gọi Cho Chúng Tôi',
            'roomDetails.emailUs': 'Email Cho Chúng Tôi',
            'roomDetails.similarRooms': 'Các Phòng Tương Tự Bạn Có Thể Thích'
        }
    };

    // Add room details translations to main translations object
    if (typeof window.allTranslations === 'object') {
        if (!window.allTranslations.en) {
            window.allTranslations.en = {};
        }
        if (!window.allTranslations.vi) {
            window.allTranslations.vi = {};
        }
        
        // Merge room details translations with main translations
        Object.assign(window.allTranslations.en, roomDetailsTranslations.en);
        Object.assign(window.allTranslations.vi, roomDetailsTranslations.vi);
        
        // Load translations for current language if loadTranslations function exists
        const savedLanguage = localStorage.getItem('language') || 'en';
        if (typeof loadTranslations === 'function') {
            loadTranslations(savedLanguage);
        }
    }

    // Get DOM elements
    const loadingSpinner = document.getElementById('loading-spinner');
    const roomDetailsContent = document.getElementById('room-details-content');
    const roomNotFound = document.getElementById('room-not-found');
    const roomMainImage = document.getElementById('room-main-image');
    const roomGalleryThumbs = document.getElementById('room-gallery-thumbs');
    const roomTitle = document.getElementById('room-title');
    const roomBadges = document.getElementById('room-badges');
    const roomPrice = document.getElementById('room-price');
    const roomFeatures = document.getElementById('room-features');
    const roomDescriptionText = document.getElementById('room-description-text');
    const roomAmenitiesList = document.getElementById('room-amenities-list');
    const similarRoomsGrid = document.getElementById('similar-rooms-grid');
    const breakdownRate = document.getElementById('breakdown-rate');
    const breakdownTaxes = document.getElementById('breakdown-taxes');
    const breakdownTotal = document.getElementById('breakdown-total');
    const bookNowBtn = document.getElementById('book-now-btn');

    // Get room ID from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const roomId = urlParams.get('id');

    // If no room ID, show not found
    if (!roomId) {
        showRoomNotFound();
        return;
    }

    // Initialize date pickers
    if (typeof flatpickr !== 'undefined') {
        // Get current date and tomorrow's date
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        // Check-in date picker
        const checkInPicker = flatpickr('#details-check-in', {
            dateFormat: 'Y-m-d',
            minDate: today,
            defaultDate: today,
            onChange: function(selectedDates, dateStr) {
                // When check-in date changes, update check-out minimum date
                const checkOutDate = selectedDates[0];
                const nextDay = new Date(checkOutDate);
                nextDay.setDate(checkOutDate.getDate() + 1);
                
                checkOutPicker.set('minDate', nextDay);
                
                // If check-out date is now less than check-in date + 1, update it
                const currentCheckOutDate = checkOutPicker.selectedDates[0];
                if (currentCheckOutDate <= checkOutDate) {
                    checkOutPicker.setDate(nextDay);
                }
                
                // Update price calculation
                updatePriceCalculation();
            }
        });

        // Check-out date picker
        const checkOutPicker = flatpickr('#details-check-out', {
            dateFormat: 'Y-m-d',
            minDate: tomorrow,
            defaultDate: tomorrow,
            onChange: function() {
                // Update price calculation
                updatePriceCalculation();
            }
        });
    }

    // Load room details
    fetchRoomDetails(roomId);

    // Event listeners
    if (bookNowBtn) {
        bookNowBtn.addEventListener('click', function() {
            const checkInDate = document.getElementById('details-check-in').value;
            const checkOutDate = document.getElementById('details-check-out').value;
            const guests = document.getElementById('details-guests').value;
            
            if (!checkInDate || !checkOutDate) {
                alert('Please select check-in and check-out dates.');
                return;
            }
            
            // In a real implementation, this would send booking request to server
            // For this demo, just show a confirmation message
            alert(`Thank you for your booking request!\n\nRoom: ${roomTitle.textContent}\nCheck-in: ${checkInDate}\nCheck-out: ${checkOutDate}\nGuests: ${guests}\n\nYour booking is confirmed!`);
        });
    }

    // Guest dropdown change event
    const guestsDropdown = document.getElementById('details-guests');
    if (guestsDropdown) {
        guestsDropdown.addEventListener('change', function() {
            // In a real app, might update prices based on guest count
            updatePriceCalculation();
        });
    }

    // Fetch room details from API (simulated)
    function fetchRoomDetails(id) {
        // Show loading spinner
        loadingSpinner.style.display = 'flex';
        roomDetailsContent.style.display = 'none';
        roomNotFound.style.display = 'none';
        
        // Simulate API request delay
        setTimeout(() => {
            // This would be a real API call in production
            // For demo, we'll use sample room data
            const roomId = parseInt(id);
            
            // Sample room data (would come from API)
            const rooms = [
                {
                    id: 1,
                    name: 'Deluxe Ocean View',
                    type: 'deluxe',
                    price: 250,
                    capacity: 2,
                    view: 'ocean',
                    images: [
                        'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=1600',
                        'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=1600',
                        'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=1600',
                        'https://images.unsplash.com/photo-1629079447777-1e605162dc8d?q=80&w=1600'
                    ],
                    description: '<p>Experience luxury with stunning views of the ocean from your private balcony. This elegantly appointed Deluxe Ocean View room features modern amenities and stylish décor, perfect for a relaxing getaway.</p><p>Wake up to the sound of waves and breathtaking panoramic views of the blue ocean. The room is designed with comfort and luxury in mind, featuring premium bedding, a spacious layout, and thoughtful amenities.</p>',
                    features: ['breakfast', 'free_wifi', 'king_bed'],
                    amenities: ['Free WiFi', 'Air Conditioning', 'Flat-screen TV', 'Mini Bar', 'Coffee Maker', 'Safe', 'Premium Bedding', 'Bath Amenities', 'Daily Housekeeping', 'Room Service'],
                    iconAmenities: {
                        'Free WiFi': '📶',
                        'Air Conditioning': '❄️',
                        'Flat-screen TV': '📺',
                        'Mini Bar': '🍹',
                        'Coffee Maker': '☕',
                        'Safe': '🔒',
                        'Premium Bedding': '🛏️',
                        'Bath Amenities': '🧴',
                        'Daily Housekeeping': '🧹',
                        'Room Service': '🍽️'
                    },
                    rating: 4.8,
                    roomSize: '45 m²',
                    bedType: 'King Bed',
                    similarRooms: [2, 7, 8]
                },
                {
                    id: 2,
                    name: 'Family Suite',
                    type: 'suite',
                    price: 350,
                    capacity: 4,
                    view: 'garden',
                    images: [
                        'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=1600',
                        'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=1600',
                        'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=1600',
                        'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1600'
                    ],
                    description: '<p>Spacious accommodation perfect for families with separate living area. The Family Suite offers ample space for everyone, with a master bedroom and separate area for children or additional guests.</p><p>Enjoy garden views and a peaceful atmosphere, with all the amenities you need for a comfortable family vacation. The suite features a spacious bathroom, comfortable seating area, and premium entertainment options.</p>',
                    features: ['breakfast', 'free_cancellation'],
                    amenities: ['Free WiFi', 'Air Conditioning', 'Flat-screen TV', 'Mini Bar', 'Coffee Maker', 'Safe', 'Separate Living Area', 'Multiple Beds', 'Children\'s Amenities', 'Daily Housekeeping'],
                    iconAmenities: {
                        'Free WiFi': '📶',
                        'Air Conditioning': '❄️',
                        'Flat-screen TV': '📺',
                        'Mini Bar': '🍹',
                        'Coffee Maker': '☕',
                        'Safe': '🔒',
                        'Separate Living Area': '🛋️',
                        'Multiple Beds': '🛏️',
                        'Children\'s Amenities': '🧸',
                        'Daily Housekeeping': '🧹'
                    },
                    rating: 4.6,
                    roomSize: '65 m²',
                    bedType: 'King Bed + Twin Beds',
                    similarRooms: [4, 6, 9]
                },
                {
                    id: 3,
                    name: 'Presidential Suite',
                    type: 'suite',
                    price: 550,
                    capacity: 2,
                    view: 'ocean',
                    images: [
                        'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=1600',
                        'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?q=80&w=1600',
                        'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1600',
                        'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=1600'
                    ],
                    description: '<p>Our most exclusive accommodation with private pool and premium amenities. The Presidential Suite offers unparalleled luxury and an unforgettable stay experience.</p><p>Enjoy panoramic ocean views, a private terrace with infinity pool, spacious living areas, and personalized service. The suite includes premium furnishings, top-of-the-line entertainment systems, and exclusive amenities not available in other room types.</p>',
                    features: ['breakfast', 'free_cancellation', 'private_pool'],
                    amenities: ['Private Pool', 'Ocean View', 'Separate Living Room', 'Dining Area', 'Private Terrace', 'Premium WiFi', 'Smart Home Controls', 'VIP Amenities', 'Butler Service', 'Premium Bar'],
                    iconAmenities: {
                        'Private Pool': '🏊',
                        'Ocean View': '🌊',
                        'Separate Living Room': '🛋️',
                        'Dining Area': '🍽️',
                        'Private Terrace': '🏞️',
                        'Premium WiFi': '📶',
                        'Smart Home Controls': '🎛️',
                        'VIP Amenities': '🎁',
                        'Butler Service': '👨‍💼',
                        'Premium Bar': '🥂'
                    },
                    rating: 4.9,
                    roomSize: '120 m²',
                    bedType: 'King Bed',
                    similarRooms: [7, 8, 1]
                },
                // More rooms would be defined here
                {
                    id: 4,
                    name: 'Garden View Villa',
                    type: 'villa',
                    price: 450,
                    capacity: 6,
                    view: 'garden',
                    images: [
                        'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1600',
                        'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=1600',
                        'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1600',
                        'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=1600'
                    ],
                    description: '<p>Secluded villa with multiple bedrooms and a serene garden view. Perfect for larger families or groups seeking privacy and luxury.</p><p>This spacious villa features multiple bedrooms, a fully equipped kitchen, a private garden, and a comfortable living area. Enjoy the peaceful surroundings while still having access to all resort amenities.</p>',
                    features: ['breakfast', 'free_cancellation'],
                    amenities: ['Private Garden', 'Multiple Bedrooms', 'Full Kitchen', 'Dining Area', 'Living Room', 'Free WiFi', 'Air Conditioning', 'Washing Machine', 'Daily Housekeeping', 'BBQ Area'],
                    iconAmenities: {
                        'Private Garden': '🌳',
                        'Multiple Bedrooms': '🛏️',
                        'Full Kitchen': '🍳',
                        'Dining Area': '🍽️',
                        'Living Room': '🛋️',
                        'Free WiFi': '📶',
                        'Air Conditioning': '❄️',
                        'Washing Machine': '🧺',
                        'Daily Housekeeping': '🧹',
                        'BBQ Area': '🍖'
                    },
                    rating: 4.7,
                    roomSize: '120 m²',
                    bedType: 'Multiple Beds',
                    similarRooms: [2, 6, 9]
                },
                {
                    id: 5,
                    name: 'Standard Room',
                    type: 'standard',
                    price: 180,
                    capacity: 2,
                    view: 'mountain',
                    images: [
                        'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1600',
                        'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=1600',
                        'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=1600',
                        'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=1600'
                    ],
                    description: '<p>Comfortable and affordable accommodation with all essential amenities. Our Standard Room offers excellent value without compromising on quality.</p><p>Featuring a cozy atmosphere, comfortable bedding, and mountain views, this room provides all the essentials for a pleasant stay. Perfect for travelers who plan to spend most of their time exploring the resort and nearby attractions.</p>',
                    features: [],
                    amenities: ['Free WiFi', 'Air Conditioning', 'Flat-screen TV', 'Shower', 'Hair Dryer', 'Telephone', 'Safe', 'Desk', 'Wardrobe', 'Daily Housekeeping'],
                    iconAmenities: {
                        'Free WiFi': '📶',
                        'Air Conditioning': '❄️',
                        'Flat-screen TV': '📺',
                        'Shower': '🚿',
                        'Hair Dryer': '💨',
                        'Telephone': '☎️',
                        'Safe': '🔒',
                        'Desk': '🪑',
                        'Wardrobe': '👔',
                        'Daily Housekeeping': '🧹'
                    },
                    rating: 4.2,
                    roomSize: '32 m²',
                    bedType: 'Queen Bed',
                    similarRooms: [9, 6, 1]
                }
            ];
            
            // Find the room with matching ID
            const room = rooms.find(r => r.id === roomId);
            
            if (room) {
                // Display room details
                displayRoomDetails(room);
                
                // Display similar rooms
                if (room.similarRooms && room.similarRooms.length > 0) {
                    const similarRooms = room.similarRooms.map(id => 
                        rooms.find(r => r.id === id)
                    ).filter(r => r); // Filter out any undefined rooms
                    
                    displaySimilarRooms(similarRooms);
                }
                
                // Hide loading spinner, show content
                loadingSpinner.style.display = 'none';
                roomDetailsContent.style.display = 'block';
                
                // Initialize price calculation
                updatePriceCalculation(room.price);
            } else {
                // Room not found
                showRoomNotFound();
            }
        }, 1000); // Simulate 1 second loading time
    }

    // Display room details
    function displayRoomDetails(room) {
        // Set main image
        if (room.images && room.images.length > 0) {
            roomMainImage.src = room.images[0];
            roomMainImage.alt = room.name;
            
            // Set gallery thumbnails
            roomGalleryThumbs.innerHTML = '';
            room.images.forEach((image, index) => {
                const thumb = document.createElement('div');
                thumb.className = `gallery-thumb ${index === 0 ? 'active' : ''}`;
                thumb.innerHTML = `<img src="${image}" alt="${room.name} Image ${index + 1}">`;
                
                // Add click event to change main image
                thumb.addEventListener('click', function() {
                    roomMainImage.src = image;
                    
                    // Update active thumbnail
                    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
                
                roomGalleryThumbs.appendChild(thumb);
            });
        }
        
        // Set room title
        roomTitle.textContent = room.name;
        
        // Set room badges
        roomBadges.innerHTML = '';
        if (room.features) {
            if (room.features.includes('breakfast')) {
                const badge = document.createElement('span');
                badge.className = 'badge badge-primary';
                badge.textContent = 'Breakfast Included';
                roomBadges.appendChild(badge);
            }
            
            if (room.features.includes('free_cancellation')) {
                const badge = document.createElement('span');
                badge.className = 'badge badge-secondary';
                badge.textContent = 'Free Cancellation';
                roomBadges.appendChild(badge);
            }
            
            if (room.features.includes('private_pool')) {
                const badge = document.createElement('span');
                badge.className = 'badge badge-success';
                badge.textContent = 'Private Pool';
                roomBadges.appendChild(badge);
            }
        }
        
        // Set room price
        roomPrice.textContent = '$' + room.price;
        
        // Set room features
        roomFeatures.innerHTML = '';
        
        // Guest capacity feature
        const guestsFeature = document.createElement('div');
        guestsFeature.className = 'room-feature';
        guestsFeature.innerHTML = `
            <div class="feature-icon">👥</div>
            <div class="feature-text">${room.capacity} Guests</div>
        `;
        roomFeatures.appendChild(guestsFeature);
        
        // Room size feature
        const sizeFeature = document.createElement('div');
        sizeFeature.className = 'room-feature';
        sizeFeature.innerHTML = `
            <div class="feature-icon">📏</div>
            <div class="feature-text">${room.roomSize}</div>
        `;
        roomFeatures.appendChild(sizeFeature);
        
        // Bed type feature
        const bedFeature = document.createElement('div');
        bedFeature.className = 'room-feature';
        bedFeature.innerHTML = `
            <div class="feature-icon">🛏️</div>
            <div class="feature-text">${room.bedType}</div>
        `;
        roomFeatures.appendChild(bedFeature);
        
        // View feature
        const viewFeature = document.createElement('div');
        viewFeature.className = 'room-feature';
        const viewIcon = room.view === 'ocean' ? '🌊' : room.view === 'garden' ? '🌳' : room.view === 'pool' ? '🏊' : '🏔️';
        const viewText = room.view ? room.view.charAt(0).toUpperCase() + room.view.slice(1) + ' View' : 'Mountain View';
        viewFeature.innerHTML = `
            <div class="feature-icon">${viewIcon}</div>
            <div class="feature-text">${viewText}</div>
        `;
        roomFeatures.appendChild(viewFeature);
        
        // Set room description
        roomDescriptionText.innerHTML = room.description || 'No description available.';
        
        // Set room amenities
        roomAmenitiesList.innerHTML = '';
        if (room.amenities && room.amenities.length > 0) {
            room.amenities.forEach(amenity => {
                const amenityItem = document.createElement('div');
                amenityItem.className = 'amenity-item';
                
                // Get icon for amenity if available
                const icon = room.iconAmenities && room.iconAmenities[amenity] ? room.iconAmenities[amenity] : '✓';
                
                amenityItem.innerHTML = `
                    <span class="amenity-icon">${icon}</span>
                    <span>${amenity}</span>
                `;
                roomAmenitiesList.appendChild(amenityItem);
            });
        } else {
            roomAmenitiesList.innerHTML = '<p>No specific amenities listed</p>';
        }
    }

    // Display similar rooms
    function displaySimilarRooms(rooms) {
        similarRoomsGrid.innerHTML = '';
        
        rooms.forEach(room => {
            const roomCard = document.createElement('div');
            roomCard.className = 'room-card';
            
            // Determine image to use
            const imageUrl = room.images && room.images.length > 0 
                ? room.images[0] 
                : 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=800';
            
            roomCard.innerHTML = `
                <div class="room-card-image">
                    <img src="${imageUrl}" alt="${room.name}">
                    <div class="room-card-badges">
                        ${room.features && room.features.includes('breakfast') ? 
                            '<span class="badge badge-primary">Breakfast Included</span>' : ''}
                    </div>
                </div>
                <div class="room-card-content">
                    <h3 class="room-card-title">${room.name}</h3>
                    <div class="room-card-footer">
                        <div class="room-price">
                            <span class="price">$${room.price}</span> / night
                        </div>
                        <a href="room-details.html?id=${room.id}" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            `;
            
            similarRoomsGrid.appendChild(roomCard);
        });
    }

    // Update price calculation in booking form
    function updatePriceCalculation(roomPrice) {
        // Get room price from current room if not provided
        const price = roomPrice || parseInt(document.getElementById('room-price').textContent.replace('$', '')) || 0;
        
        // Get number of nights based on check-in and check-out dates
        const checkInDate = document.getElementById('details-check-in').value;
        const checkOutDate = document.getElementById('details-check-out').value;
        
        let nights = 1;
        if (checkInDate && checkOutDate) {
            const date1 = new Date(checkInDate);
            const date2 = new Date(checkOutDate);
            const timeDiff = Math.abs(date2.getTime() - date1.getTime());
            nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
        }
        
        // Calculate total room rate
        const roomRate = price * nights;
        
        // Calculate taxes (10%)
        const taxes = Math.round(roomRate * 0.1);
        
        // Calculate total
        const total = roomRate + taxes;
        
        // Update display
        breakdownRate.textContent = '$' + roomRate;
        breakdownTaxes.textContent = '$' + taxes;
        breakdownTotal.textContent = '$' + total;
    }

    // Show room not found message
    function showRoomNotFound() {
        loadingSpinner.style.display = 'none';
        roomDetailsContent.style.display = 'none';
        roomNotFound.style.display = 'block';
    }
}); 