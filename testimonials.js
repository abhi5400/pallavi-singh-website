// Testimonials Carousel
class TestimonialsCarousel {
    constructor() {
        this.container = document.getElementById('testimonialsContainer');
        this.prevBtn = document.getElementById('prevTestimonial');
        this.nextBtn = document.getElementById('nextTestimonial');
        this.dotsContainer = document.getElementById('testimonialDots');
        this.cards = document.querySelectorAll('.testimonial-card');
        
        if (!this.container || !this.prevBtn || !this.nextBtn || !this.dotsContainer || this.cards.length === 0) {
            this.isDisabled = true;
            return;
        }
        
        this.currentIndex = 0;
        this.cardsPerView = 3;
        this.totalCards = this.cards.length;
        this.totalSlides = Math.ceil(this.totalCards / this.cardsPerView);
        this.isTransitioning = false;
        
        this.init();
    }
    
    init() {
        this.createDots();
        this.updateCarousel();
        this.bindEvents();
        this.startAutoPlay();
    }
    
    createDots() {
        this.dotsContainer.innerHTML = '';
        for (let i = 0; i < this.totalSlides; i++) {
            const dot = document.createElement('div');
            dot.className = 'testimonial-dot';
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => this.goToSlide(i));
            this.dotsContainer.appendChild(dot);
        }
    }
    
    bindEvents() {
        this.prevBtn.addEventListener('click', () => this.prevSlide());
        this.nextBtn.addEventListener('click', () => this.nextSlide());
        
        // Touch/swipe support
        let startX = 0;
        let endX = 0;
        
        this.container.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });
        
        this.container.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            this.handleSwipe(startX, endX);
        });
        
        // Pause auto-play on hover
        this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.container.addEventListener('mouseleave', () => this.startAutoPlay());
    }
    
    handleSwipe(startX, endX) {
        const threshold = 50;
        const diff = startX - endX;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                this.nextSlide();
            } else {
                this.prevSlide();
            }
        }
    }
    
    goToSlide(index) {
        if (this.isTransitioning) return;
        this.isTransitioning = true;
        this.currentIndex = index;
        this.updateCarousel();
        setTimeout(() => {
            this.isTransitioning = false;
        }, 500);
    }
    
    nextSlide() {
        if (this.isTransitioning) return;
        this.isTransitioning = true;
        this.currentIndex = (this.currentIndex + 1) % this.totalSlides;
        this.updateCarousel();
        setTimeout(() => {
            this.isTransitioning = false;
        }, 500);
    }
    
    prevSlide() {
        if (this.isTransitioning) return;
        this.isTransitioning = true;
        this.currentIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
        this.updateCarousel();
        setTimeout(() => {
            this.isTransitioning = false;
        }, 500);
    }
    
    updateCarousel() {
        const translateX = -this.currentIndex * 100;
        this.container.style.transform = `translateX(${translateX}%)`;
        
        // Update dots
        document.querySelectorAll('.testimonial-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
        });
        
        // Remove disabled state for continuous loop
        this.prevBtn.disabled = false;
        this.nextBtn.disabled = false;
    }
    
    startAutoPlay() {
        this.autoPlayInterval = setInterval(() => {
            if (!this.isTransitioning) {
                this.nextSlide();
            }
        }, 5000); // Change slide every 5 seconds
    }
    
    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
        }
    }
    
    // Responsive handling
    handleResize() {
        const width = window.innerWidth;
        if (width <= 480) {
            this.cardsPerView = 1;
        } else if (width <= 768) {
            this.cardsPerView = 2;
        } else {
            this.cardsPerView = 3;
        }
        
        this.totalSlides = Math.ceil(this.totalCards / this.cardsPerView);
        this.currentIndex = Math.min(this.currentIndex, this.totalSlides - 1);
        
        this.createDots();
        this.updateCarousel();
    }
}

// Initialize testimonials carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize testimonials carousel
    const testimonialsCarousel = new TestimonialsCarousel();
    
    if (!testimonialsCarousel.isDisabled) {
        // Handle window resize
        window.addEventListener('resize', () => {
            testimonialsCarousel.handleResize();
        });
    }
});
