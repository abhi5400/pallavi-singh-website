<?php
/**
 * JSON-based Database Configuration for Pallavi Singh Coaching Website
 * 
 * This file provides a simple file-based storage system using JSON files
 * as an alternative to MySQL when database drivers are not available
 */

// Storage directory
define('DATA_DIR', __DIR__ . '/../data/');
define('ADMIN_EMAIL', 'pallavi@thestorytree.com');

// Ensure data directory exists
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

class Database {
    private static $instance = null;
    private $dataDir;
    
    private function __construct() {
        $this->dataDir = DATA_DIR;
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get data from JSON file
     */
    public function getData($table) {
        $file = $this->dataDir . $table . '.json';
        if (!file_exists($file)) {
            return [];
        }
        $content = file_get_contents($file);
        return json_decode($content, true) ?: [];
    }
    
    /**
     * Save data to JSON file
     */
    public function saveData($table, $data) {
        $file = $this->dataDir . $table . '.json';
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Add new record to table
     */
    public function insert($table, $data) {
        $records = $this->getData($table);
        $data['id'] = count($records) + 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $records[] = $data;
        $this->saveData($table, $records);
        return $data['id'];
    }
    
    /**
     * Update record in table
     */
    public function update($table, $id, $data) {
        $records = $this->getData($table);
        foreach ($records as &$record) {
            if ($record['id'] == $id) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                $record = array_merge($record, $data);
                break;
            }
        }
        $this->saveData($table, $records);
    }
    
    /**
     * Get record by ID
     */
    public function findById($table, $id) {
        $records = $this->getData($table);
        foreach ($records as $record) {
            if ($record['id'] == $id) {
                return $record;
            }
        }
        return null;
    }
    
    /**
     * Get records with conditions
     */
    public function where($table, $conditions = []) {
        $records = $this->getData($table);
        if (empty($conditions)) {
            return $records;
        }
        
        $filtered = [];
        foreach ($records as $record) {
            $match = true;
            foreach ($conditions as $key => $value) {
                if (!isset($record[$key]) || $record[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $filtered[] = $record;
            }
        }
        return $filtered;
    }
    
    /**
     * Count records
     */
    public function count($table, $conditions = []) {
        return count($this->where($table, $conditions));
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
    
}

// Utility functions
function sendEmail($to, $subject, $body, $isHTML = true) {
    // This is a basic email function - you should implement proper SMTP
    $headers = "From: Pallavi Singh Coaching <noreply@pallavi-coaching.com>\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
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
