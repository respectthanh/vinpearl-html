document.addEventListener('DOMContentLoaded', function() {
  // Mobile menu toggle
  const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
  const mainNav = document.querySelector('.main-nav');
  
  if (mobileMenuToggle && mainNav) {
    mobileMenuToggle.addEventListener('click', function() {
      this.classList.toggle('active');
      mainNav.classList.toggle('active');
    });
  }

  // Check if user is logged in and update header
  checkUserLoginStatus();
  
  // Language selector dropdown
  const languageToggle = document.getElementById('language-toggle');
  const languageDropdown = document.querySelector('.language-dropdown');
  const languageOptions = document.querySelectorAll('.language-dropdown a');
  
  if (languageToggle && languageDropdown) {
    // Toggle language dropdown
    languageToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      languageDropdown.classList.toggle('active');
    });
    
    // Close dropdown when clicking elsewhere
    document.addEventListener('click', function() {
      languageDropdown.classList.remove('active');
    });
    
    // Prevent closing when clicking inside dropdown
    languageDropdown.addEventListener('click', function(e) {
      e.stopPropagation();
    });
    
    // Handle language selection
    languageOptions.forEach(option => {
      option.addEventListener('click', function(e) {
        e.preventDefault();
        
        const lang = this.getAttribute('data-lang');
        languageToggle.textContent = lang === 'en' ? 'EN' : 'VI';
        
        // Remove active class from all options
        languageOptions.forEach(opt => opt.classList.remove('active'));
        
        // Add active class to selected option
        this.classList.add('active');
        
        // Close dropdown
        languageDropdown.classList.remove('active');
        
        // Store language preference
        localStorage.setItem('language', lang);
        
        // Load translations for the selected language
        loadTranslations(lang);
      });
    });
    
    // Set initial language based on localStorage or default to English
    const savedLanguage = localStorage.getItem('language') || 'en';
    languageToggle.textContent = savedLanguage === 'en' ? 'EN' : 'VI';
    
    // Set the active class on the correct language option
    languageOptions.forEach(option => {
      if (option.getAttribute('data-lang') === savedLanguage) {
        option.classList.add('active');
      } else {
        option.classList.remove('active');
      }
    });
    
    // Load translations for the initial language
    loadTranslations(savedLanguage);
  }

  // Scroll reveal functionality
  const scrollRevealElements = document.querySelectorAll('.scroll-reveal');
  
  const isElementInViewport = (el) => {
    const rect = el.getBoundingClientRect();
    return (
      rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.75 &&
      rect.bottom >= 0
    );
  };
  
  const handleScrollReveal = () => {
    scrollRevealElements.forEach(element => {
      if (isElementInViewport(element) && !element.classList.contains('scroll-visible')) {
        element.classList.add('scroll-visible');
      }
    });
  };
  
  // Initial check on page load
  handleScrollReveal();
  
  // Check on scroll
  window.addEventListener('scroll', handleScrollReveal);
  
  // Smooth scroll for anchor links
  const anchorLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])');
  
  anchorLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      const targetId = this.getAttribute('href');
      const targetElement = document.querySelector(targetId);
      
      if (targetElement) {
        const offsetTop = targetElement.offsetTop;
        
        window.scrollTo({
          top: offsetTop,
          behavior: 'smooth'
        });
      }
    });
  });
  
  // Sticky header on scroll
  const header = document.querySelector('.header');
  let lastScrollTop = 0;
  
  window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > 100) {
      header.classList.add('header-scrolled');
    } else {
      header.classList.remove('header-scrolled');
    }
    
    if (scrollTop > lastScrollTop && scrollTop > 300) {
      // Scrolling down
      header.classList.add('header-hidden');
    } else {
      // Scrolling up
      header.classList.remove('header-hidden');
    }
    
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
  
  // Newsletter form submission
  const newsletterForm = document.querySelector('.newsletter-form');
  
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const emailInput = this.querySelector('input[type="email"]');
      const email = emailInput.value.trim();
      
      if (email) {
        // Validate email format
        if (!validateEmail(email)) {
          showFormMessage(newsletterForm, 'Please enter a valid email address', 'error');
          return;
        }
        
        // Here you would typically send this to your server
        // For now, just show a success message
        showFormMessage(newsletterForm, `Thank you for subscribing with ${email}!`, 'success');
        emailInput.value = '';
      }
    });
  }
  
  // Form validation utility functions
  function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
  
  function validateRequired(value) {
    return value.trim() !== '';
  }
  
  function validatePhone(phone) {
    const re = /^\+?[0-9\s\-\(\)]{8,20}$/;
    return re.test(String(phone).trim());
  }
  
  function validatePassword(password) {
    // At least 8 characters, at least one letter and one number
    const re = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
    return re.test(String(password));
  }
  
  function showFormMessage(formEl, message, type = 'success') {
    // Remove any existing message
    const existingMessage = formEl.querySelector('.form-message');
    if (existingMessage) {
      existingMessage.remove();
    }
    
    // Create new message
    const messageEl = document.createElement('div');
    messageEl.className = `form-message ${type === 'error' ? 'form-message-error' : 'form-message-success'}`;
    messageEl.textContent = message;
    
    // Add to form
    formEl.appendChild(messageEl);
    
    // Auto-remove after a delay for success messages
    if (type === 'success') {
      setTimeout(() => {
        messageEl.remove();
      }, 5000);
    }
  }
  
  // Auth form functionality (login/register)
  const authForm = document.querySelector('.auth-form');
  const authTabs = document.querySelectorAll('.auth-tab');
  
  if (authForm && authTabs.length) {
    // Toggle between login and register forms
    authTabs.forEach(tab => {
      tab.addEventListener('click', function() {
        // Update active tab
        authTabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // Update form action and fields visibility
        const isLogin = this.getAttribute('data-tab') === 'login';
        authForm.setAttribute('data-mode', isLogin ? 'login' : 'register');
        
        // Toggle visibility of register-only fields
        const registerFields = authForm.querySelectorAll('.register-only');
        registerFields.forEach(field => {
          field.style.display = isLogin ? 'none' : 'block';
        });
        
        // Update submit button text
        const submitBtn = authForm.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.textContent = isLogin ? 'Sign In' : 'Sign Up';
        }
      });
    });
    
    // Form submission with validation
    authForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const isLogin = this.getAttribute('data-mode') === 'login';
      const email = this.querySelector('input[name="email"]').value;
      const password = this.querySelector('input[name="password"]').value;
      
      // Validate required fields
      if (!validateRequired(email) || !validateRequired(password)) {
        showFormMessage(authForm, 'All fields are required', 'error');
        return;
      }
      
      // Validate email format
      if (!validateEmail(email)) {
        showFormMessage(authForm, 'Please enter a valid email address', 'error');
        return;
      }
      
      if (!isLogin) {
        // Additional validation for registration
        const name = this.querySelector('input[name="name"]').value;
        const passwordConfirm = this.querySelector('input[name="password_confirm"]').value;
        
        if (!validateRequired(name)) {
          showFormMessage(authForm, 'Please enter your name', 'error');
          return;
        }
        
        if (!validatePassword(password)) {
          showFormMessage(authForm, 'Password must be at least 8 characters with at least one letter and one number', 'error');
          return;
        }
        
        if (password !== passwordConfirm) {
          showFormMessage(authForm, 'Passwords do not match', 'error');
          return;
        }
      }
      
      // Form is valid, submit it (in a real app, this would be an AJAX request)
      showFormMessage(authForm, isLogin ? 'Login successful!' : 'Registration successful!', 'success');
      
      // Redirect to home page after a delay (in a real app)
      setTimeout(() => {
        window.location.href = '/index.html';
      }, 2000);
    });
  }
  
  // Booking form functionality
  const bookingForm = document.querySelector('.booking-form');
  
  if (bookingForm) {
    // Date picker initialization would go here if using a library
    // For now, we'll use built-in date inputs
    
    // Calculate total when dates or guests change
    const calculateTotal = () => {
      const checkin = new Date(bookingForm.querySelector('input[name="checkin"]').value);
      const checkout = new Date(bookingForm.querySelector('input[name="checkout"]').value);
      const guests = parseInt(bookingForm.querySelector('select[name="guests"]').value) || 1;
      const roomType = bookingForm.querySelector('input[name="room_type"]:checked').value;
      
      // Base rates per room type
      const rates = {
        'standard': 120,
        'deluxe': 180,
        'suite': 250
      };
      
      if (checkin && checkout && !isNaN(checkin) && !isNaN(checkout)) {
        // Calculate number of nights
        const nights = Math.max(1, Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24)));
        
        // Base rate based on room type
        const baseRate = rates[roomType] || rates.standard;
        
        // Additional cost for extra guests (beyond 2)
        const extraGuestFee = Math.max(0, guests - 2) * 25;
        
        // Calculate nightly rate
        const nightlyRate = baseRate + extraGuestFee;
        
        // Calculate subtotal
        const subtotal = nightlyRate * nights;
        
        // Calculate tax (12%)
        const tax = subtotal * 0.12;
        
        // Calculate total
        const total = subtotal + tax;
        
        // Update summary
        document.getElementById('summary-nights').textContent = nights;
        document.getElementById('summary-rate').textContent = `$${nightlyRate}`;
        document.getElementById('summary-subtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('summary-tax').textContent = `$${tax.toFixed(2)}`;
        document.getElementById('summary-total').textContent = `$${total.toFixed(2)}`;
        
        // Update hidden field
        bookingForm.querySelector('input[name="total_amount"]').value = total.toFixed(2);
      }
    };
    
    // Add event listeners to form fields
    const dateInputs = bookingForm.querySelectorAll('input[type="date"]');
    const guestsSelect = bookingForm.querySelector('select[name="guests"]');
    const roomOptions = bookingForm.querySelectorAll('input[name="room_type"]');
    
    dateInputs.forEach(input => {
      input.addEventListener('change', calculateTotal);
    });
    
    if (guestsSelect) {
      guestsSelect.addEventListener('change', calculateTotal);
    }
    
    roomOptions.forEach(option => {
      option.addEventListener('change', calculateTotal);
    });
    
    // Initialize with default values
    calculateTotal();
    
    // Form submission
    bookingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Gather form data
      const formData = new FormData(this);
      const bookingData = {};
      
      for (const [key, value] of formData.entries()) {
        bookingData[key] = value;
      }
      
      // Basic validation
      const checkin = bookingData.checkin;
      const checkout = bookingData.checkout;
      const name = bookingData.name;
      const email = bookingData.email;
      const phone = bookingData.phone;
      
      if (!checkin || !checkout || !name || !email || !phone) {
        showFormMessage(bookingForm, 'Please fill in all required fields', 'error');
        return;
      }
      
      if (!validateEmail(email)) {
        showFormMessage(bookingForm, 'Please enter a valid email address', 'error');
        return;
      }
      
      if (!validatePhone(phone)) {
        showFormMessage(bookingForm, 'Please enter a valid phone number', 'error');
        return;
      }
      
      // Form is valid, submit it (in a real app, this would be an AJAX request)
      showFormMessage(bookingForm, 'Your booking has been submitted successfully!', 'success');
      
      // Reset form after a delay
      setTimeout(() => {
        bookingForm.reset();
        calculateTotal();
      }, 3000);
    });
  }
  
  // Internationalization functionality
  function loadTranslations(lang) {
    // In a production app, we'd fetch these from a JSON file
    const translations = {
      'en': {
        'nav.home': 'Home',
        'nav.about': 'About',
        'nav.rooms': 'Rooms',
        'nav.tours': 'Tours',
        'nav.packages': 'Packages',
        'nav.nearby': 'Nearby',
        'nav.contact': 'Contact',
        'nav.signin': 'Sign In',
        'home.hero.title': 'Experience Luxury in Paradise',
        'home.hero.subtitle': 'Welcome to the premier beachfront resort in Nha Trang with world-class amenities and unparalleled service.',
        'home.hero.cta': 'Book Your Stay',
        'home.hero.discover': 'Discover More',
        'home.about.title': 'Luxury Resort Experience',
        'home.about.description': 'Nestled on the pristine coastline of Nha Trang, our award-winning resort offers an unforgettable blend of traditional Vietnamese hospitality and modern luxury. With breathtaking views of the ocean, exquisite dining options, and a wide range of activities, Vinpearl Resort is the perfect destination for your dream vacation.',
        'home.rooms.title': 'Luxurious Accommodations',
        'home.rooms.description': 'Choose from our selection of beautifully designed rooms and suites, each offering comfort, elegance, and stunning views.',
        'home.rooms.viewAll': 'View All Rooms',
        'home.tours.title': 'Exciting Tours & Activities',
        'home.tours.description': 'Discover the beauty of Nha Trang and surrounding areas with our carefully crafted tours and activities.',
        'home.tours.viewAll': 'View All Tours',
        'home.testimonials.title': 'What Our Guests Say',
        'rooms.details': 'View Details',
        'tours.details': 'View Details',
        'footer.newsletter': 'Subscribe to our newsletter for the latest updates and special offers.',
        'footer.subscribe': 'Subscribe',
        'footer.email': 'Your Email'
      },
      'vi': {
        'nav.home': 'Trang chủ',
        'nav.about': 'Giới thiệu',
        'nav.rooms': 'Phòng',
        'nav.tours': 'Tours',
        'nav.packages': 'Gói dịch vụ',
        'nav.nearby': 'Điểm gần đây',
        'nav.contact': 'Liên hệ',
        'nav.signin': 'Đăng nhập',
        'home.hero.title': 'Trải nghiệm Sang trọng tại Thiên đường',
        'home.hero.subtitle': 'Chào mừng đến với khu nghỉ dưỡng bên bờ biển hàng đầu tại Nha Trang với tiện nghi đẳng cấp thế giới và dịch vụ vô song.',
        'home.hero.cta': 'Đặt phòng ngay',
        'home.hero.discover': 'Khám phá thêm',
        'home.about.title': 'Trải nghiệm Khu nghỉ dưỡng Sang trọng',
        'home.about.description': 'Tọa lạc trên bờ biển nguyên sơ của Nha Trang, khu nghỉ dưỡng đạt giải thưởng của chúng tôi mang đến sự kết hợp khó quên giữa lòng hiếu khách truyền thống của Việt Nam và sự sang trọng hiện đại. Với tầm nhìn tuyệt đẹp ra đại dương, các lựa chọn ẩm thực tuyệt vời và nhiều hoạt động đa dạng, Vinpearl Resort là điểm đến hoàn hảo cho kỳ nghỉ trong mơ của bạn.',
        'home.rooms.title': 'Phòng Nghỉ Sang Trọng',
        'home.rooms.description': 'Lựa chọn từ bộ sưu tập phòng và suite được thiết kế đẹp mắt của chúng tôi, mỗi phòng đều mang đến sự thoải mái, sang trọng và tầm nhìn tuyệt đẹp.',
        'home.rooms.viewAll': 'Xem tất cả phòng',
        'home.tours.title': 'Tours & Hoạt động Thú vị',
        'home.tours.description': 'Khám phá vẻ đẹp của Nha Trang và các khu vực lân cận với các tour và hoạt động được thiết kế cẩn thận của chúng tôi.',
        'home.tours.viewAll': 'Xem tất cả tours',
        'home.testimonials.title': 'Khách hàng Nói gì về Chúng tôi',
        'rooms.details': 'Xem Chi tiết',
        'tours.details': 'Xem Chi tiết',
        'footer.newsletter': 'Đăng ký nhận bản tin của chúng tôi để cập nhật thông tin mới nhất và ưu đãi đặc biệt.',
        'footer.subscribe': 'Đăng ký',
        'footer.email': 'Email của bạn'
      }
    };
    
    // Find all elements with data-i18n attribute
    const elements = document.querySelectorAll('[data-i18n]');
    elements.forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (translations[lang] && translations[lang][key]) {
        // If the element has a placeholder attribute, update that
        if (el.hasAttribute('placeholder')) {
          el.setAttribute('placeholder', translations[lang][key]);
        } else {
          // Otherwise update the text content
          el.textContent = translations[lang][key];
        }
      }
    });
  }
  
  /**
   * Check if user is logged in via AJAX and update the header UI accordingly
   */
  function checkUserLoginStatus() {
    // Create a simple AJAX request to check login status
    const xhr = new XMLHttpRequest();
    // Update path to be relative to the current root
    xhr.open('GET', '/includes/check_login_status.php', true);
    
    xhr.onload = function() {
      if (this.status === 200) {
        try {
          const response = JSON.parse(this.responseText);
          console.log("Login status response:", response); // Add debugging to see the response
          
          // Find the sign-in button in the header - look for both data attribute and class
          const signInButton = document.querySelector('a[data-i18n="nav.signin"], .header-buttons a.btn.btn-white');
          
          if (response.isLoggedIn && signInButton) {
            // User is logged in, replace sign in button with user menu
            const userName = response.user.name || 'User';
            const headerButtons = signInButton.closest('.header-buttons');
            
            if (!headerButtons) {
              console.error('Could not find header-buttons container');
              return;
            }
            
            // Create user menu HTML
            const userMenuHTML = `
              <div class="user-menu-container">
                <button class="user-menu-button">
                  <span class="user-avatar">${userName.charAt(0)}</span>
                  <span class="user-name">${userName}</span>
                </button>
                <div class="user-dropdown">
                  <a href="/pages/profile/index.html">My Profile</a>
                  <a href="/pages/profile/bookings.html">My Bookings</a>
                  ${response.user.role === 'ADMIN' ? '<a href="/pages/admin/index.html">Admin Panel</a>' : ''}
                  <a href="/pages/auth/logout.php">Logout</a>
                </div>
              </div>
            `;
            
            // Replace sign in button with user menu
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = userMenuHTML;
            headerButtons.replaceChild(tempDiv.firstElementChild, signInButton);
            
            // Setup user dropdown functionality
            const userMenuButton = headerButtons.querySelector('.user-menu-button');
            const userDropdown = headerButtons.querySelector('.user-dropdown');
            
            if (userMenuButton && userDropdown) {
              userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
              });
              
              // Close dropdown when clicking elsewhere
              document.addEventListener('click', function() {
                userDropdown.classList.remove('active');
              });
              
              // Prevent closing when clicking inside dropdown
              userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
              });
            }
          } else {
            console.log("User not logged in or sign-in button not found");
          }
        } catch (e) {
          console.error('Error parsing JSON response:', e);
        }
      } else {
        console.error('Failed to load login status, status code:', this.status);
      }
    };
    
    xhr.onerror = function() {
      console.error('Request error while checking login status');
    };
    
    xhr.send();
  }

  // Page transition effects
  const pageLinks = document.querySelectorAll('a:not([href^="#"]):not([target="_blank"])');
  
  pageLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      // Skip if modifier keys are pressed
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      
      const href = this.getAttribute('href');
      
      // Skip for external links
      if (href.includes('://') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
      
      e.preventDefault();
      
      // Add page transition class
      document.body.classList.add('page-transition-out');
      
      // Navigate to the new page after transition
      setTimeout(() => {
        window.location.href = href;
      }, 300);
    });
  });
  
  // Add page transition in class when page is loaded
  document.body.classList.add('page-transition-in');
  
  // Remove transition class after animation completes
  setTimeout(() => {
    document.body.classList.remove('page-transition-in');
  }, 500);
});