/**
 * Admin Panel JavaScript - Pallavi Singh Coaching
 * Handles interactive features and animations
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeAdminPanel();
});

/**
 * Initialize all admin panel features
 */
function initializeAdminPanel() {
    animateStatCards();
    addTableRowEffects();
    addButtonAnimations();
    addStatusBadgeAnimations();
    addLogoutConfirmation();
    addSmoothScrolling();
    initializeAutoRefresh();
}

/**
 * Animate stat cards on page load
 */
function animateStatCards() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });
}

/**
 * Add hover effects to table rows
 */
function addTableRowEffects() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
}

/**
 * Add click animations to buttons
 */
function addButtonAnimations() {
    const buttons = document.querySelectorAll('button, .logout');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
}

/**
 * Add pulse animation to new status badges
 */
function addStatusBadgeAnimations() {
    const newStatusBadges = document.querySelectorAll('.status.new');
    newStatusBadges.forEach(badge => {
        badge.classList.add('pulse');
    });
}

/**
 * Add logout confirmation dialog
 */
function addLogoutConfirmation() {
    const logoutLink = document.querySelector('.logout');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    }
}

/**
 * Enable smooth scrolling
 */
function addSmoothScrolling() {
    document.documentElement.style.scrollBehavior = 'smooth';
}

/**
 * Initialize auto-refresh functionality
 */
function initializeAutoRefresh() {
    // Auto-refresh data every 30 seconds (optional)
    setInterval(() => {
        console.log('Auto-refresh: Data updated');
        // You can add AJAX call here to refresh data without page reload
        // refreshDashboardData();
    }, 30000);
}

/**
 * Show loading animation
 */
function showLoading() {
    const loader = document.createElement('div');
    loader.innerHTML = `
        <div style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        ">
            <div style="
                text-align: center;
                color: #1A535C;
                font-size: 1.2em;
                font-weight: 600;
            ">
                <div style="
                    width: 50px;
                    height: 50px;
                    border: 4px solid #e0e0e0;
                    border-top: 4px solid #1A535C;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 20px;
                "></div>
                Loading...
            </div>
        </div>
        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    `;
    document.body.appendChild(loader);
    
    setTimeout(() => {
        if (document.body.contains(loader)) {
            document.body.removeChild(loader);
        }
    }, 1000);
}

/**
 * Refresh dashboard data via AJAX
 */
function refreshDashboardData() {
    // This function can be used to refresh data without page reload
    fetch(window.location.href)
        .then(response => response.text())
        .then(data => {
            // Parse the response and update specific elements
            console.log('Dashboard data refreshed');
        })
        .catch(error => {
            console.error('Error refreshing data:', error);
        });
}

/**
 * Show notification message
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'error' ? '#ff6b6b' : type === 'success' ? '#4ECDC4' : '#1A535C';
    
    notification.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${bgColor};
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        ">
            ${message}
        </div>
        <style>
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        </style>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (document.body.contains(notification)) {
            document.body.removeChild(notification);
        }
    }, 3000);
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showNotification('Failed to copy to clipboard', 'error');
    });
}

/**
 * Export data as CSV
 */
function exportToCSV(data, filename) {
    const csvContent = "data:text/csv;charset=utf-8," + data;
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Global functions for use in HTML
window.showLoading = showLoading;
window.showNotification = showNotification;
window.copyToClipboard = copyToClipboard;
window.exportToCSV = exportToCSV;
