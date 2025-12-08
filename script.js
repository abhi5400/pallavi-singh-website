// Initialize Scrollify for smooth section transitions (guarded)
function initScrollEnhancements($) {
    let hasScrollify = false;
    try {
        hasScrollify = typeof $.scrollify === 'function';
        if (hasScrollify) {
            $.scrollify({
                section: ".page-section",
                easing: "jswing",
                scrollSpeed: 800,
                standardScrollElements: ".normal-scroll",
                offset: 0,
                overflowScroll: true,
                scrollbars: true,
                touchScroll: true,
                before: function() {
                    $(".page-section").removeClass("active");
                    $(".scroll-dot").removeClass("active");
                    const current = $.scrollify.current();
                    if (current && current.addClass) {
                        current.addClass("active");
                        const currentId = current.attr('id');
                        $(`.scroll-dot[data-section="${currentId}"]`).addClass("active");
                    }
                },
            });
        } else {
            console.warn('Scrollify not available; using native scrolling.');
        }
    } catch (err) {
        hasScrollify = false;
        console.warn('Failed to initialize Scrollify:', err);
    }
    
    // Override navigation link clicks to use Scrollify when available
    $('.nav-link').on('click', function(e) {
        if (!hasScrollify) return;
        const target = $(this).attr('href');
        // Let external links behave normally
        if (!target || /^https?:/i.test(target)) return;

        // Use Scrollify only for same-page anchors (including index.html# anchors)
        const isHashLink = target.startsWith('#');
        const isIndexHashLink = target.startsWith('index.html#');

        if (isHashLink || isIndexHashLink) {
            e.preventDefault();
            const destination = isIndexHashLink ? target.replace(/^index\.html/, '') : target;
            $.scrollify.move(destination);
        }
    });
    
    // Scroll indicator click events (only if Scrollify is active)
    if (hasScrollify) {
    $('.scroll-dot').on('click', function() {
        const targetSection = $(this).data('section');
            if (targetSection) {
        $.scrollify.move(`#${targetSection}`);
            }
        });
    }
}

if (typeof window.jQuery !== 'undefined') {
    window.jQuery(function($) {
        initScrollEnhancements($);
    });
} else {
    document.addEventListener('DOMContentLoaded', function() {
        console.warn('jQuery not available; skipping Scrollify enhancements.');
});
}

// Mobile Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
    hamburger.classList.remove('active');
    navMenu.classList.remove('active');
}));

// Dynamic Quotes System
class DynamicQuotes {
    constructor() {
        this.quotes = [
            {
                text: "You are not defined by your struggles; you are shaped by how you rise from them.",
                author: ""
            },
            {
                text: "Every story has the power to transform, every conversation can heal, and every moment is an opportunity to grow.",
                author: ""
            },
            {
                text: "The journey of self-discovery begins with a single step of courage and a willingness to embrace your authentic self.",
                author: ""
            },
            {
                text: "Habits are the bridge between your dreams and your reality. Build them with intention, and they will carry you forward.",
                author: ""
            },
            {
                text: "Our limits are not set by people around us but by our own limiting beliefs. Break away and keep discovering yourself.",
                author: "Pallavi Singh"
            },
            {
                text: "We live in our minds! Keep it clean and clutter free",
                author: "Pallavi Singh"
            },
            {
                text: "Live, love, laugh! Help, support, give!\n\nAll the spices that make an awesome dish called Life.",
                author: "Pallavi Singh"
            },
            {
                text: "Where is the butterfly garden?\n\nWhispered the girl to the tree…\n\nJust around the corner, answered hope",
                author: "Pallavi Singh"
            }
        ];
        
        this.currentIndex = 0;
        this.autoRotateInterval = null;
        this.autoRotateDelay = 5000; // 5 seconds
        
        this.init();
    }
    
