/**
 * Admin Layout JavaScript - Pallavi Singh Coaching
 * Handles sidebar, navigation, and layout interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminLayout();
});

/**
 * Initialize admin layout functionality
 */
function initializeAdminLayout() {
    initializeSidebar();
    initializeSearch();
    initializeNotifications();
    initializeUserMenu();
    initializeResponsive();
    initializeBreadcrumbs();
}

/**
 * Initialize sidebar functionality
 */
function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    console.log('Sidebar elements found:', { sidebar, mainContent, sidebarToggle });
    
    // Toggle sidebar
    if (sidebarToggle) {
        console.log('Adding click event listener to sidebar toggle');
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Sidebar toggle clicked');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            console.log('Sidebar collapsed:', sidebar.classList.contains('collapsed'));
            console.log('Main content expanded:', mainContent.classList.contains('expanded'));
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
    } else {
        console.error('Sidebar toggle button not found!');
    }
    
    // Load saved sidebar state
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }
    
    // Add hover effects to nav items
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            if (!sidebar.classList.contains('collapsed')) return;
            
            // Show tooltip for collapsed sidebar
            showTooltip(this);
        });
        
        item.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
    
    // Ensure smooth scrolling in sidebar
    const sidebarNav = document.querySelector('.sidebar-nav');
    if (sidebarNav) {
        sidebarNav.style.scrollBehavior = 'smooth';
    }
}

/**
 * Initialize global search functionality
 */
function initializeSearch() {
    const searchInput = document.getElementById('globalSearch');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length > 2) {
                searchTimeout = setTimeout(() => {
                    performGlobalSearch(query);
                }, 300);
            } else {
                hideSearchResults();
            }
        });
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }
}

/**
 * Initialize notifications
 */
function initializeNotifications() {
    const notificationBell = document.getElementById('notificationBell');
    const notificationBadge = document.getElementById('notificationBadge');
    
    if (notificationBell) {
        notificationBell.addEventListener('click', function() {
            showNotifications();
        });
    }
    
    // Update notification count
    updateNotificationCount();
}

/**
 * Initialize user menu
 */
function initializeUserMenu() {
    const userMenu = document.querySelector('.user-menu');
    
    if (userMenu) {
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target)) {
                const dropdown = userMenu.querySelector('.user-menu-dropdown');
                if (dropdown) {
                    dropdown.style.opacity = '0';
                    dropdown.style.visibility = 'hidden';
                }
            }
        });
    }
}

/**
 * Initialize responsive behavior
 */
function initializeResponsive() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    function handleResize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('mobile-closed');
            mainContent.classList.remove('expanded');
        } else {
            sidebar.classList.remove('mobile-closed');
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize(); // Initial call
}

/**
 * Initialize breadcrumbs
 */
function initializeBreadcrumbs() {
    const breadcrumbs = document.querySelectorAll('.breadcrumb a');
    
    breadcrumbs.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading state
            showLoading();
        });
    });
}

/**
 * Show tooltip for collapsed sidebar items
 */
function showTooltip(element) {
    const tooltip = document.createElement('div');
    tooltip.className = 'sidebar-tooltip';
    tooltip.textContent = element.querySelector('span').textContent;
    
    // Position tooltip
    const rect = element.getBoundingClientRect();
    tooltip.style.position = 'fixed';
    tooltip.style.left = rect.right + 10 + 'px';
    tooltip.style.top = rect.top + (rect.height / 2) - 15 + 'px';
    tooltip.style.zIndex = '10000';
    
    // Style tooltip
    tooltip.style.background = '#1A535C';
    tooltip.style.color = 'white';
    tooltip.style.padding = '8px 12px';
    tooltip.style.borderRadius = '6px';
    tooltip.style.fontSize = '0.9em';
    tooltip.style.whiteSpace = 'nowrap';
    tooltip.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    tooltip.style.opacity = '0';
    tooltip.style.transition = 'opacity 0.2s ease';
    
    document.body.appendChild(tooltip);
    
    // Animate in
    setTimeout(() => {
        tooltip.style.opacity = '1';
    }, 10);
}

/**
 * Hide tooltip
 */
function hideTooltip() {
    const tooltip = document.querySelector('.sidebar-tooltip');
    if (tooltip) {
        tooltip.style.opacity = '0';
        setTimeout(() => {
            if (tooltip.parentNode) {
                tooltip.parentNode.removeChild(tooltip);
            }
        }, 200);
    }
}

/**
 * Perform global search
 */
function performGlobalSearch(query) {
    // This would typically make an AJAX call to search across all data
    console.log('Searching for:', query);
    
    // Show search results (placeholder)
    showSearchResults([
        { type: 'Contact', title: 'John Doe', url: 'contacts.php?id=1' },
        { type: 'Booking', title: 'Session Request', url: 'bookings.php?id=2' },
        { type: 'Journey', title: 'Transformation Journey', url: 'journeys.php?id=3' }
    ]);
}

/**
 * Show search results
 */
