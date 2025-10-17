<?php
/**
 * Admin Layout Template - Pallavi Singh Coaching
 * Main layout template with sidebar and top navigation
 */

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle = $pageTitle ?? 'Admin Dashboard';
$pageSubtitle = $pageSubtitle ?? 'Manage your coaching business';

// Get user info
$user = $_SESSION['admin_user'];
$userInitials = strtoupper(substr($user['full_name'], 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Pallavi Singh Coaching</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin-layout.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-tachometer-alt"></i> Admin Panel</h2>
                <p>Pallavi Singh Coaching</p>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="analytics.php" class="nav-item <?php echo $currentPage === 'analytics' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytics</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Forms</div>
                    <a href="forms.php" class="nav-item <?php echo $currentPage === 'forms' ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Forms Overview</span>
                    </a>
                    <a href="contact-forms.php" class="nav-item <?php echo $currentPage === 'contact-forms' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i>
                        <span>Contact Forms</span>
                    </a>
                    <a href="join-forms.php" class="nav-item <?php echo $currentPage === 'join-forms' ? 'active' : ''; ?>">
                        <i class="fas fa-handshake"></i>
                        <span>Join Forms</span>
                    </a>
                    <a href="booking-forms.php" class="nav-item <?php echo $currentPage === 'booking-forms' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                    <a href="journey-forms.php" class="nav-item <?php echo $currentPage === 'journey-forms' ? 'active' : ''; ?>">
                        <i class="fas fa-star"></i>
                        <span>Journeys</span>
                    </a>
                    <a href="waitlist-forms.php" class="nav-item <?php echo $currentPage === 'waitlist-forms' ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i>
                        <span>Waitlist</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="clients.php" class="nav-item <?php echo $currentPage === 'clients' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Clients</span>
                    </a>
                    <a href="sessions.php" class="nav-item <?php echo $currentPage === 'sessions' ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i>
                        <span>Sessions</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Content</div>
                    <a href="blog.php" class="nav-item <?php echo $currentPage === 'blog' ? 'active' : ''; ?>">
                        <i class="fas fa-blog"></i>
                        <span>Blog Posts</span>
                    </a>
                    <a href="events.php" class="nav-item <?php echo $currentPage === 'events' ? 'active' : ''; ?>">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events & Workshops</span>
                    </a>
                    <a href="services.php" class="nav-item <?php echo $currentPage === 'services' ? 'active' : ''; ?>">
                        <i class="fas fa-bullseye"></i>
                        <span>Services</span>
                    </a>
                    <a href="testimonials.php" class="nav-item <?php echo $currentPage === 'testimonials' ? 'active' : ''; ?>">
                        <i class="fas fa-star"></i>
                        <span>Testimonials</span>
                    </a>
                    <a href="media.php" class="nav-item <?php echo $currentPage === 'media' ? 'active' : ''; ?>">
                        <i class="fas fa-images"></i>
                        <span>Media Library</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <a href="settings.php" class="nav-item <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="users.php" class="nav-item <?php echo $currentPage === 'users' ? 'active' : ''; ?>">
                        <i class="fas fa-user-cog"></i>
                        <span>Users</span>
                    </a>
                    <a href="backup.php" class="nav-item <?php echo $currentPage === 'backup' ? 'active' : ''; ?>">
                        <i class="fas fa-database"></i>
                        <span>Backup</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar"><?php echo $userInitials; ?></div>
                    <div class="user-details">
                        <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
                        <p><?php echo htmlspecialchars($user['role']); ?></p>
                    </div>
                </div>
                <a href="?logout=1" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Navigation Bar -->
            <div class="top-navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="breadcrumb">
                        <a href="dashboard.php">Dashboard</a>
                        <span class="breadcrumb-separator">›</span>
                        <span><?php echo htmlspecialchars($pageTitle); ?></span>
                    </div>
                </div>
                
                <div class="navbar-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search..." id="globalSearch">
                    </div>
                    
                    <button class="notification-bell" id="notificationBell">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge">3</span>
                    </button>
                    
                    <div class="user-menu">
                        <button class="user-menu-toggle">
                            <div class="user-menu-avatar"><?php echo $userInitials; ?></div>
                            <span class="user-menu-name"><?php echo htmlspecialchars($user['full_name']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-menu-dropdown">
                            <a href="profile.php" class="dropdown-item">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a href="settings.php" class="dropdown-item">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <a href="help.php" class="dropdown-item">
                                <i class="fas fa-question-circle"></i> Help
                            </a>
                            <a href="?logout=1" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="content-area">
                <div class="page-header">
                    <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <p class="page-subtitle"><?php echo htmlspecialchars($pageSubtitle); ?></p>
                </div>
                
                <!-- Page Content -->
                <div class="page-content">
                    <?php if (isset($pageContent)): ?>
                        <?php echo $pageContent; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/admin-layout.js"></script>
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