    init() {
        this.quoteText = document.getElementById('quoteText');
        this.quoteAuthor = document.getElementById('quoteAuthor');
        this.quoteCard = document.getElementById('quoteCard');
        this.quoteProgress = document.getElementById('quoteProgress');
        this.prevBtn = document.getElementById('prevQuote');
        this.nextBtn = document.getElementById('nextQuote');
        this.dotsContainer = document.getElementById('quoteDots');
        
        if (!this.quoteText) return; // Exit if quotes section doesn't exist
        
        this.createDots();
        this.showQuote(this.currentIndex);
        this.bindEvents();
        this.startAutoRotate();
        this.startProgressBar();
    }
    
    createDots() {
        this.dotsContainer.innerHTML = '';
        this.quotes.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.className = 'quote-dot';
            if (index === this.currentIndex) {
                dot.classList.add('active');
            }
            dot.addEventListener('click', () => this.goToQuote(index));
            this.dotsContainer.appendChild(dot);
        });
    }
    
    showQuote(index) {
        const quote = this.quotes[index];
        
        // Add card flip animation
        this.quoteCard.style.transform = 'rotateY(90deg)';
        
        setTimeout(() => {
            // Remove active class from current elements
            this.quoteText.classList.remove('active');
            this.quoteAuthor.classList.remove('active');
            
            // Update content
            this.quoteText.innerHTML = quote.text.replace(/\n\n/g, '<br><br>').replace(/\n/g, '<br>');
            this.quoteAuthor.textContent = quote.author ? `- ${quote.author}` : '';
            
            // Update dots
            document.querySelectorAll('.quote-dot').forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
            
            // Flip card back
            this.quoteCard.style.transform = 'rotateY(0deg)';
            
            // Add active class back with delay
            setTimeout(() => {
                this.quoteText.classList.add('active');
                this.quoteAuthor.classList.add('active');
            }, 200);
        }, 300);
        
        // Reset progress bar
        this.resetProgressBar();
    }
    
    nextQuote() {
        this.currentIndex = (this.currentIndex + 1) % this.quotes.length;
        this.showQuote(this.currentIndex);
        this.resetAutoRotate();
    }
    
    prevQuote() {
        this.currentIndex = (this.currentIndex - 1 + this.quotes.length) % this.quotes.length;
        this.showQuote(this.currentIndex);
        this.resetAutoRotate();
    }
    
    goToQuote(index) {
        this.currentIndex = index;
        this.showQuote(this.currentIndex);
        this.resetAutoRotate();
    }
    
    startAutoRotate() {
        this.autoRotateInterval = setInterval(() => {
            this.nextQuote();
        }, this.autoRotateDelay);
    }
    
    stopAutoRotate() {
        if (this.autoRotateInterval) {
            clearInterval(this.autoRotateInterval);
            this.autoRotateInterval = null;
        }
    }
    
    resetAutoRotate() {
        this.stopAutoRotate();
        this.startAutoRotate();
    }
    
    startProgressBar() {
        this.progressInterval = setInterval(() => {
            const progress = this.quoteProgress;
            progress.style.width = '100%';
        }, 100);
    }
    
    resetProgressBar() {
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
        }
        this.quoteProgress.style.width = '0%';
        this.startProgressBar();
    }
    
    bindEvents() {
        this.nextBtn.addEventListener('click', () => this.nextQuote());
        this.prevBtn.addEventListener('click', () => this.prevQuote());
        
        // Card click to advance
        this.quoteCard.addEventListener('click', () => this.nextQuote());
        
        // Pause auto-rotate on hover
        const quotesSection = document.querySelector('.quotes-section');
        if (quotesSection) {
            quotesSection.addEventListener('mouseenter', () => {
                this.stopAutoRotate();
                if (this.progressInterval) {
                    clearInterval(this.progressInterval);
                }
            });
            quotesSection.addEventListener('mouseleave', () => {
                this.startAutoRotate();
                this.resetProgressBar();
            });
        }
        
        // Keyboard navigation - only when quotes section is focused
        document.addEventListener('keydown', (e) => {
            // Only handle keyboard navigation if not typing in an input field
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
                return;
            }
            
            if (e.key === 'ArrowLeft') {
                this.prevQuote();
            } else if (e.key === 'ArrowRight') {
                this.nextQuote();
            } else if (e.key === ' ') {
                e.preventDefault();
                this.nextQuote();
            }
        });
        
        // Touch/swipe support
        let startX = 0;
        let startY = 0;
        
        this.quoteCard.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });
        
        this.quoteCard.addEventListener('touchend', (e) => {
            if (!startX || !startY) return;
            
            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;
            
            const diffX = startX - endX;
            const diffY = startY - endY;
            
            if (Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX > 50) {
                    this.nextQuote();
                } else if (diffX < -50) {
                    this.prevQuote();
                }
            }
            
            startX = 0;
            startY = 0;
        });
    }
}

