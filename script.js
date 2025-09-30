// =====================================
// Global Variables
// =====================================
let currentSlide = 0;
let isAutoPlaying = true;
let autoPlayInterval;
let weatherData = null;

// =====================================
// DOM Ready Event
// =====================================
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeCarousel();
    initializeNavigation();
    initializeScrollProgress();
    initializeCounterAnimations();
    initializeWeatherWidget();
    initializeBackToTop();
    initializeSmoothScrolling();
    initializeAOS();
    
    // Add event listeners
    addEventListeners();
    
    console.log('ZekkoBlora website initialized successfully!');
});

// =====================================
// Carousel Functionality
// =====================================
function initializeCarousel() {
    const carousel = document.getElementById('heroCarousel');
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.querySelector('.carousel-btn.prev');
    const nextBtn = document.querySelector('.carousel-btn.next');
    
    if (!carousel || slides.length === 0) return;
    
    // Set up carousel
    updateCarousel();
    startAutoPlay();
    
    // Previous button
    prevBtn?.addEventListener('click', () => {
        pauseAutoPlay();
        previousSlide();
        resumeAutoPlay();
    });
    
    // Next button
    nextBtn?.addEventListener('click', () => {
        pauseAutoPlay();
        nextSlide();
        resumeAutoPlay();
    });
    
    // Indicator buttons
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            pauseAutoPlay();
            goToSlide(index);
            resumeAutoPlay();
        });
    });
    
    // Pause on hover
    carousel.addEventListener('mouseenter', pauseAutoPlay);
    carousel.addEventListener('mouseleave', resumeAutoPlay);
    
    // Touch/swipe support
    addTouchSupport(carousel);
}

function updateCarousel() {
    const carousel = document.getElementById('heroCarousel');
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    
    if (!carousel || slides.length === 0) return;
    
    // Update carousel position
    const translateX = -currentSlide * 25; // 25% per slide (100% / 4 slides)
    carousel.style.transform = `translateX(${translateX}%)`;
    
    // Update slide active states
    slides.forEach((slide, index) => {
        slide.classList.toggle('active', index === currentSlide);
    });
    
    // Update indicator active states
    indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === currentSlide);
    });
}

function nextSlide() {
    const slides = document.querySelectorAll('.carousel-slide');
    currentSlide = (currentSlide + 1) % slides.length;
    updateCarousel();
}

function previousSlide() {
    const slides = document.querySelectorAll('.carousel-slide');
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    updateCarousel();
}

function goToSlide(index) {
    const slides = document.querySelectorAll('.carousel-slide');
    if (index >= 0 && index < slides.length) {
        currentSlide = index;
        updateCarousel();
    }
}

function startAutoPlay() {
    if (autoPlayInterval) clearInterval(autoPlayInterval);
    autoPlayInterval = setInterval(nextSlide, 5000); // 5 seconds
    isAutoPlaying = true;
}

function pauseAutoPlay() {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
        isAutoPlaying = false;
    }
}

function resumeAutoPlay() {
    if (!isAutoPlaying) {
        setTimeout(startAutoPlay, 2000); // Resume after 2 seconds
    }
}

function addTouchSupport(carousel) {
    let startX = 0;
    let startY = 0;
    let isDragging = false;
    
    carousel.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = true;
        pauseAutoPlay();
    });
    
    carousel.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
    });
    
    carousel.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        
        const endX = e.changedTouches[0].clientX;
        const endY = e.changedTouches[0].clientY;
        const deltaX = startX - endX;
        const deltaY = startY - endY;
        
        // Check if it's a horizontal swipe
        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
            if (deltaX > 0) {
                nextSlide();
            } else {
                previousSlide();
            }
        }
        
        isDragging = false;
        resumeAutoPlay();
    });
}

