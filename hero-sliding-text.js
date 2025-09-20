// Hero Sliding Text Animation
document.addEventListener('DOMContentLoaded', function() {
    const slidingItems = document.querySelectorAll('.sliding-text-item');
    let currentIndex = 0;
    
    if (slidingItems.length === 0) return;
    
    // Initialize all items
    slidingItems.forEach((item, index) => {
        item.style.opacity = index === 0 ? '1' : '0';
        item.style.position = 'absolute';
        item.style.top = '50%';
        item.style.left = '50%';
        item.style.transform = 'translate(-50%, -50%)';
        item.style.width = '90%';
        item.style.maxWidth = '600px';
        item.style.textAlign = 'center';
        item.style.transition = 'all 0.8s cubic-bezier(0.4, 0.0, 0.2, 1)';
        item.style.zIndex = '10';
    });
    
    function slideToNext() {
        // Horizontal slide out animation
        slidingItems[currentIndex].style.opacity = '0';
        slidingItems[currentIndex].style.transform = 'translate(-150%, -50%) scale(0.95)';
        
        // Move to next index
        currentIndex = (currentIndex + 1) % slidingItems.length;
        
        // Reset next item position for horizontal entrance
        slidingItems[currentIndex].style.transform = 'translate(50%, -50%) scale(0.95)';
        slidingItems[currentIndex].style.opacity = '0';
        
        // Horizontal slide in animation
        setTimeout(() => {
            slidingItems[currentIndex].style.opacity = '1';
            slidingItems[currentIndex].style.transform = 'translate(-50%, -50%) scale(1)';
        }, 200);
    }
    
    // Start sliding after 2 seconds, then every 5 seconds
    setTimeout(() => {
        slideToNext();
        setInterval(slideToNext, 5000);
    }, 2000);
});