// Initialize Dynamic Quotes when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new DynamicQuotes();
    // Hero intro reveal
    const hero = document.querySelector('.hero-content');
    if (hero) requestAnimationFrame(() => hero.classList.add('show'));
});

// Service Card Flip Functionality
function flipCard(card) {
    card.classList.toggle('flipped');
    if (navigator.vibrate) {
        navigator.vibrate(50);
    }
}

// Service Card Animation Observer
function observeServiceCards() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = `${Math.random() * 0.5}s`;
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.service-card').forEach(card => {
        observer.observe(card);
    });
}

// Initialize service card animations when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    observeServiceCards();
    // Generic section/card intersection reveals
    if ('IntersectionObserver' in window) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    sectionObserver.unobserve(e.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });

        document.querySelectorAll('.page-section').forEach(s => sectionObserver.observe(s));
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    cardObserver.unobserve(e.target);
                }
            });
        }, { threshold: 0.2, rootMargin: '0px 0px -10% 0px' });

        document.querySelectorAll('.blog-card, .service-card, .event-card').forEach(c => cardObserver.observe(c));
    }
});

// FAQ Accordion Functionality
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all other FAQ items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            
            // Toggle current item
            if (isActive) {
                item.classList.remove('active');
            } else {
                item.classList.add('active');
            }
        });
    });
});

// Dropdown menu functionality
document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', (e) => {
        e.preventDefault();
        const dropdown = toggle.closest('.nav-dropdown');
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        
        // Close other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== dropdownMenu) {
                menu.style.display = 'none';
            }
        });
        
        // Toggle current dropdown
        if (dropdownMenu.style.display === 'block') {
            dropdownMenu.style.display = 'none';
        } else {
            dropdownMenu.style.display = 'block';
        }
    });
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.nav-dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

// Enhanced smooth scrolling for internal elements
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        const target = document.querySelector(href);

        // Do not interfere with Join Now modal trigger
        if (href === '#join-form') {
            return;
        }

        // If it's a page section, route via Scrollify for consistent behavior
        if (target && target.classList.contains('page-section')) {
            e.preventDefault();
            if (typeof $.scrollify === 'function') {
                $.scrollify.move(href);
            } else if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            return;
        }

        // For other internal anchors, use smooth scroll
        e.preventDefault();
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Ensure Join Now always opens the modal, even if other handlers interfere
document.addEventListener('click', function(e) {
    const trigger = e.target.closest('a[href="#join-form"], .join-now-btn');
    if (!trigger) return;
    e.preventDefault();
    const modal = document.getElementById('join-form-modal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        // focus first input if available
        const form = document.getElementById('joinForm');
        if (form) {
            const firstInput = form.querySelector('input');
            if (firstInput) setTimeout(() => firstInput.focus(), 300);
        }
    }
});

// Explicit hero button bindings to guarantee behavior
document.addEventListener('DOMContentLoaded', function() {
    const readMoreBtn = document.getElementById('heroReadMore');
    if (readMoreBtn) {
        readMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = '#about';
            const target = document.querySelector(href);
            if (typeof $.scrollify === 'function') {
                $.scrollify.move(href);
            } else if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    const joinNowBtn = document.getElementById('heroJoinNow');
    if (joinNowBtn) {
        joinNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const modal = document.getElementById('join-form-modal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                const form = document.getElementById('joinForm');
                if (form) {
                    const firstInput = form.querySelector('input');
                    if (firstInput) setTimeout(() => firstInput.focus(), 300);
                }
            }
        });
    }
});