// =====================================
// Navigation Functionality
// =====================================
function initializeNavigation() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    // Mobile menu toggle
    navToggle?.addEventListener('click', () => {
        navMenu?.classList.toggle('active');
        navToggle.classList.toggle('active');
        
        // Update ARIA attributes
        const isExpanded = navMenu?.classList.contains('active');
        navToggle.setAttribute('aria-expanded', isExpanded);
    });
    
    // Close mobile menu when clicking on links
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu?.classList.remove('active');
            navToggle?.classList.remove('active');
            navToggle?.setAttribute('aria-expanded', 'false');
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!navToggle?.contains(e.target) && !navMenu?.contains(e.target)) {
            navMenu?.classList.remove('active');
            navToggle?.classList.remove('active');
            navToggle?.setAttribute('aria-expanded', 'false');
        }
    });
    
    // Highlight active navigation item based on scroll position
    updateActiveNavItem();
}

function updateActiveNavItem() {
    const sections = document.querySelectorAll('.content-section');
    const navLinks = document.querySelectorAll('.nav-menu a, .quick-nav a');
    
    window.addEventListener('scroll', () => {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 150; // Account for fixed header
            const sectionHeight = section.offsetHeight;
            
            if (window.pageYOffset >= sectionTop && 
                window.pageYOffset < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });
}

// =====================================
// Scroll Progress Functionality
// =====================================
function initializeScrollProgress() {
    const progressBar = document.getElementById('progressBar');
    const progressCircle = document.getElementById('progressCircle');
    const progressText = document.querySelector('.progress-text');
    
    if (!progressBar) return;
    
    // Calculate scroll progress
    function updateScrollProgress() {
        const scrollTop = window.pageYOffset;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;
        
        // Update progress bar
        progressBar.style.width = scrollPercent + '%';
        
        // Update circular progress
        if (progressCircle && progressText) {
            const circumference = 2 * Math.PI * 35; // radius = 35
            const offset = circumference - (scrollPercent / 100) * circumference;
            
            progressCircle.style.strokeDashoffset = offset;
            progressText.textContent = Math.round(scrollPercent) + '%';
        }
    }
    
    // Throttle scroll events for better performance
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                updateScrollProgress();
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Initial update
    updateScrollProgress();
}

// =====================================
// Counter Animations
// =====================================
function initializeCounterAnimations() {
    const counters = document.querySelectorAll('.stat-number[data-target]');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    counters.forEach(counter => {
        observer.observe(counter);
    });
}

function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000; // 2 seconds
    const increment = target / (duration / 16); // 60fps
    let current = 0;
    
    const timer = setInterval(() => {
        current += increment;
        
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        // Format number with commas for large numbers
        const formatted = Math.floor(current).toLocaleString('id-ID');
        element.textContent = formatted;
    }, 16);
}

// =====================================
// Weather Widget
// =====================================
function initializeWeatherWidget() {
    const weatherWidget = document.getElementById('weatherWidget');
    if (!weatherWidget) return;
    
    // Blora coordinates (approximate)
    const lat = -6.9698;
    const lon = 111.4183;
    
    fetchWeatherData(lat, lon);
}

async function fetchWeatherData(lat, lon) {
    const weatherWidget = document.getElementById('weatherWidget');
    if (!weatherWidget) return;
    
    try {
        // Using Open-Meteo API (free, no API key required)
        const response = await fetch(
            `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&hourly=temperature_2m,relativehumidity_2m&timezone=Asia%2FJakarta`
        );
        
        if (!response.ok) throw new Error('Weather data fetch failed');
        
        const data = await response.json();
        weatherData = data;
        
        displayWeatherData(data);
    } catch (error) {
        console.error('Error fetching weather data:', error);
        displayWeatherError();
    }
}

function displayWeatherData(data) {
    const weatherWidget = document.getElementById('weatherWidget');
    if (!weatherWidget || !data.current_weather) return;
    
    const weather = data.current_weather;
    const temp = Math.round(weather.temperature);
    const windSpeed = Math.round(weather.windspeed);
    
    // Get weather description based on weather code
    const weatherDesc = getWeatherDescription(weather.weathercode);
    const weatherIcon = getWeatherIcon(weather.weathercode);
    
    weatherWidget.innerHTML = `
        <div class="weather-content">
            <div class="weather-main">
                <div class="weather-icon">
                    <i class="fas ${weatherIcon}"></i>
                </div>
                <div class="weather-temp">${temp}°C</div>
            </div>
            <div class="weather-details">
                <div class="weather-desc">${weatherDesc}</div>
                <div class="weather-wind">
                    <i class="fas fa-wind"></i>
                    ${windSpeed} km/h
                </div>
            </div>
            <div class="weather-time">
                Diperbarui: ${new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                })}
            </div>
        </div>
    `;
    
    // Add CSS for weather widget
    addWeatherWidgetStyles();
}

