<?php
// Simple PHP test
echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current directory: " . getcwd() . "<br>";

// Test database class
try {
    require_once 'config/database_json.php';
    echo "Database class loaded successfully<br>";
    
    $db = Database::getInstance();
    echo "Database instance created<br>";
    
    // Test data directory
    if (is_writable(DATA_DIR)) {
        echo "Data directory is writable<br>";
    } else {
        echo "Data directory is NOT writable<br>";
    }
    
    // Test file operations
    $testFile = DATA_DIR . 'test.json';
    $testData = ['test' => 'data', 'timestamp' => date('Y-m-d H:i:s')];
    
    if (file_put_contents($testFile, json_encode($testData))) {
        echo "File write test successful<br>";
        unlink($testFile); // Clean up
    } else {
        echo "File write test failed<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

// Test form processing
echo "<br>Testing form processing...<br>";

// Simulate POST data
$_POST = [
    'full_name' => 'Test User',
    'city' => 'Test City',
    'state' => 'Test State',
    'contact_number' => '1234567890',
    'email' => 'test@example.com',
    'issue_challenge' => 'This is a test challenge description',
    'terms_accepted' => 'on'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

try {
    // Capture output from process_join.php
    ob_start();
    include 'process_join.php';
    $output = ob_get_clean();
    
    echo "Form processing output:<br>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
} catch (Exception $e) {
    echo "Form processing error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}
?>