// Contact form handling
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Basic validation
    if (!data.name || !data.email || !data.message) {
        alert('Please fill in all required fields.');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email)) {
        alert('Please enter a valid email address.');
        return;
    }
    
    // Simulate form submission (replace with actual form handling)
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    submitButton.textContent = 'Sending...';
    submitButton.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        alert('Thank you for your message! I will get back to you soon.');
        this.reset();
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    }, 2000);
});

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 100) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 4px 20px rgba(26, 83, 92, 0.1)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        navbar.style.boxShadow = 'none';
    }
});

// Service card hover effects
document.querySelectorAll('.service-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Blog card hover effects
document.querySelectorAll('.blog-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Event card hover effects
document.querySelectorAll('.event-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Social media links
document.querySelectorAll('.social-link').forEach(link => {
    link.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-3px)';
    });
    
    link.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Button hover effects
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('mouseenter', function() {
        if (this.classList.contains('btn-primary')) {
            this.style.transform = 'translateY(-2px)';
        }
    });
    
    button.addEventListener('mouseleave', function() {
        if (this.classList.contains('btn-primary')) {
            this.style.transform = 'translateY(0)';
        }
    });
});

// Form input focus effects
document.querySelectorAll('input, textarea, select').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
    });
});

// Lazy loading for images (if needed)
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Initialize tooltips (if needed)
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
}

// Initialize tooltips when DOM is loaded
document.addEventListener('DOMContentLoaded', initTooltips);

// ========================================
// BOOK NOW POPUP FORM FUNCTIONALITY
// ========================================

// Book Now popup form handling
document.addEventListener('DOMContentLoaded', function() {
    const bookNowPopup = document.getElementById('bookNowPopup');
    const bookNowBtn = document.getElementById('bookNowBtn');
    const closeBookNowPopup = document.getElementById('closeBookNowPopup');
    const cancelBookNow = document.getElementById('cancelBookNow');
    const bookNowForm = document.getElementById('bookNowForm');

    // Show popup when "Book Now" button is clicked
    if (bookNowBtn) {
        bookNowBtn.addEventListener('click', function() {
            showBookNowPopup();
        });
    }

    // Close popup when close button is clicked
    if (closeBookNowPopup) {
        closeBookNowPopup.addEventListener('click', function() {
            hideBookNowPopup();
        });
    }

    // Close popup when cancel button is clicked
    if (cancelBookNow) {
        cancelBookNow.addEventListener('click', function() {
            hideBookNowPopup();
        });
    }

    // Close popup when clicking outside the form
    if (bookNowPopup) {
        bookNowPopup.addEventListener('click', function(e) {
            if (e.target === bookNowPopup) {
                hideBookNowPopup();
            }
        });
    }

    // Close popup with Escape key
    document.addEventListener('keydown', function(e) {
        if (!bookNowPopup) return;
        if (e.key === 'Escape' && bookNowPopup.classList.contains('active')) {
            hideBookNowPopup();
        }
    });

    // Form submission handling
    if (bookNowForm) {
        bookNowForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleBookNowFormSubmission(this);
        });
    }

    // Set minimum date for date picker
    const dateInput = document.getElementById('book-preferred-date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    }

    // Real-time validation for Book Now form
    setupBookNowFormValidation();
});

function showBookNowPopup() {
    const popup = document.getElementById('bookNowPopup');
    if (popup) {
        popup.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
}

function hideBookNowPopup() {
    const popup = document.getElementById('bookNowPopup');
    if (popup) {
        popup.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }
}

function setupBookNowFormValidation() {
    const form = document.getElementById('bookNowForm');
    if (!form) return;

    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        // Validate on blur
        input.addEventListener('blur', function() {
            validateBookNowField(this);
        });
        
        // Clear errors on focus
        input.addEventListener('focus', function() {
            const fieldGroup = this.closest('.form-group');
            if (fieldGroup) {
                fieldGroup.classList.remove('error');
            }
        });
        
        // Real-time validation for email
        if (input.type === 'email') {
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    validateBookNowField(this);
                }
            });
        }
    });
}

