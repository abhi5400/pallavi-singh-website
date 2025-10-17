<?php
/**
 * Production Database Configuration for Live Website
 * Use environment variables for security
 */

// Production database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'pallavi_singh');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', 'utf8mb4');

class ProductionDatabase {
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
                error_log("Production MySQL database connection successful");
            } catch (PDOException $e) {
                error_log("Production MySQL connection failed, falling back to JSON: " . $e->getMessage());
                $this->useJson = true;
            }
        } else {
            error_log("PDO MySQL driver not available, using JSON database");
            $this->useJson = true;
        }
        
        if ($this->useJson) {
            // Initialize JSON database as fallback
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
     * Insert data and return the last insert ID
     */
    public function insert($table, $data) {
        if ($this->useJson) {
            return $this->jsonDb->insert($table, $data);
        }
        
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->connection->prepare($sql)->execute($data);
        
        return $this->connection->lastInsertId();
    }
    
    /**
     * Update data
     */
    public function update($table, $id, $data) {
        if ($this->useJson) {
            return $this->jsonDb->update($table, $id, $data);
        }
        
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($data);
    }
    
    /**
     * Find records with conditions
     */
    public function where($table, $conditions = []) {
        if ($this->useJson) {
            return $this->jsonDb->where($table, $conditions);
        }
        
        if (empty($conditions)) {
            $sql = "SELECT * FROM {$table}";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $whereClause[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        
        $where = implode(' AND ', $whereClause);
        $sql = "SELECT * FROM {$table} WHERE {$where}";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Count records
     */
    public function count($table, $conditions = []) {
        if ($this->useJson) {
            return $this->jsonDb->count($table, $conditions);
        }
        
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'];
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