function displayWeatherError() {
    const weatherWidget = document.getElementById('weatherWidget');
    if (!weatherWidget) return;
    
    weatherWidget.innerHTML = `
        <div class="weather-error">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Data cuaca tidak tersedia</span>
        </div>
    `;
}

function getWeatherDescription(code) {
    const descriptions = {
        0: 'Cerah',
        1: 'Sebagian Berawan',
        2: 'Berawan Sebagian',
        3: 'Berawan',
        45: 'Berkabut',
        48: 'Kabut Beku',
        51: 'Gerimis Ringan',
        53: 'Gerimis Sedang',
        55: 'Gerimis Lebat',
        61: 'Hujan Ringan',
        63: 'Hujan Sedang',
        65: 'Hujan Lebat',
        71: 'Salju Ringan',
        73: 'Salju Sedang',
        75: 'Salju Lebat',
        80: 'Hujan Shower',
        81: 'Hujan Shower Sedang',
        82: 'Hujan Shower Lebat',
        95: 'Badai Petir',
        96: 'Badai dengan Hujan Es',
        99: 'Badai dengan Hujan Es Lebat'
    };
    
    return descriptions[code] || 'Tidak Diketahui';
}

function getWeatherIcon(code) {
    const icons = {
        0: 'fa-sun',
        1: 'fa-cloud-sun',
        2: 'fa-cloud-sun',
        3: 'fa-cloud',
        45: 'fa-smog',
        48: 'fa-smog',
        51: 'fa-cloud-drizzle',
        53: 'fa-cloud-rain',
        55: 'fa-cloud-rain',
        61: 'fa-cloud-rain',
        63: 'fa-cloud-rain',
        65: 'fa-cloud-showers-heavy',
        71: 'fa-snowflake',
        73: 'fa-snowflake',
        75: 'fa-snowflake',
        80: 'fa-cloud-showers-heavy',
        81: 'fa-cloud-showers-heavy',
        82: 'fa-cloud-showers-heavy',
        95: 'fa-bolt',
        96: 'fa-bolt',
        99: 'fa-bolt'
    };
    
    return icons[code] || 'fa-question';
}

function addWeatherWidgetStyles() {
    if (document.getElementById('weather-widget-styles')) return;
    
    const styles = `
        <style id="weather-widget-styles">
            .weather-content {
                background: var(--color-light-gray);
                border-radius: var(--radius-md);
                padding: var(--spacing-md);
            }
            
            .weather-main {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: var(--spacing-md);
                margin-bottom: var(--spacing-md);
            }
            
            .weather-icon i {
                font-size: var(--font-size-2xl);
                color: var(--color-warm-orange);
            }
            
            .weather-temp {
                font-size: var(--font-size-2xl);
                font-weight: 700;
                color: var(--color-dark-green);
            }
            
            .weather-details {
                text-align: center;
                margin-bottom: var(--spacing-sm);
            }
            
            .weather-desc {
                font-weight: 600;
                color: var(--color-text-dark);
                margin-bottom: var(--spacing-xs);
            }
            
            .weather-wind {
                font-size: var(--font-size-sm);
                color: var(--color-text-light);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: var(--spacing-xs);
            }
            
            .weather-time {
                font-size: var(--font-size-xs);
                color: var(--color-text-light);
                text-align: center;
                margin-top: var(--spacing-sm);
                padding-top: var(--spacing-sm);
                border-top: 1px solid var(--color-light-gray);
            }
            
            .weather-error {
                text-align: center;
                color: var(--color-text-light);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: var(--spacing-sm);
            }
        </style>
    `;
    
    document.head.insertAdjacentHTML('beforeend', styles);
}