function validateBookNowField(field) {
    const fieldGroup = field.closest('.form-group');
    const value = field.value.trim();
    
    if (field.hasAttribute('required') && !value) {
        fieldGroup.classList.add('error');
        return false;
    }
    
    if (value) {
        if (field.type === 'email' && !isValidEmail(value)) {
            fieldGroup.classList.add('error');
            return false;
        }
        
        
        if (field.tagName === 'TEXTAREA' && field.name === 'goals' && value.length < 20) {
            fieldGroup.classList.add('error');
            return false;
        }
    }
    
    fieldGroup.classList.remove('error');
    return true;
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function handleBookNowFormSubmission(form) {
    // Clear previous error states
    form.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('error');
    });

    // Get form data
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validate form
    let isValid = true;
    const errors = [];

    // Required field validation
    const requiredFields = ['first-name', 'last-name', 'email', 'service', 'session-type', 'goals'];
    requiredFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        const fieldGroup = field.closest('.form-group');
        
        if (!data[fieldName] || data[fieldName].trim() === '') {
            fieldGroup.classList.add('error');
            const fieldLabel = fieldName.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
            errors.push(`${fieldLabel} is required`);
            isValid = false;
        }
    });

    // Email validation
    if (data.email && !isValidEmail(data.email)) {
        const emailGroup = form.querySelector('[name="email"]').closest('.form-group');
        emailGroup.classList.add('error');
        errors.push('Please enter a valid email address');
        isValid = false;
    }


    // Goals validation
    if (data.goals && data.goals.length < 20) {
        const goalsGroup = form.querySelector('[name="goals"]').closest('.form-group');
        goalsGroup.classList.add('error');
        errors.push('Goals description must be at least 20 characters long');
        isValid = false;
    }

    // Terms validation
    if (!data.terms) {
        const termsGroup = form.querySelector('[name="terms"]').closest('.form-group');
        termsGroup.classList.add('error');
        errors.push('Please accept the terms and conditions');
        isValid = false;
    }

    if (!isValid) {
        // Show first error message
        alert(errors[0]);
        return;
    }

    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Booking Your Session...';
    submitButton.disabled = true;

    // Simulate form submission
    setTimeout(() => {
        // Log form data (for development)
        console.log('Book Now form submitted:', data);
        
        // Show success message
        showBookNowSuccess();
        
        // Reset form
        form.reset();
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    }, 2000);
}

function showBookNowSuccess() {
    const popup = document.getElementById('bookNowPopup');
    const form = document.getElementById('bookNowForm');
    
    if (popup && form) {
        // Replace form content with success message
        form.innerHTML = `
            <div class="popup-success">
                <i class="fas fa-calendar-check"></i>
                <h3>Session Booking Confirmed!</h3>
                <p>Thank you for booking your session with Pallavi Singh. We've received your booking request and will contact you within 24 hours to confirm your session details and schedule.</p>
                <p><strong>What happens next?</strong></p>
                <ul style="text-align: left; margin: 20px 0; color: #666;">
                    <li>We'll review your session preferences</li>
                    <li>Contact you to confirm the date and time</li>
                    <li>Send you a calendar invitation</li>
                    <li>Provide any pre-session materials if needed</li>
                </ul>
                <button class="btn btn-primary" onclick="hideBookNowPopup()">Close</button>
            </div>
        `;
        
        // Auto-close after 8 seconds
        setTimeout(() => {
            hideBookNowPopup();
            // Reset form for next time
            location.reload();
        }, 8000);
    }
}

// ========================================
// JOURNEY POPUP FORM FUNCTIONALITY
// ========================================

