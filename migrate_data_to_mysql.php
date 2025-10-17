<?php
/**
 * Data Migration Script: JSON to MySQL
 * This script will help you migrate your JSON data to MySQL database
 */

// Check if MySQL is available
function checkMySQL() {
    try {
        $pdo = new PDO("mysql:host=localhost", "root", "");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (!checkMySQL()) {
    echo "❌ MySQL is not available. Please ensure XAMPP/WAMP is running.\n";
    echo "📋 Manual Setup Instructions:\n";
    echo "1. Open phpMyAdmin: http://localhost/phpmyadmin/\n";
    echo "2. Create database: pallavi_singh\n";
    echo "3. Import the SQL file: database_setup.sql\n";
    echo "4. Run this script again to migrate data\n";
    exit;
}

echo "✅ MySQL is available!\n";

// Database configuration
$host = 'localhost';
$dbname = 'pallavi_singh';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database '$dbname'\n";
    
    // Migrate data from JSON files
    $json_files = [
        'admin_users' => 'admin_users',
        'contact_submissions' => 'contact_submissions', 
        'join_submissions' => 'join_submissions',
        'newsletter_subscriptions' => 'newsletter_subscriptions',
        'waitlist_subscriptions' => 'waitlist_subscriptions',
        'blog_posts' => 'blog_posts',
        'clients' => 'clients',
        'services' => 'services',
        'events_workshops' => 'events_workshops',
        'testimonials' => 'testimonials',
        'sessions' => 'sessions',
        'payments' => 'payments',
        'media_library' => 'media_library',
        'analytics' => 'analytics',
        'email_log' => 'email_log',
        'booking_submissions' => 'booking_submissions',
        'journey_submissions' => 'journey_submissions'
    ];
    
    foreach ($json_files as $table_name => $json_file) {
        $json_path = __DIR__ . "/data/$json_file.json";
        
        if (file_exists($json_path)) {
            $json_data = json_decode(file_get_contents($json_path), true);
            
            if (!empty($json_data)) {
                // Clear existing data
                $pdo->exec("DELETE FROM `$table_name`");
                
                // Insert data
                $inserted_count = 0;
                foreach ($json_data as $record) {
                    $columns = array_keys($record);
                    $placeholders = ':' . implode(', :', $columns);
                    $sql = "INSERT INTO `$table_name` (" . implode(', ', $columns) . ") VALUES ($placeholders)";
                    
                    try {
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($record);
                        $inserted_count++;
                    } catch (Exception $e) {
                        echo "⚠️  Warning: Could not insert record into $table_name: " . $e->getMessage() . "\n";
                    }
                }
                
                echo "✅ Migrated $inserted_count records to '$table_name' table\n";
            } else {
                echo "ℹ️  No data found in $json_file.json\n";
            }
        } else {
            echo "ℹ️  File $json_file.json not found\n";
        }
    }
    
    echo "\n🎉 Data migration completed successfully!\n";
    echo "📊 Database: $dbname\n";
    echo "🌐 Access phpMyAdmin: http://localhost/phpmyadmin/index.php?route=/database/structure&db=$dbname\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Please ensure the database and tables are created first.\n";
}
?>
