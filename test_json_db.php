<?php
/**
 * Test JSON Database - Debug Form Submission Issues
 */

require_once 'config/database.php';

echo "<h2>JSON Database Test</h2>";

try {
    $db = Database::getInstance();
    echo "<p>✅ Database instance created</p>";
    
    // Test inserting data
    $testData = [
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
    
    echo "<p>Test data prepared:</p>";
    echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";
    
    $result = $db->insert('join_submissions', $testData);
    
    if ($result) {
        echo "<p>✅ Data inserted successfully with ID: " . $result . "</p>";
    } else {
        echo "<p>❌ Data insertion failed</p>";
    }
    
    // Test reading data
    $allData = $db->getAll('join_submissions');
    echo "<p>✅ Retrieved " . count($allData) . " records from join_submissions</p>";
    
    // Check if the data directory is writable
    $dataDir = __DIR__ . '/data/';
    if (is_writable($dataDir)) {
        echo "<p>✅ Data directory is writable</p>";
    } else {
        echo "<p>❌ Data directory is not writable</p>";
    }
    
    // Check specific JSON file
    $joinFile = $dataDir . 'join_submissions.json';
    if (file_exists($joinFile)) {
        echo "<p>✅ join_submissions.json exists</p>";
        $content = file_get_contents($joinFile);
        $data = json_decode($content, true);
        echo "<p>✅ JSON file contains " . count($data) . " records</p>";
    } else {
        echo "<p>❌ join_submissions.json does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<h3>File Permissions:</h3>";
$dataDir = __DIR__ . '/data/';
$files = glob($dataDir . '*.json');
foreach ($files as $file) {
    $perms = fileperms($file);
    $readable = is_readable($file) ? 'Yes' : 'No';
    $writable = is_writable($file) ? 'Yes' : 'No';
    echo "<p>" . basename($file) . " - Readable: $readable, Writable: $writable</p>";
}
?>
