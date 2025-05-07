document.addEventListener('DOMContentLoaded', function() {
  // Initialize translations object
  window.allTranslations = window.allTranslations || {
    'en': {
      'nav.home': 'Home',
      'nav.about': 'About',
      'nav.rooms': 'Rooms',
      'nav.tours': 'Tours',
      'nav.packages': 'Packages',
      'nav.nearby': 'Nearby',
      'nav.signin': 'Sign In',
      'footer.newsletter': 'Subscribe to our newsletter for the latest updates and special offers.',
      'footer.email': 'Your Email',
      'footer.subscribe': 'Subscribe'
    },
    'vi': {
      'nav.home': 'Trang chủ',
      'nav.about': 'Giới thiệu',
      'nav.rooms': 'Phòng',
      'nav.tours': 'Tour',
      'nav.packages': 'Gói dịch vụ',
      'nav.nearby': 'Khu vực lân cận',
      'nav.signin': 'Đăng nhập',
      'footer.newsletter': 'Đăng ký nhận thông tin mới nhất và ưu đãi đặc biệt.',
      'footer.email': 'Email của bạn',
      'footer.subscribe': 'Đăng ký'
    }
  };

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
        
        // Update active class
        languageOptions.forEach(opt => opt.classList.remove('active'));
        this.classList.add('active');
        
        // Update toggle text
        languageToggle.textContent = lang.toUpperCase();
        
        // Save selection to localStorage
        localStorage.setItem('language', lang);
        
        // Update page content with translations
        loadTranslations(lang);
        
        // Close dropdown
        languageDropdown.classList.remove('active');
      });
    });
    
    // Load saved language
    const savedLanguage = localStorage.getItem('language') || 'en';
    languageOptions.forEach(option => {
      if (option.getAttribute('data-lang') === savedLanguage) {
        option.classList.add('active');
        languageToggle.textContent = savedLanguage.toUpperCase();
      } else {
        option.classList.remove('active');
      }
    });
    
    // Load translations
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
      // Allow the form to submit normally to the backend
      // The backend will handle validation and authentication
      
      // We'll just do some basic front-end validation first
      const isLogin = this.getAttribute('data-mode') === 'login';
      const email = this.querySelector('input[name="email"]').value;
      const password = this.querySelector('input[name="password"]').value;
      
      // Validate required fields
      if (!validateRequired(email) || !validateRequired(password)) {
        e.preventDefault();
        showFormMessage(authForm, 'All fields are required', 'error');
        return;
      }
      
      // Validate email format
      if (!validateEmail(email)) {
        e.preventDefault();
        showFormMessage(authForm, 'Please enter a valid email address', 'error');
        return;
      }
      
      if (!isLogin) {
        // Additional validation for registration
        const name = this.querySelector('input[name="name"]').value;
        const passwordConfirm = this.querySelector('input[name="password_confirm"]').value;
        
        if (!validateRequired(name)) {
          e.preventDefault();
          showFormMessage(authForm, 'Please enter your name', 'error');
          return;
        }
        
        if (!validatePassword(password)) {
          e.preventDefault();
          showFormMessage(authForm, 'Password must be at least 8 characters with at least one letter and one number', 'error');
          return;
        }
        
        if (password !== passwordConfirm) {
          e.preventDefault();
          showFormMessage(authForm, 'Passwords do not match', 'error');
          return;
        }
      }
      
      // If we get here, let the form submit to the backend
    });
  }
  
  // Booking form functionality
  const bookingForm = document.querySelector('.booking-form');
  
  if (bookingForm) {
    // Date picker initialization would go here if using a library
    // For now, we'll use built-in date inputs
    
    // Calculate total when dates or guests change
    const calculateTotal = () => {
      const checkIn = new Date(bookingForm.querySelector('input[name="check_in"]').value);
      const checkOut = new Date(bookingForm.querySelector('input[name="check_out"]').value);
      const adults = parseInt(bookingForm.querySelector('select[name="adults"]').value) || 1;
      const children = parseInt(bookingForm.querySelector('select[name="children"]').value) || 0;
      
      // Calculate number of nights
      const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
      
      // Example rates
      const baseRate = 150; // Per night
      const adultRate = 25; // Per adult
      const childRate = 15; // Per child
      
      // Calculate total
      let total = 0;
      
      if (!isNaN(nights) && nights > 0) {
        total = baseRate * nights;
        total += (adults > 1) ? (adults - 1) * adultRate * nights : 0;
        total += children * childRate * nights;
        
        // Update the total display
        const totalElement = bookingForm.querySelector('.booking-total');
        if (totalElement) {
          totalElement.textContent = `$${total}`;
        }
      }
    };
    
    // Add event listeners
    const inputs = bookingForm.querySelectorAll('input[name="check_in"], input[name="check_out"], select[name="adults"], select[name="children"]');
    inputs.forEach(input => {
      input.addEventListener('change', calculateTotal);
    });
    
    // Initial calculation
    calculateTotal();
    
    // Form submission
    bookingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Validate fields
      const name = bookingForm.querySelector('input[name="name"]').value.trim();
      const email = bookingForm.querySelector('input[name="email"]').value.trim();
      const phone = bookingForm.querySelector('input[name="phone"]').value.trim();
      const checkIn = bookingForm.querySelector('input[name="check_in"]').value;
      const checkOut = bookingForm.querySelector('input[name="check_out"]').value;
      
      if (!name || !email || !phone || !checkIn || !checkOut) {
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
    if (!window.allTranslations || !window.allTranslations[lang]) {
      console.warn('No translations available for', lang);
      return;
    }
    
    const translations = window.allTranslations[lang];
    
    // Update all elements with data-i18n attribute
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      
      if (translations[key]) {
        el.textContent = translations[key];
      }
      
      // Handle attributes like placeholders
      const attrKey = el.getAttribute('data-i18n-attr');
      if (attrKey && translations[key]) {
        el.setAttribute(attrKey, translations[key]);
      }
    });
  }
  
  /**
   * Check if user is logged in via AJAX and update the header UI accordingly
   */
  function checkUserLoginStatus() {
    // Create a simple AJAX request to check login status
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '/includes/check_login_status.php', true);
    
    xhr.onload = function() {
      if (this.status === 200) {
        try {
          const response = JSON.parse(this.responseText);
          
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
                  <a href="/pages/profile/index.php">My Profile</a>
                  <a href="/pages/profile/bookings.php">My Bookings</a>
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