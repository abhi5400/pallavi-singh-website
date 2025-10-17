<?php
/**
 * MySQL Database Configuration for Pallavi Singh Coaching Website
 * 
 * This file provides MySQL database connection and operations
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pallavi_singh');
define('DB_USER', 'root'); // Change this to your MySQL username
define('DB_PASS', ''); // Change this to your MySQL password
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static $instance = null;
    private $connection;
    private $useJson = false;
    private $jsonDb;
    
    private function __construct() {
        // Check if PDO MySQL is available
        if (class_exists('PDO') && in_array('mysql', PDO::getAvailableDrivers())) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
                $this->useJson = false;
                error_log("MySQL database connection successful");
            } catch (PDOException $e) {
                error_log("MySQL connection failed, falling back to JSON: " . $e->getMessage());
                $this->useJson = true;
            }
        } else {
            error_log("PDO MySQL driver not available, using JSON database");
            $this->useJson = true;
        }
        
        if ($this->useJson) {
            // Initialize JSON database
            require_once __DIR__ . '/database_json.php';
            $this->jsonDb = JsonDatabase::getInstance();
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
     * Execute a query with parameters
     */
    public function query($sql, $params = []) {
        if ($this->useJson) {
            // For JSON database, we don't use SQL queries
            throw new Exception("SQL queries not supported in JSON mode");
        }
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            throw $e;
        }
    }
    
    /**
     * Insert data and return the last insert ID
     */
    public function insert($table, $data) {
        if ($this->useJson) {
            return $this->jsonDb->insert($table, $data);
        }
        
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return $this->connection->lastInsertId();
    }
    
    /**
     * Update data
     */
    public function update($table, $data, $where, $whereParams = []) {
        if ($this->useJson) {
            return $this->jsonDb->update($table, $data, $where, $whereParams);
        }
        
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Select data
     */
    public function select($table, $where = '', $params = [], $orderBy = '', $limit = '') {
        if ($this->useJson) {
            return $this->jsonDb->getData($table);
        }
        
        $sql = "SELECT * FROM {$table}";
        
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if (!empty($limit)) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Find record by ID
     */
    public function findById($table, $id) {
        if ($this->useJson) {
            $data = $this->jsonDb->getData($table);
            foreach ($data as $record) {
                if ($record['id'] == $id) {
                    return $record;
                }
            }
            return null;
        }
        
        $sql = "SELECT * FROM {$table} WHERE id = :id";
        $stmt = $this->query($sql, ['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Find records with conditions
     */
    public function where($table, $conditions = []) {
        if ($this->useJson) {
            return $this->jsonDb->where($table, $conditions);
        }
        
        if (empty($conditions)) {
            return $this->select($table);
        }
        
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $whereClause[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        
        $where = implode(' AND ', $whereClause);
        return $this->select($table, $where, $params);
    }
    
    /**
     * Count records
     */
    public function count($table, $where = '', $params = []) {
        if ($this->useJson) {
            $data = $this->jsonDb->getData($table);
            return count($data);
        }
        
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Delete records
     */
    public function delete($table, $where, $params = []) {
        if ($this->useJson) {
            return $this->jsonDb->delete($table, $where, $params);
        }
        
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
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
     * Begin transaction
     */
    public function beginTransaction() {
        if ($this->useJson) {
            // JSON database doesn't support transactions
            return true;
        }
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        if ($this->useJson) {
            // JSON database doesn't support transactions
            return true;
        }
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        if ($this->useJson) {
            // JSON database doesn't support transactions
            return true;
        }
        return $this->connection->rollback();
    }
    
    /**
     * Check if using JSON database
     */
    public function isUsingJson() {
        return $this->useJson;
    }
    
    /**
     * Get database type
     */
    public function getDatabaseType() {
        return $this->useJson ? 'JSON' : 'MySQL';
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>