function showSearchResults(results) {
    hideSearchResults(); // Remove existing results
    
    const searchBox = document.querySelector('.search-box');
    const resultsContainer = document.createElement('div');
    resultsContainer.className = 'search-results';
    
    results.forEach(result => {
        const resultItem = document.createElement('a');
        resultItem.href = result.url;
        resultItem.className = 'search-result-item';
        resultItem.innerHTML = `
            <div class="search-result-type">${result.type}</div>
            <div class="search-result-title">${result.title}</div>
        `;
        resultsContainer.appendChild(resultItem);
    });
    
    // Style results container
    resultsContainer.style.position = 'absolute';
    resultsContainer.style.top = '100%';
    resultsContainer.style.left = '0';
    resultsContainer.style.right = '0';
    resultsContainer.style.background = 'white';
    resultsContainer.style.borderRadius = '8px';
    resultsContainer.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
    resultsContainer.style.zIndex = '1000';
    resultsContainer.style.maxHeight = '300px';
    resultsContainer.style.overflowY = 'auto';
    
    searchBox.style.position = 'relative';
    searchBox.appendChild(resultsContainer);
}

/**
 * Hide search results
 */
function hideSearchResults() {
    const existingResults = document.querySelector('.search-results');
    if (existingResults) {
        existingResults.remove();
    }
}

/**
 * Show notifications
 */
function showNotifications() {
    // Create notifications panel
    const panel = document.createElement('div');
    panel.className = 'notifications-panel';
    panel.innerHTML = `
        <div class="notifications-header">
            <h3>Notifications</h3>
            <button class="close-notifications">×</button>
        </div>
        <div class="notifications-list">
            <div class="notification-item">
                <div class="notification-icon">📧</div>
                <div class="notification-content">
                    <div class="notification-title">New Contact Form</div>
                    <div class="notification-message">John Doe submitted a contact form</div>
                    <div class="notification-time">2 minutes ago</div>
                </div>
            </div>
            <div class="notification-item">
                <div class="notification-icon">📅</div>
                <div class="notification-content">
                    <div class="notification-title">Booking Request</div>
                    <div class="notification-message">New session booking from Jane Smith</div>
                    <div class="notification-time">15 minutes ago</div>
                </div>
            </div>
            <div class="notification-item">
                <div class="notification-icon">🌟</div>
                <div class="notification-content">
                    <div class="notification-title">Journey Signup</div>
                    <div class="notification-message">New transformation journey signup</div>
                    <div class="notification-time">1 hour ago</div>
                </div>
            </div>
        </div>
    `;
    
    // Style panel
    panel.style.position = 'fixed';
    panel.style.top = '70px';
    panel.style.right = '20px';
    panel.style.width = '350px';
    panel.style.background = 'white';
    panel.style.borderRadius = '10px';
    panel.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
    panel.style.zIndex = '10000';
    panel.style.opacity = '0';
    panel.style.transform = 'translateY(-10px)';
    panel.style.transition = 'all 0.3s ease';
    
    document.body.appendChild(panel);
    
    // Animate in
    setTimeout(() => {
        panel.style.opacity = '1';
        panel.style.transform = 'translateY(0)';
    }, 10);
    
    // Close button functionality
    panel.querySelector('.close-notifications').addEventListener('click', () => {
        panel.style.opacity = '0';
        panel.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            if (panel.parentNode) {
                panel.parentNode.removeChild(panel);
            }
        }, 300);
    });
    
    // Close on outside click
    setTimeout(() => {
        document.addEventListener('click', function closeOnOutsideClick(e) {
            if (!panel.contains(e.target) && !document.getElementById('notificationBell').contains(e.target)) {
                panel.style.opacity = '0';
                panel.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (panel.parentNode) {
                        panel.parentNode.removeChild(panel);
                    }
                }, 300);
                document.removeEventListener('click', closeOnOutsideClick);
            }
        });
    }, 100);
}

/**
 * Update notification count
 */
function updateNotificationCount() {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        // This would typically fetch from server
        const count = 3; // Placeholder
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

/**
 * Show loading state
 */
function showLoading() {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Loading...</p>
        </div>
    `;
    
    // Style overlay
    loadingOverlay.style.position = 'fixed';
    loadingOverlay.style.top = '0';
    loadingOverlay.style.left = '0';
    loadingOverlay.style.width = '100%';
    loadingOverlay.style.height = '100%';
    loadingOverlay.style.background = 'rgba(255, 255, 255, 0.9)';
    loadingOverlay.style.display = 'flex';
    loadingOverlay.style.alignItems = 'center';
    loadingOverlay.style.justifyContent = 'center';
    loadingOverlay.style.zIndex = '9999';
    loadingOverlay.style.backdropFilter = 'blur(5px)';
    
    document.body.appendChild(loadingOverlay);
    
    // Auto remove after 2 seconds
    setTimeout(() => {
        if (loadingOverlay.parentNode) {
            loadingOverlay.parentNode.removeChild(loadingOverlay);
        }
    }, 2000);
}

// Global functions
window.showLoading = showLoading;
window.showNotifications = showNotifications;

// Debug function to test sidebar toggle
window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebar && mainContent) {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
        
        console.log('Sidebar toggled manually. Collapsed:', isCollapsed);
    } else {
        console.error('Sidebar or main content element not found!');
    }
};
