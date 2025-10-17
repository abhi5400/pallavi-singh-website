<?php
/**
 * Simple Migration Script: JSON to MySQL
 */

// Database configuration
$host = 'localhost';
$dbname = 'pallavi_singh';
$username = 'root';
$password = '';

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
    
    echo "✅ Database '$dbname' created successfully!\n";
    
    // Create tables
    $tables = [
        'admin_users' => "CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role VARCHAR(20) DEFAULT 'admin',
            is_active BOOLEAN DEFAULT TRUE,
            last_login DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        'contact_submissions' => "CREATE TABLE IF NOT EXISTS contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NULL,
            subject VARCHAR(100) NULL,
            service_interest VARCHAR(100) NULL,
            message TEXT NOT NULL,
            submission_date DATETIME NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT NULL,
            status VARCHAR(20) DEFAULT 'new',
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        'join_submissions' => "CREATE TABLE IF NOT EXISTS join_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            form_id VARCHAR(50) NOT NULL UNIQUE,
            full_name VARCHAR(100) NOT NULL,
            age INT NULL,
            city VARCHAR(100) NOT NULL,
            state VARCHAR(100) NOT NULL,
            contact_number VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL,
            issue_challenge TEXT NOT NULL,
            goals TEXT NULL,
            terms_accepted BOOLEAN DEFAULT FALSE,
            newsletter_subscription BOOLEAN DEFAULT FALSE,
            submission_date DATETIME NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT NULL,
            status VARCHAR(20) DEFAULT 'new',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        echo "✅ Table '$name' created successfully!\n";
    }
    
    // Migrate data
    $json_files = ['admin_users', 'contact_submissions', 'join_submissions'];
    
    foreach ($json_files as $file) {
        $json_path = __DIR__ . "/data/$file.json";
        
        if (file_exists($json_path)) {
            $data = json_decode(file_get_contents($json_path), true);
            
            if (!empty($data)) {
                // Clear existing data
                $pdo->exec("DELETE FROM $file");
                
                // Insert data
                $count = 0;
                foreach ($data as $record) {
                    $columns = array_keys($record);
                    $placeholders = ':' . implode(', :', $columns);
                    $sql = "INSERT INTO $file (" . implode(', ', $columns) . ") VALUES ($placeholders)";
                    
                    try {
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($record);
                        $count++;
                    } catch (Exception $e) {
                        echo "⚠️  Warning: Could not insert record: " . $e->getMessage() . "\n";
                    }
                }
                
                echo "✅ Migrated $count records to '$file' table\n";
            }
        }
    }
    
    echo "\n🎉 Migration completed!\n";
    echo "🌐 Access phpMyAdmin: http://localhost/phpmyadmin/index.php?route=/database/structure&db=$dbname\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
