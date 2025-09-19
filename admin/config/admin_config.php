<?php
/**
 * Admin Panel Configuration - Pallavi Singh Coaching
 * Centralized configuration for admin panel settings
 */

// Admin Panel Settings
define('ADMIN_PANEL_NAME', 'Pallavi Singh Coaching');
define('ADMIN_PANEL_VERSION', '1.0.0');
define('ADMIN_PANEL_THEME', 'modern');

// Dashboard Settings
define('DASHBOARD_ITEMS_PER_PAGE', 10);
define('DASHBOARD_AUTO_REFRESH', 30000); // 30 seconds in milliseconds
define('DASHBOARD_SHOW_ANALYTICS', true);

// Security Settings
define('ADMIN_SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('ADMIN_MAX_LOGIN_ATTEMPTS', 5);
define('ADMIN_LOCKOUT_DURATION', 900); // 15 minutes in seconds

// Email Settings
define('ADMIN_NOTIFICATION_EMAIL', 'pallavi@thestorytree.com');
define('ADMIN_EMAIL_FROM_NAME', 'Pallavi Singh Coaching Admin');

// File Upload Settings
define('ADMIN_MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ADMIN_ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Display Settings
define('ADMIN_DATE_FORMAT', 'M j, Y H:i');
define('ADMIN_TIMEZONE', 'Asia/Kolkata');
define('ADMIN_CURRENCY', 'INR');

// Feature Flags
define('ADMIN_ENABLE_EXPORT', true);
define('ADMIN_ENABLE_BULK_ACTIONS', true);
define('ADMIN_ENABLE_ADVANCED_FILTERS', true);
define('ADMIN_ENABLE_REAL_TIME_UPDATES', false);

// API Settings
define('ADMIN_API_ENABLED', false);
define('ADMIN_API_RATE_LIMIT', 100); // requests per hour

// Logging Settings
define('ADMIN_LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('ADMIN_LOG_RETENTION_DAYS', 30);

// Cache Settings
define('ADMIN_CACHE_ENABLED', true);
define('ADMIN_CACHE_DURATION', 300); // 5 minutes in seconds

/**
 * Get admin configuration value
 */
function getAdminConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Check if feature is enabled
 */
function isFeatureEnabled($feature) {
    $featureKey = 'ADMIN_ENABLE_' . strtoupper($feature);
    return getAdminConfig($featureKey, false);
}

/**
 * Get admin panel info
 */
function getAdminPanelInfo() {
    return [
        'name' => ADMIN_PANEL_NAME,
        'version' => ADMIN_PANEL_VERSION,
        'theme' => ADMIN_PANEL_THEME,
        'timezone' => ADMIN_TIMEZONE,
        'date_format' => ADMIN_DATE_FORMAT
    ];
}

/**
 * Format date according to admin settings
 */
function formatAdminDate($date, $format = null) {
    $format = $format ?: ADMIN_DATE_FORMAT;
    $dateTime = new DateTime($date);
    $dateTime->setTimezone(new DateTimeZone(ADMIN_TIMEZONE));
    return $dateTime->format($format);
}

/**
 * Get admin navigation items
 */
function getAdminNavigation() {
    return [
        'dashboard' => [
            'title' => '📊 Dashboard',
            'url' => 'dashboard.php',
            'icon' => '📊'
        ],
        'contacts' => [
            'title' => '📧 Contacts',
            'url' => 'contacts.php',
            'icon' => '📧'
        ],
        'bookings' => [
            'title' => '📅 Bookings',
            'url' => 'bookings.php',
            'icon' => '📅'
        ],
        'journeys' => [
            'title' => '🌟 Journeys',
            'url' => 'journeys.php',
            'icon' => '🌟'
        ],
        'newsletter' => [
            'title' => '📬 Newsletter',
            'url' => 'newsletter.php',
            'icon' => '📬'
        ],
        'settings' => [
            'title' => '⚙️ Settings',
            'url' => 'settings.php',
            'icon' => '⚙️'
        ]
    ];
}

/**
 * Log admin activity
 */
function logAdminActivity($action, $details = '') {
    if (ADMIN_LOG_LEVEL === 'DEBUG' || ADMIN_LOG_LEVEL === 'INFO') {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user' => $_SESSION['admin_user']['username'] ?? 'unknown',
            'action' => $action,
            'details' => $details,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        $logFile = __DIR__ . '/../logs/admin_activity.log';
        $logDir = dirname($logFile);
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
}

// Set timezone
date_default_timezone_set(ADMIN_TIMEZONE);
?>