// Popup form handling
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('journeyPopup');
    const startJourneyBtn = document.getElementById('startJourneyBtn');
    const closePopup = document.getElementById('closePopup');
    const cancelJourney = document.getElementById('cancelJourney');
    const journeyForm = document.getElementById('journeyForm');

    // Show popup when "Start Your Journey" button is clicked
    if (startJourneyBtn) {
        startJourneyBtn.addEventListener('click', function() {
            showPopup();
        });
    }

    // Close popup when close button is clicked
    if (closePopup) {
        closePopup.addEventListener('click', function() {
            hidePopup();
        });
    }

    // Close popup when cancel button is clicked
    if (cancelJourney) {
        cancelJourney.addEventListener('click', function() {
            hidePopup();
        });
    }

    // Close popup when clicking outside the form
    if (popup) {
        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                hidePopup();
            }
        });
    }

    // Close popup with Escape key
    document.addEventListener('keydown', function(e) {
        if (!popup) return;
        if (e.key === 'Escape' && popup.classList.contains('active')) {
            hidePopup();
        }
    });

    // Form submission handling
    if (journeyForm) {
        journeyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleJourneyFormSubmission(this);
        });
    }

    // Real-time validation for popup form
    setupPopupFormValidation();
});

function showPopup() {
    const popup = document.getElementById('journeyPopup');
    if (popup) {
        popup.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
}

function hidePopup() {
    const popup = document.getElementById('journeyPopup');
    if (popup) {
        popup.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }
}

function setupPopupFormValidation() {
    const form = document.getElementById('journeyForm');
    if (!form) return;

    const inputs = form.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        // Validate on blur
        input.addEventListener('blur', function() {
            validatePopupField(this);
        });
        
        // Clear errors on focus
        input.addEventListener('focus', function() {
            const fieldGroup = this.closest('.form-group');
            if (fieldGroup) {
                fieldGroup.classList.remove('error');
            }
        });
        
        // Real-time validation for email
        if (input.type === 'email') {
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    validatePopupField(this);
                }
            });
        }
    });
}

function validatePopupField(field) {
    const fieldGroup = field.closest('.form-group');
    const value = field.value.trim();
    
    if (field.hasAttribute('required') && !value) {
        fieldGroup.classList.add('error');
        return false;
    }
    
    if (value) {
        
        if (field.type === 'number') {
            const age = parseInt(value);
            if (age < 16 || age > 100) {
                fieldGroup.classList.add('error');
                return false;
            }
        }
    }
    
    fieldGroup.classList.remove('error');
    return true;
}


function handleJourneyFormSubmission(form) {
    // Clear previous error states
    form.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('error');
    });

    // Get form data
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Validate form
    let isValid = true;
    const errors = [];

    // Required field validation
    const requiredFields = ['name', 'age', 'city'];
    requiredFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        const fieldGroup = field.closest('.form-group');
        
        if (!data[fieldName] || data[fieldName].trim() === '') {
            fieldGroup.classList.add('error');
            errors.push(`${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required`);
            isValid = false;
        }
    });

    // Age validation
    if (data.age) {
        const age = parseInt(data.age);
        if (age < 16 || age > 100) {
            const ageGroup = form.querySelector('[name="age"]').closest('.form-group');
            ageGroup.classList.add('error');
            errors.push('Age must be between 16 and 100');
            isValid = false;
        }
    }


    // Terms validation
    if (!data.terms) {
        const termsGroup = form.querySelector('[name="terms"]').closest('.form-group');
        termsGroup.classList.add('error');
        errors.push('Please accept the terms and conditions');
        isValid = false;
    }

    if (!isValid) {
        // Show first error message
        alert(errors[0]);
        return;
    }

    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Starting Your Journey...';
    submitButton.disabled = true;

    // Simulate form submission
    setTimeout(() => {
        // Log form data (for development)
        console.log('Journey form submitted:', data);
        
        // Show success message
        showJourneySuccess();
        
        // Reset form
        form.reset();
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    }, 2000);
}

