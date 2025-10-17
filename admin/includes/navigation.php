<?php
/**
 * Admin Navigation Component - Pallavi Singh Coaching
 * Reusable navigation for admin pages
 */

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    return;
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="admin-nav">
    <div class="nav-container">
        <div class="nav-brand">
            <h2>🎯 Admin Panel</h2>
        </div>
        
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                📊 Dashboard
            </a>
            <a href="analytics.php" class="nav-link <?php echo $currentPage === 'analytics.php' ? 'active' : ''; ?>">
                📈 Analytics
            </a>
            
            <!-- Forms Section -->
            <div class="nav-dropdown">
                <a href="forms.php" class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['forms.php', 'contact-forms.php', 'join-forms.php', 'booking-forms.php', 'journey-forms.php', 'waitlist-forms.php']) ? 'active' : ''; ?>">
                    📋 Forms <span class="dropdown-arrow">▼</span>
                </a>
                <div class="dropdown-menu">
                    <a href="forms.php" class="dropdown-item">📊 Forms Overview</a>
                    <a href="contact-forms.php" class="dropdown-item">📧 Contact Forms</a>
                    <a href="join-forms.php" class="dropdown-item">🤝 Join Forms</a>
                    <a href="newsletter.php" class="dropdown-item">📬 Newsletter</a>
                    <a href="booking-forms.php" class="dropdown-item">📅 Bookings</a>
                    <a href="journey-forms.php" class="dropdown-item">🌟 Journeys</a>
                    <a href="waitlist-forms.php" class="dropdown-item">⏳ Waitlist</a>
                </div>
            </div>
            
            <!-- Content Management -->
            <div class="nav-dropdown">
                <a href="#" class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['blog.php', 'media.php', 'testimonials.php', 'services.php', 'events.php']) ? 'active' : ''; ?>">
                    📝 Content <span class="dropdown-arrow">▼</span>
                </a>
                <div class="dropdown-menu">
                    <a href="blog.php" class="dropdown-item">📝 Blog Posts</a>
                    <a href="media.php" class="dropdown-item">🖼️ Media Library</a>
                    <a href="testimonials.php" class="dropdown-item">⭐ Testimonials</a>
                    <a href="services.php" class="dropdown-item">🎯 Services</a>
                    <a href="events.php" class="dropdown-item">📅 Events</a>
                </div>
            </div>
            
            <!-- Client Management -->
            <div class="nav-dropdown">
                <a href="#" class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['clients.php', 'sessions.php', 'users.php']) ? 'active' : ''; ?>">
                    👥 Clients <span class="dropdown-arrow">▼</span>
                </a>
                <div class="dropdown-menu">
                    <a href="clients.php" class="dropdown-item">👥 Client List</a>
                    <a href="sessions.php" class="dropdown-item">🕐 Sessions</a>
                    <a href="users.php" class="dropdown-item">👤 Users</a>
                </div>
            </div>
            
            <a href="settings.php" class="nav-link <?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
                ⚙️ Settings
            </a>
        </div>
        
        <div class="nav-user">
            <span class="user-name">👤 <?php echo htmlspecialchars($_SESSION['admin_user']['full_name']); ?></span>
            <a href="?logout=1" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
</nav>

<style>
.admin-nav {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 15px 0;
    margin-bottom: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav-brand h2 {
    color: #1A535C;
    font-size: 1.5em;
    margin: 0;
    background: linear-gradient(135deg, #1A535C, #4ECDC4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-menu {
    display: flex;
    gap: 20px;
    align-items: center;
}

.nav-dropdown {
    position: relative;
}

.dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 5px;
}

.dropdown-arrow {
    font-size: 0.8em;
    transition: transform 0.3s ease;
}

.nav-dropdown:hover .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border: 1px solid rgba(0,0,0,0.1);
    min-width: 200px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.nav-dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: block;
    padding: 12px 20px;
    color: #666;
    text-decoration: none;
    transition: all 0.3s ease;
    border-bottom: 1px solid #f0f0f0;
}

.dropdown-item:last-child {
    border-bottom: none;
    border-radius: 0 0 10px 10px;
}

.dropdown-item:first-child {
    border-radius: 10px 10px 0 0;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #1A535C, #4ECDC4);
    color: white;
    transform: translateX(5px);
}

.nav-link {
    color: #666;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-link:hover,
.nav-link.active {
    background: linear-gradient(135deg, #1A535C, #4ECDC4);
    color: white;
    transform: translateY(-2px);
}

.nav-user {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-name {
    color: #666;
    font-weight: 500;
}

.logout-btn {
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

@media (max-width: 768px) {
    .nav-container {
        flex-direction: column;
        gap: 15px;
    }
    
    .nav-menu {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .nav-user {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