// =====================================
// Back to Top Functionality
// =====================================
function initializeBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    if (!backToTopBtn) return;
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
    
    // Smooth scroll to top
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// =====================================
// Smooth Scrolling
// =====================================
function initializeSmoothScrolling() {
    const scrollLinks = document.querySelectorAll('a[href^="#"]');
    
    scrollLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            
            // Skip if it's just "#"
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (!target) return;
            
            e.preventDefault();
            
            // Calculate offset for fixed header
            const headerHeight = document.querySelector('.header')?.offsetHeight || 0;
            const targetPosition = target.offsetTop - headerHeight - 20;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        });
    });
}

// =====================================
// AOS (Animate On Scroll) Initialization
// =====================================
function initializeAOS() {
    // Check if AOS library is loaded
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false,
            offset: 120,
            delay: 0,
            anchorPlacement: 'top-bottom'
        });
        
        // Refresh AOS when carousel changes (for dynamic content)
        const carousel = document.getElementById('heroCarousel');
        if (carousel) {
            carousel.addEventListener('transitionend', () => {
                AOS.refresh();
            });
        }
    } else {
        console.warn('AOS library not loaded. Animation effects will not work.');
    }
}

// =====================================
// Additional Event Listeners
// =====================================
function addEventListeners() {
    // Keyboard navigation for carousel
    document.addEventListener('keydown', (e) => {
        if (e.target.closest('.carousel-container')) {
            switch (e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    pauseAutoPlay();
                    previousSlide();
                    resumeAutoPlay();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    pauseAutoPlay();
                    nextSlide();
                    resumeAutoPlay();
                    break;
                case ' ':
                case 'Enter':
                    if (e.target.classList.contains('indicator')) {
                        e.preventDefault();
                        const index = Array.from(document.querySelectorAll('.indicator')).indexOf(e.target);
                        pauseAutoPlay();
                        goToSlide(index);
                        resumeAutoPlay();
                    }
                    break;
            }
        }
    });
    
    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Refresh AOS on resize
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
            
            // Update carousel position
            updateCarousel();
        }, 250);
    });
    
    // Handle visibility change (for auto-play)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            pauseAutoPlay();
        } else if (isAutoPlaying) {
            startAutoPlay();
        }
    });
    
    // Lazy loading for images
    initializeLazyLoading();
}

// =====================================
// Lazy Loading for Images
// =====================================
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.add('fade-in');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// =====================================
// Utility Functions
// =====================================

// Throttle function for performance optimization
function throttle(func, limit) {
    let lastFunc;
    let lastRan;
    return function() {
        const context = this;
        const args = arguments;
        if (!lastRan) {
            func.apply(context, args);
            lastRan = Date.now();
        } else {
            clearTimeout(lastFunc);
            lastFunc = setTimeout(function() {
                if ((Date.now() - lastRan) >= limit) {
                    func.apply(context, args);
                    lastRan = Date.now();
                }
            }, limit - (Date.now() - lastRan));
        }
    }
}

// Debounce function for performance optimization
function debounce(func, wait, immediate) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Format number with Indonesian locale
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Check if element is in viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// =====================================
// Error Handling
// =====================================
window.addEventListener('error', (e) => {
    console.error('JavaScript Error:', e.error);
    // You could send this to an analytics service
});

window.addEventListener('unhandledrejection', (e) => {
    console.error('Unhandled Promise Rejection:', e.reason);
    // You could send this to an analytics service
});

// =====================================
// Performance Monitoring
// =====================================
window.addEventListener('load', () => {
    // Log performance metrics
    if ('performance' in window) {
        const perfData = performance.timing;
        const loadTime = perfData.loadEventEnd - perfData.navigationStart;
        console.log(`Page loaded in ${loadTime}ms`);
    }
});

// =====================================
// Export functions for testing (if needed)
// =====================================
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        nextSlide,
        previousSlide,
        goToSlide,
        updateCarousel,
        formatNumber,
        isInViewport,
        throttle,
        debounce
    };
}