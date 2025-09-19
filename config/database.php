<?php
/**
 * Database Configuration for Pallavi Singh Coaching Website
 * 
 * This file contains the database connection settings and utility functions
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pallavi_coaching_db');
define('DB_USER', 'root'); // Change this to your database username
define('DB_PASS', ''); // Change this to your database password
define('DB_CHARSET', 'utf8mb4');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com'); // Change to your SMTP server
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Change to your email
define('SMTP_PASSWORD', 'your-app-password'); // Change to your app password
define('FROM_EMAIL', 'noreply@pallavi-coaching.com');
define('FROM_NAME', 'Pallavi Singh Coaching');

// Site configuration
define('SITE_URL', 'https://your-domain.com'); // Change to your domain
define('ADMIN_EMAIL', 'pallavi@thestorytree.com');

class Database {
    private $connection;
    private static $instance = null;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Get client IP address
     */
    public static function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Log form analytics
     */
    public function logFormAnalytics($formType, $actionType, $formData = null, $errorMessage = null) {
        try {
            $stmt = $this->connection->prepare("
                INSERT INTO form_analytics (form_type, action_type, user_ip, user_agent, referrer, form_data, error_message, session_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $formType,
                $actionType,
                self::getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $_SERVER['HTTP_REFERER'] ?? '',
                $formData ? json_encode($formData) : null,
                $errorMessage,
                session_id()
            ]);
        } catch (PDOException $e) {
            error_log("Failed to log form analytics: " . $e->getMessage());
        }
    }
}

// Utility functions
function sendEmail($to, $subject, $body, $isHTML = true) {
    // This is a basic email function - you should implement proper SMTP
    $headers = "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . FROM_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    if ($isHTML) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }
    
    return mail($to, $subject, $body, $headers);
}

function generateUnsubscribeToken() {
    return bin2hex(random_bytes(32));
}

function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

