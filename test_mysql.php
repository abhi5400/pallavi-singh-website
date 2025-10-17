<?php
/**
 * Test MySQL database connection and basic operations
 */

require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>MySQL Database Test</h1>";

try {
    // Test database connection
    echo "<h2>Testing Database Connection</h2>";
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "✓ Database connection successful<br>";
    
    // Test basic query
    echo "<h2>Testing Basic Query</h2>";
    $result = $db->query("SELECT 1 as test");
    $row = $result->fetch();
    echo "✓ Basic query successful: " . $row['test'] . "<br>";
    
    // Test table existence
    echo "<h2>Testing Table Existence</h2>";
    $tables = ['admin_users', 'join_submissions', 'newsletter_subscriptions', 'contact_submissions', 'blog_posts', 'testimonials', 'services'];
    
    foreach ($tables as $table) {
        try {
            $result = $db->query("SHOW TABLES LIKE ?", [$table]);
            if ($result->rowCount() > 0) {
                echo "✓ Table '{$table}' exists<br>";
            } else {
                echo "✗ Table '{$table}' does not exist<br>";
            }
        } catch (Exception $e) {
            echo "✗ Error checking table '{$table}': " . $e->getMessage() . "<br>";
        }
    }
    
    // Test insert operation
    echo "<h2>Testing Insert Operation</h2>";
    try {
        $testData = [
            'form_id' => 'TEST-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            'full_name' => 'Test User',
            'city' => 'Test City',
            'state' => 'Test State',
            'contact_number' => '1234567890',
            'email' => 'test@example.com',
            'issue_challenge' => 'This is a test submission',
            'terms_accepted' => 1,
            'newsletter_subscription' => 0,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'status' => 'new'
        ];
        
        $id = $db->insert('join_submissions', $testData);
        echo "✓ Test insert successful, ID: {$id}<br>";
        
        // Clean up test data
        $db->delete('join_submissions', 'id = ?', [$id]);
        echo "✓ Test data cleaned up<br>";
        
    } catch (Exception $e) {
        echo "✗ Insert test failed: " . $e->getMessage() . "<br>";
    }
    
    // Test select operation
    echo "<h2>Testing Select Operation</h2>";
    try {
        $results = $db->select('admin_users', '', [], 'id ASC', '5');
        echo "✓ Select operation successful, found " . count($results) . " admin users<br>";
        
        if (!empty($results)) {
            echo "Sample admin user: " . $results[0]['username'] . " (" . $results[0]['email'] . ")<br>";
        }
        
    } catch (Exception $e) {
        echo "✗ Select test failed: " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>Database Test Complete!</h2>";
    echo "<p>Your MySQL database is working correctly.</p>";
    
} catch (Exception $e) {
    echo "<h2>Database Test Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config/database.php</p>";
}
?>
