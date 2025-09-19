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
            <a href="#" class="nav-link">
                📧 Contacts
            </a>
            <a href="#" class="nav-link">
                📅 Bookings
            </a>
            <a href="#" class="nav-link">
                🌟 Journeys
            </a>
            <a href="#" class="nav-link">
                📬 Newsletter
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