function showJourneySuccess() {
    const popup = document.getElementById('journeyPopup');
    const form = document.getElementById('journeyForm');
    
    if (popup && form) {
        // Replace form content with success message
        form.innerHTML = `
            <div class="popup-success">
                <i class="fas fa-check-circle"></i>
                <h3>Welcome to Your Journey!</h3>
                <p>Thank you for taking the first step towards transformation. We've received your information and will contact you within 24 hours to discuss your personalized coaching journey.</p>
                <button class="btn btn-primary" onclick="hidePopup()">Close</button>
            </div>
        `;
        
        // Auto-close after 5 seconds
        setTimeout(() => {
            hidePopup();
            // Reset form for next time
            location.reload();
        }, 5000);
    }
}

// Handle window resize
window.addEventListener('resize', function() {
    // Close mobile menu on resize
    if (window.innerWidth > 768) {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
    }
    
    // Close dropdowns on resize
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
    });
});

document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function() {
        if (this.type === 'submit') return; // Don't add loading to form submit buttons
        
        const originalText = this.textContent;
        this.textContent = 'Loading...';
        this.disabled = true;
        
        setTimeout(() => {
            this.textContent = originalText;
            this.disabled = false;
        }, 2000);
    });
});

// Smooth reveal for sections (without animations as per user preference)
function revealSections() {
    const sections = document.querySelectorAll('.page-section');
    
    sections.forEach(section => {
        const sectionTop = section.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (sectionTop < windowHeight * 0.75) {
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }
    });
}

// Call reveal function on scroll
window.addEventListener('scroll', revealSections);

// Initialize reveal on page load
document.addEventListener('DOMContentLoaded', revealSections);

// Handle external links
document.querySelectorAll('a[href^="http"]').forEach(link => {
    link.setAttribute('target', '_blank');
    link.setAttribute('rel', 'noopener noreferrer');
});

// Add keyboard navigation support
document.addEventListener('keydown', function(e) {
    // Escape key to close mobile menu
    if (e.key === 'Escape') {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
        
        // Close dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
    
    // Arrow keys for scroll indicator navigation - only when not typing in forms
    if ((e.key === 'ArrowUp' || e.key === 'ArrowDown') && 
        e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA' && !e.target.isContentEditable) {
        e.preventDefault();
        const currentDot = document.querySelector('.scroll-dot.active');
        const dots = document.querySelectorAll('.scroll-dot');
        const currentIndex = Array.from(dots).indexOf(currentDot);
        
        let nextIndex;
        if (e.key === 'ArrowUp') {
            nextIndex = currentIndex > 0 ? currentIndex - 1 : dots.length - 1;
        } else {
            nextIndex = currentIndex < dots.length - 1 ? currentIndex + 1 : 0;
        }
        
        const nextSection = dots[nextIndex].getAttribute('data-section');
        $.scrollify.move(`#${nextSection}`);
    }
});

// Add touch support for mobile
let touchStartY = 0;
let touchEndY = 0;

document.addEventListener('touchstart', function(e) {
    touchStartY = e.changedTouches[0].screenY;
});

document.addEventListener('touchend', function(e) {
    touchEndY = e.changedTouches[0].screenY;
    handleSwipe();
});

function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartY - touchEndY;
    
    if (Math.abs(diff) > swipeThreshold) {
        const currentDot = document.querySelector('.scroll-dot.active');
        const dots = document.querySelectorAll('.scroll-dot');
        const currentIndex = Array.from(dots).indexOf(currentDot);
        
        let nextIndex;
        if (diff > 0) {
            // Swipe up - next section
            nextIndex = currentIndex < dots.length - 1 ? currentIndex + 1 : 0;
        } else {
            // Swipe down - previous section
            nextIndex = currentIndex > 0 ? currentIndex - 1 : dots.length - 1;
        }
        
        const nextSection = dots[nextIndex].getAttribute('data-section');
        $.scrollify.move(`#${nextSection}`);
    }
}
