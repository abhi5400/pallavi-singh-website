<?php
/**
 * Test Form Processing - Debug Form Submission Issues
 */

require_once 'config/database.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

echo "<h2>Form Processing Test</h2>";

try {
    // Test database connection
    $db = Database::getInstance();
    echo "<p>✅ Database connection successful</p>";
    
    // Test if we can get connection
    if ($db->getConnection()) {
        echo "<p>✅ Database connection object available</p>";
    } else {
        echo "<p>❌ Database connection object not available</p>";
    }
    
    // Test database operations
    echo "<h3>Testing Database Operations:</h3>";
    
    // Test contact submissions table
    try {
        $testData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'subject' => 'Test Subject',
            'message' => 'Test message for debugging',
            'submission_date' => date('Y-m-d H:i:s'),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'status' => 'new'
        ];
        
        $contactId = $db->insert('contact_submissions', $testData);
        if ($contactId) {
            echo "<p>✅ Contact form insertion successful (ID: {$contactId})</p>";
        } else {
            echo "<p>❌ Contact form insertion failed</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Contact form test failed: " . $e->getMessage() . "</p>";
    }
    
    // Test join submissions table
    try {
        $testJoinData = [
            'form_id' => 'TEST-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            'full_name' => 'Test User',
            'age' => 25,
            'city' => 'Test City',
            'state' => 'Test State',
            'contact_number' => '1234567890',
            'email' => 'test@example.com',
            'issue_challenge' => 'Test challenge',
            'goals' => 'Test goals',
            'terms_accepted' => true,
            'newsletter_subscription' => false,
            'submission_date' => date('Y-m-d H:i:s'),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'status' => 'new'
        ];
        
        $joinId = $db->insert('join_submissions', $testJoinData);
        if ($joinId) {
            echo "<p>✅ Join form insertion successful (ID: {$joinId})</p>";
        } else {
            echo "<p>❌ Join form insertion failed</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Join form test failed: " . $e->getMessage() . "</p>";
    }
    
    // Test waitlist subscriptions table
    try {
        $testWaitlistData = [
            'waitlist_id' => 'WAIT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'submission_date' => date('Y-m-d H:i:s'),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'status' => 'active'
        ];
        
        $waitlistId = $db->insert('waitlist_subscriptions', $testWaitlistData);
        if ($waitlistId) {
            echo "<p>✅ Waitlist form insertion successful (ID: {$waitlistId})</p>";
        } else {
            echo "<p>❌ Waitlist form insertion failed</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Waitlist form test failed: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database test failed: " . $e->getMessage() . "</p>";
    echo "<p>Error details: " . $e->getTraceAsString() . "</p>";
}

echo "<h3>Environment Information:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>PDO Available: " . (class_exists('PDO') ? 'Yes' : 'No') . "</p>";
echo "<p>MySQL PDO Driver: " . (in_array('mysql', PDO::getAvailableDrivers()) ? 'Available' : 'Not Available') . "</p>";
echo "<p>Current Directory: " . getcwd() . "</p>";
echo "<p>Config File Exists: " . (file_exists('config/database.php') ? 'Yes' : 'No') . "</p>";

?>
