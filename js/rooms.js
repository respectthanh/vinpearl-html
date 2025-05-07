/**
 * Vinpearl Resort - Rooms Listing Page JavaScript
 * Handles filtering, sorting, and pagination of room listings
 */

document.addEventListener('DOMContentLoaded', function() {
    // Room page specific translations
    const roomTranslations = {
        'en': {
            'rooms.title': 'Our Luxurious Rooms',
            'rooms.subtitle': 'Experience comfort and elegance in our beautifully designed accommodations',
            'rooms.filterTitle': 'Filter Rooms',
            'rooms.roomType': 'Room Type',
            'rooms.all': 'All Types',
            'rooms.standard': 'Standard',
            'rooms.deluxe': 'Deluxe',
            'rooms.suite': 'Suite',
            'rooms.villa': 'Villa',
            'rooms.priceRange': 'Price Range',
            'rooms.allPrices': 'All Prices',
            'rooms.economy': 'Economy ($0-$200)',
            'rooms.midRange': 'Mid-range ($200-$350)',
            'rooms.luxury': 'Luxury ($350-$500)',
            'rooms.premium': 'Premium ($500+)',
            'rooms.guests': 'Guests',
            'rooms.anyGuests': 'Any',
            'rooms.oneToTwo': '1-2 Guests',
            'rooms.threeToFour': '3-4 Guests',
            'rooms.fivePlus': '5+ Guests',
            'rooms.view': 'View',
            'rooms.anyView': 'Any View',
            'rooms.oceanView': 'Ocean View',
            'rooms.gardenView': 'Garden View',
            'rooms.poolView': 'Pool View',
            'rooms.mountainView': 'Mountain View',
            'rooms.applyFilters': 'Apply Filters',
            'rooms.roomsFound': 'rooms found',
            'rooms.sortBy': 'Sort by:',
            'rooms.recommended': 'Recommended',
            'rooms.priceLowToHigh': 'Price: Low to High',
            'rooms.priceHighToLow': 'Price: High to Low',
            'rooms.highestRated': 'Highest Rated',
            'rooms.noResults': 'No rooms match your filters',
            'rooms.tryDifferent': 'Please try different filter options',
            'rooms.resetFilters': 'Reset Filters',
            'rooms.previous': 'Previous',
            'rooms.next': 'Next',
            'rooms.viewDetails': 'View Details',
            'rooms.whyBookDirect': 'Why Book Directly With Us',
            'rooms.directBenefitsDesc': 'Enjoy these exclusive benefits when booking through our official website',
            'rooms.bestRateGuarantee': 'Best Rate Guarantee',
            'rooms.bestRateDesc': 'Find a lower price elsewhere? We\'ll match it and give you an additional 10% off.',
            'rooms.freeBreakfast': 'Complimentary Breakfast',
            'rooms.breakfastDesc': 'Enjoy a free breakfast for all guests when booking directly on our website.',
            'rooms.earlyCheckIn': 'Early Check-In & Late Check-Out',
            'rooms.checkInDesc': 'Subject to availability, enjoy early check-in and late check-out privileges.',
            'rooms.freeCancel': 'Free Cancellation',
            'rooms.cancelDesc': 'Flexible cancellation policy when booking directly with us.',
            'rooms.readyToBook': 'Ready to Experience Luxury?',
            'rooms.bookingPrompt': 'Book your perfect room at Vinpearl Nha Trang and enjoy an unforgettable stay.',
            'rooms.bookNow': 'Book Now'
        },
        'vi': {
            'rooms.title': 'Các Phòng Sang Trọng',
            'rooms.subtitle': 'Trải nghiệm sự thoải mái và sang trọng trong các phòng được thiết kế đẹp mắt',
            'rooms.filterTitle': 'Bộ Lọc Phòng',
            'rooms.roomType': 'Loại Phòng',
            'rooms.all': 'Tất Cả',
            'rooms.standard': 'Tiêu Chuẩn',
            'rooms.deluxe': 'Cao Cấp',
            'rooms.suite': 'Phòng Suite',
            'rooms.villa': 'Biệt Thự',
            'rooms.priceRange': 'Khoảng Giá',
            'rooms.allPrices': 'Tất Cả Giá',
            'rooms.economy': 'Tiết Kiệm ($0-$200)',
            'rooms.midRange': 'Trung Cấp ($200-$350)',
            'rooms.luxury': 'Cao Cấp ($350-$500)',
            'rooms.premium': 'Đặc Biệt ($500+)',
            'rooms.guests': 'Số Khách',
            'rooms.anyGuests': 'Bất Kỳ',
            'rooms.oneToTwo': '1-2 Khách',
            'rooms.threeToFour': '3-4 Khách',
            'rooms.fivePlus': '5+ Khách',
            'rooms.view': 'Tầm Nhìn',
            'rooms.anyView': 'Bất Kỳ',
            'rooms.oceanView': 'Hướng Biển',
            'rooms.gardenView': 'Hướng Vườn',
            'rooms.poolView': 'Hướng Hồ Bơi',
            'rooms.mountainView': 'Hướng Núi',
            'rooms.applyFilters': 'Áp Dụng Bộ Lọc',
            'rooms.roomsFound': 'phòng được tìm thấy',
            'rooms.sortBy': 'Sắp xếp:',
            'rooms.recommended': 'Đề Xuất',
            'rooms.priceLowToHigh': 'Giá: Thấp đến Cao',
            'rooms.priceHighToLow': 'Giá: Cao đến Thấp',
            'rooms.highestRated': 'Đánh Giá Cao Nhất',
            'rooms.noResults': 'Không có phòng nào phù hợp với bộ lọc',
            'rooms.tryDifferent': 'Vui lòng thử các tùy chọn bộ lọc khác',
            'rooms.resetFilters': 'Đặt Lại Bộ Lọc',
            'rooms.previous': 'Trước',
            'rooms.next': 'Tiếp',
            'rooms.viewDetails': 'Xem Chi Tiết',
            'rooms.whyBookDirect': 'Tại Sao Đặt Phòng Trực Tiếp',
            'rooms.directBenefitsDesc': 'Tận hưởng những đặc quyền này khi đặt phòng qua trang web chính thức của chúng tôi',
            'rooms.bestRateGuarantee': 'Đảm Bảo Giá Tốt Nhất',
            'rooms.bestRateDesc': 'Tìm thấy giá thấp hơn ở nơi khác? Chúng tôi sẽ so khớp và giảm thêm 10%.',
            'rooms.freeBreakfast': 'Bữa Sáng Miễn Phí',
            'rooms.breakfastDesc': 'Thưởng thức bữa sáng miễn phí cho tất cả khách khi đặt phòng trực tiếp.',
            'rooms.earlyCheckIn': 'Nhận Phòng Sớm & Trả Phòng Muộn',
            'rooms.checkInDesc': 'Tùy thuộc vào tình trạng phòng, được nhận phòng sớm và trả phòng muộn.',
            'rooms.freeCancel': 'Hủy Miễn Phí',
            'rooms.cancelDesc': 'Chính sách hủy linh hoạt khi đặt phòng trực tiếp với chúng tôi.',
            'rooms.readyToBook': 'Sẵn Sàng Trải Nghiệm Sang Trọng?',
            'rooms.bookingPrompt': 'Đặt phòng tại Vinpearl Nha Trang và tận hưởng kỳ nghỉ khó quên.',
            'rooms.bookNow': 'Đặt Ngay'
        }
    };

    // Add room translations to main translations object
    if (typeof window.allTranslations === 'object') {
        if (!window.allTranslations.en) {
            window.allTranslations.en = {};
        }
        if (!window.allTranslations.vi) {
            window.allTranslations.vi = {};
        }
        
        // Merge room translations with main translations
        Object.assign(window.allTranslations.en, roomTranslations.en);
        Object.assign(window.allTranslations.vi, roomTranslations.vi);
        
        // Load translations for current language if loadTranslations function exists
        const savedLanguage = localStorage.getItem('language') || 'en';
        if (typeof loadTranslations === 'function') {
            loadTranslations(savedLanguage);
        }
    }

    // Initialize Date Pickers
    if (typeof flatpickr !== 'undefined') {
        // Get current date for min date setting
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        // Format date to YYYY-MM-DD
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        // Check-in date picker
        const checkInPicker = flatpickr('#check-in-date', {
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
            }
        });

        // Check-out date picker
        const checkOutPicker = flatpickr('#check-out-date', {
            dateFormat: 'Y-m-d',
            minDate: tomorrow,
            defaultDate: tomorrow
        });
    }

    // Initialize price range slider
    const priceRangeSlider = document.getElementById('price-range-slider');
    const currentPrice = document.getElementById('current-price');
    
    if (priceRangeSlider && currentPrice) {
        priceRangeSlider.addEventListener('input', function() {
            currentPrice.textContent = this.value;
        });
    }

    // Initialize FAQ accordions
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            // Toggle active class for current item
            item.classList.toggle('active');
            
            // Close other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });
        });
    });

    // Room filtering and sorting functionality
    const roomsGrid = document.getElementById('rooms-grid');
    const noResults = document.getElementById('no-results');
    const resultsCount = document.getElementById('results-count');
    const sortBySelect = document.getElementById('sort-by');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const resetFiltersInlineBtn = document.getElementById('reset-filters-inline');
    const loadingSpinner = document.getElementById('loading-spinner');
    const roomCardTemplate = document.getElementById('room-card-template');
    const searchAvailabilityBtn = document.getElementById('search-availability');
    const bookingGuests = document.getElementById('booking-guests');
    
    // Type checkboxes
    const typeCheckboxes = document.querySelectorAll('input[name="type"]');
    // View checkboxes
    const viewCheckboxes = document.querySelectorAll('input[name="view"]');
    // Amenities checkboxes
    const amenitiesCheckboxes = document.querySelectorAll('input[name="amenities"]');
    
    // Initialize room data
    let allRooms = [];
    let currentPage = 1;
    const itemsPerPage = 6;
    
    // Initialize pagination elements
    const paginationPrev = document.querySelector('.pagination-prev');
    const paginationNext = document.querySelector('.pagination-next');
    const paginationNumbers = document.querySelector('.pagination-numbers');

    // Update search-availability button to use check-in/check-out dates
    if (searchAvailabilityBtn) {
        searchAvailabilityBtn.addEventListener('click', function() {
            const checkInDate = document.getElementById('check-in-date').value;
            const checkOutDate = document.getElementById('check-out-date').value;
            const guests = bookingGuests.value;

            // Reset other filters
            resetFilters();

            // Build filter object from dates and guests
            const filters = {
                checkIn: checkInDate,
                checkOut: checkOutDate,
                guests: guests
            };

            // Apply filters
            fetchRooms(filters);
        });
    }
    
    // Apply filters event
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyFilters();
        });
    }
    
    // Reset filters events
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            resetFilters();
            fetchRooms();
        });
    }
    
    if (resetFiltersInlineBtn) {
        resetFiltersInlineBtn.addEventListener('click', function() {
            resetFilters();
            fetchRooms();
        });
    }
    
    // Sort event
    if (sortBySelect) {
        sortBySelect.addEventListener('change', function() {
            applyFilters();
        });
    }
    
    // Function to collect filter values and apply
    function applyFilters() {
        // Selected room types
        const selectedTypes = Array.from(typeCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        
        // Selected views
        const selectedViews = Array.from(viewCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        
        // Selected amenities
        const selectedAmenities = Array.from(amenitiesCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        
        // Price range
        const maxPrice = priceRangeSlider ? priceRangeSlider.value : 1000;
        
        // Sort option
        const sortBy = sortBySelect ? sortBySelect.value : 'recommended';
        
        // Build filter object
        const filters = {
            types: selectedTypes,
            views: selectedViews,
            amenities: selectedAmenities,
            maxPrice: maxPrice,
            sortBy: sortBy
        };
        
        // Reset pagination to first page when filters change
        currentPage = 1;
        
        // Apply filters
        fetchRooms(filters);
    }
    
    // Function to reset all filters
    function resetFilters() {
        // Reset checkboxes
        typeCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        viewCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        amenitiesCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset price range slider
        if (priceRangeSlider) {
            priceRangeSlider.value = 500;
            if (currentPrice) {
                currentPrice.textContent = '500';
            }
        }
        
        // Reset sort selection
        if (sortBySelect) {
            sortBySelect.value = 'recommended';
        }
    }
    
    // Fetch rooms from API
    function fetchRooms(filters = {}) {
        // Show loading spinner
        loadingSpinner.style.display = 'flex';
        roomsGrid.innerHTML = '';
        roomsGrid.appendChild(loadingSpinner);
        
        // Build query parameters
        let queryParams = new URLSearchParams();
        queryParams.append('page', currentPage);
        queryParams.append('limit', itemsPerPage);
        
        // Add check-in/check-out dates if provided
        if (filters.checkIn) {
            queryParams.append('check_in', filters.checkIn);
        }
        
        if (filters.checkOut) {
            queryParams.append('check_out', filters.checkOut);
        }
        
        // Add guest count if provided
        if (filters.guests) {
            queryParams.append('guests', filters.guests);
        }
        
        // Add room types if selected
        if (filters.types && filters.types.length > 0) {
            queryParams.append('types', filters.types.join(','));
        }
        
        // Add room views if selected
        if (filters.views && filters.views.length > 0) {
            queryParams.append('views', filters.views.join(','));
        }
        
        // Add amenities if selected
        if (filters.amenities && filters.amenities.length > 0) {
            queryParams.append('amenities', filters.amenities.join(','));
        }
        
        // Add max price if set
        if (filters.maxPrice) {
            queryParams.append('max_price', filters.maxPrice);
        }
        
        // Add sort option
        if (filters.sortBy && filters.sortBy !== 'recommended') {
            if (filters.sortBy === 'price_asc') {
                queryParams.append('sort_by', 'price');
                queryParams.append('sort_dir', 'ASC');
            } else if (filters.sortBy === 'price_desc') {
                queryParams.append('sort_by', 'price');
                queryParams.append('sort_dir', 'DESC');
            } else if (filters.sortBy === 'name_asc') {
                queryParams.append('sort_by', 'rating');
                queryParams.append('sort_dir', 'DESC');
            }
        }
        
        // In a real implementation, this would fetch from an API endpoint
        // For this static demo, we'll simulate an API response
        simulateFetchRooms(queryParams);
    }
    
    // Simulate API fetch for the static demo
    function simulateFetchRooms(queryParams) {
        // Simulate network delay
        setTimeout(() => {
            // Sample room data
            const sampleRooms = [
                {
                    id: 1,
                    name: 'Deluxe Ocean View',
                    type: 'deluxe',
                    price: 250,
                    capacity: 2,
                    view: 'ocean',
                    images: ['deluxe-ocean.jpg'],
                    description: 'Experience luxury with stunning views of the ocean from your private balcony.',
                    features: ['breakfast', 'free_wifi', 'king_bed'],
                    amenities: ['wifi', 'aircon', 'minibar'],
                    rating: 4.8
                },
                {
                    id: 2,
                    name: 'Family Suite',
                    type: 'suite',
                    price: 350,
                    capacity: 4,
                    view: 'garden',
                    images: ['family-suite.jpg'],
                    description: 'Spacious accommodation perfect for families with separate living area.',
                    features: ['breakfast', 'free_cancellation'],
                    amenities: ['wifi', 'aircon', 'balcony'],
                    rating: 4.6
                },
                {
                    id: 3,
                    name: 'Presidential Suite',
                    type: 'suite',
                    price: 550,
                    capacity: 2,
                    view: 'ocean',
                    images: ['presidential-suite.jpg'],
                    description: 'Our most exclusive accommodation with private pool and premium amenities.',
                    features: ['breakfast', 'free_cancellation', 'private_pool'],
                    amenities: ['wifi', 'aircon', 'minibar', 'pool'],
                    rating: 4.9
                },
                {
                    id: 4,
                    name: 'Garden View Villa',
                    type: 'villa',
                    price: 450,
                    capacity: 6,
                    view: 'garden',
                    images: ['garden-villa.jpg'],
                    description: 'Secluded villa with multiple bedrooms and a serene garden view.',
                    features: ['breakfast', 'free_cancellation'],
                    amenities: ['wifi', 'aircon', 'balcony'],
                    rating: 4.7
                },
                {
                    id: 5,
                    name: 'Standard Room',
                    type: 'standard',
                    price: 180,
                    capacity: 2,
                    view: 'mountain',
                    images: ['standard-room.jpg'],
                    description: 'Comfortable and affordable accommodation with all essential amenities.',
                    features: [],
                    amenities: ['wifi', 'aircon'],
                    rating: 4.2
                },
                {
                    id: 6,
                    name: 'Poolside Suite',
                    type: 'suite',
                    price: 380,
                    capacity: 3,
                    view: 'pool',
                    images: ['poolside-suite.jpg'],
                    description: 'Elegant suite with direct access to the swimming pool.',
                    features: ['breakfast', 'free_cancellation'],
                    amenities: ['wifi', 'aircon', 'minibar', 'pool'],
                    rating: 4.5
                },
                {
                    id: 7,
                    name: 'Honeymoon Suite',
                    type: 'suite',
                    price: 480,
                    capacity: 2,
                    view: 'ocean',
                    images: ['honeymoon-suite.jpg'],
                    description: 'Romantic suite with jacuzzi and panoramic ocean views.',
                    features: ['breakfast', 'free_cancellation'],
                    amenities: ['wifi', 'aircon', 'minibar', 'balcony'],
                    rating: 4.9
                },
                {
                    id: 8,
                    name: 'Beachfront Bungalow',
                    type: 'villa',
                    price: 520,
                    capacity: 4,
                    view: 'ocean',
                    images: ['beachfront-bungalow.jpg'],
                    description: 'Private bungalow steps away from the white sands of Nha Trang beach.',
                    features: ['breakfast', 'free_cancellation', 'private_pool'],
                    amenities: ['wifi', 'aircon', 'minibar', 'pool'],
                    rating: 4.8
                },
                {
                    id: 9,
                    name: 'Deluxe Twin Room',
                    type: 'deluxe',
                    price: 220,
                    capacity: 2,
                    view: 'mountain',
                    images: ['deluxe-twin.jpg'],
                    description: 'Spacious room with two comfortable beds and mountain views.',
                    features: ['breakfast'],
                    amenities: ['wifi', 'aircon'],
                    rating: 4.3
                }
            ];
            
            // Apply filters from query parameters
            let filteredRooms = [...sampleRooms];
            
            // Get parameters from queryParams
            const params = {};
            queryParams.forEach((value, key) => {
                params[key] = value;
            });
            
            // Filter by room types
            if (params.types) {
                const types = params.types.split(',');
                filteredRooms = filteredRooms.filter(room => types.includes(room.type));
            }
            
            // Filter by views
            if (params.views) {
                const views = params.views.split(',');
                filteredRooms = filteredRooms.filter(room => views.includes(room.view));
            }
            
            // Filter by amenities
            if (params.amenities) {
                const amenities = params.amenities.split(',');
                filteredRooms = filteredRooms.filter(room => 
                    amenities.every(amenity => room.amenities.includes(amenity))
                );
            }
            
            // Filter by max price
            if (params.max_price) {
                const maxPrice = parseInt(params.max_price);
                filteredRooms = filteredRooms.filter(room => room.price <= maxPrice);
            }
            
            // Filter by guest capacity
            if (params.guests) {
                const guests = parseInt(params.guests);
                filteredRooms = filteredRooms.filter(room => room.capacity >= guests);
            }
            
            // Sort rooms
            if (params.sort_by) {
                if (params.sort_by === 'price') {
                    filteredRooms.sort((a, b) => {
                        return params.sort_dir === 'ASC' ? a.price - b.price : b.price - a.price;
                    });
                } else if (params.sort_by === 'rating') {
                    filteredRooms.sort((a, b) => b.rating - a.rating);
                }
            }
            
            // Calculate pagination
            const totalRooms = filteredRooms.length;
            const totalPages = Math.ceil(totalRooms / itemsPerPage);
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            
            // Get current page rooms
            const paginatedRooms = filteredRooms.slice(startIndex, endIndex);
            
            // Create response object similar to API response
            const response = {
                rooms: paginatedRooms,
                pagination: {
                    total_rooms: totalRooms,
                    current_page: currentPage,
                    total_pages: totalPages,
                    items_per_page: itemsPerPage
                }
            };
            
            // Process the "response"
            // Hide loading spinner
            loadingSpinner.style.display = 'none';
            
            // Store rooms data
            allRooms = response.rooms || [];
            
            // Display rooms
            displayRooms(allRooms);
            
            // Update pagination based on API response
            updatePagination(response.pagination);
            
            // Update results count
            resultsCount.textContent = response.pagination.total_rooms;
            
            // Show no results message if needed
            if (allRooms.length === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }, 800); // 800ms delay to simulate network
    }
    
    // Display rooms in the grid
    function displayRooms(rooms) {
        // Clear existing rooms
        roomsGrid.innerHTML = '';
        
        // If no rooms, show no results message
        if (rooms.length === 0) {
            noResults.style.display = 'block';
            return;
        }
        
        // Hide no results message
        noResults.style.display = 'none';
        
        // Create room cards
        rooms.forEach(room => {
            // Clone template
            const roomCard = document.importNode(roomCardTemplate.content, true).querySelector('.room-card');
            
            // Set room data attributes
            roomCard.setAttribute('data-type', room.type || '');
            roomCard.setAttribute('data-price', room.price || 0);
            roomCard.setAttribute('data-guests', room.capacity || 1);
            roomCard.setAttribute('data-view', room.view || 'standard');
            
            // Set room image
            const roomImage = roomCard.querySelector('.room-card-image img');
            if (room.images && room.images.length > 0) {
                // For the static demo, use Unsplash images based on room type
                let imageUrl;
                switch(room.type) {
                    case 'deluxe':
                        imageUrl = 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=800';
                        break;
                    case 'suite':
                        imageUrl = 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=800';
                        break;
                    case 'villa':
                        imageUrl = 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=800';
                        break;
                    default:
                        imageUrl = 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=800';
                }
                roomImage.src = imageUrl;
                roomImage.alt = room.name;
            } else {
                // Use a placeholder image if none is available
                roomImage.src = 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=800';
                roomImage.alt = room.name;
            }
            
            // Set badges
            const badgesContainer = roomCard.querySelector('.room-card-badges');
            badgesContainer.innerHTML = '';
            
            if (room.features) {
                if (room.features.includes('breakfast')) {
                    const badge = document.createElement('span');
                    badge.className = 'badge badge-primary';
                    badge.textContent = 'Breakfast Included';
                    badgesContainer.appendChild(badge);
                }
                
                if (room.features.includes('free_cancellation')) {
                    const badge = document.createElement('span');
                    badge.className = 'badge badge-secondary';
                    badge.textContent = 'Free Cancellation';
                    badgesContainer.appendChild(badge);
                }
                
                if (room.features.includes('private_pool')) {
                    const badge = document.createElement('span');
                    badge.className = 'badge badge-success';
                    badge.textContent = 'Private Pool';
                    badgesContainer.appendChild(badge);
                }
            }
            
            // Set room title
            roomCard.querySelector('.room-card-title').textContent = room.name;
            
            // Set room features
            const featuresContainer = roomCard.querySelector('.room-card-features');
            featuresContainer.innerHTML = '';
            
            // Guests feature
            const guestsFeature = document.createElement('div');
            guestsFeature.className = 'room-feature';
            guestsFeature.innerHTML = `
                <span class="room-feature-icon">👥</span>
                <span class="room-feature-text">${room.capacity || 2} Guests</span>
            `;
            featuresContainer.appendChild(guestsFeature);
            
            // Bed feature - determine bed type based on capacity
            const bedFeature = document.createElement('div');
            bedFeature.className = 'room-feature';
            
            let bedType;
            if (room.capacity === 1) {
                bedType = 'Single Bed';
            } else if (room.capacity === 2) {
                bedType = 'King Bed';
            } else {
                bedType = 'Multiple Beds';
            }
            
            bedFeature.innerHTML = `
                <span class="room-feature-icon">🛏️</span>
                <span class="room-feature-text">${bedType}</span>
            `;
            featuresContainer.appendChild(bedFeature);
            
            // Size feature - generate random size based on room type
            const sizeFeature = document.createElement('div');
            sizeFeature.className = 'room-feature';
            
            let roomSize;
            switch(room.type) {
                case 'standard':
                    roomSize = '32-40';
                    break;
                case 'deluxe':
                    roomSize = '45-55';
                    break;
                case 'suite':
                    roomSize = '65-80';
                    break;
                case 'villa':
                    roomSize = '100-150';
                    break;
                default:
                    roomSize = '40';
            }
            
            sizeFeature.innerHTML = `
                <span class="room-feature-icon">📏</span>
                <span class="room-feature-text">${roomSize} m²</span>
            `;
            featuresContainer.appendChild(sizeFeature);
            
            // View feature
            const viewFeature = document.createElement('div');
            viewFeature.className = 'room-feature';
            const viewIcon = room.view === 'ocean' ? '🌊' : room.view === 'garden' ? '🌳' : room.view === 'pool' ? '🏊' : '🏔️';
            const viewText = room.view ? room.view.charAt(0).toUpperCase() + room.view.slice(1) + ' View' : 'Mountain View';
            viewFeature.innerHTML = `
                <span class="room-feature-icon">${viewIcon}</span>
                <span class="room-feature-text">${viewText}</span>
            `;
            featuresContainer.appendChild(viewFeature);
            
            // Set room description
            roomCard.querySelector('.room-card-description p').textContent = room.description || 'Enjoy a comfortable stay in our beautifully designed room with all modern amenities.';
            
            // Set room price
            roomCard.querySelector('.price').textContent = '$' + (room.price || 0);
            
            // Set details link
            roomCard.querySelector('.room-card-footer a').href = `room-details.html?id=${room.id}`;
            
            // Add to the grid
            roomsGrid.appendChild(roomCard);
        });
    }
    
    // Update pagination based on server response
    function updatePagination(pagination) {
        if (!pagination) return;
        
        const totalPages = pagination.total_pages || 1;
        currentPage = pagination.current_page || 1;
        
        // Set previous button state
        paginationPrev.disabled = currentPage === 1;
        
        // Set next button state
        paginationNext.disabled = currentPage === totalPages;
        
        // Update pagination numbers
        paginationNumbers.innerHTML = '';
        
        // Determine how many page numbers to show (max 5)
        const maxPagesToShow = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
        
        // Adjust start page if needed
        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }
        
        // Add first page if not in range
        if (startPage > 1) {
            const firstPageBtn = document.createElement('button');
            firstPageBtn.classList.add('pagination-number');
            firstPageBtn.textContent = '1';
            firstPageBtn.addEventListener('click', () => {
                goToPage(1);
            });
            paginationNumbers.appendChild(firstPageBtn);
            
            // Add ellipsis if needed
            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.classList.add('pagination-ellipsis');
                ellipsis.textContent = '...';
                paginationNumbers.appendChild(ellipsis);
            }
        }
        
        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.classList.add('pagination-number');
            if (i === currentPage) {
                pageButton.classList.add('active');
            }
            pageButton.textContent = i;
            pageButton.addEventListener('click', () => {
                goToPage(i);
            });
            paginationNumbers.appendChild(pageButton);
        }
        
        // Add last page if not in range
        if (endPage < totalPages) {
            // Add ellipsis if needed
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.classList.add('pagination-ellipsis');
                ellipsis.textContent = '...';
                paginationNumbers.appendChild(ellipsis);
            }
            
            const lastPageBtn = document.createElement('button');
            lastPageBtn.classList.add('pagination-number');
            lastPageBtn.textContent = totalPages;
            lastPageBtn.addEventListener('click', () => {
                goToPage(totalPages);
            });
            paginationNumbers.appendChild(lastPageBtn);
        }
        
        // Add event listeners to previous and next buttons
        paginationPrev.onclick = function() {
            if (currentPage > 1) {
                goToPage(currentPage - 1);
            }
        };
        
        paginationNext.onclick = function() {
            if (currentPage < totalPages) {
                goToPage(currentPage + 1);
            }
        };
    }
    
    // Go to a specific page
    function goToPage(page) {
        if (page !== currentPage) {
            currentPage = page;
            // Get current filters
            applyFilters();
        }
    }
    
    // Initial load of rooms
    fetchRooms();
});