<?php
/**
 * Debug script to test form processing
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Form Processing Debug</h2>";

// Test 1: Check if required files exist
echo "<h3>1. File Check:</h3>";
$files = [
    'config/database.php',
    'config/database_json.php',
    'process_join.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file} exists<br>";
    } else {
        echo "❌ {$file} missing<br>";
    }
}

// Test 2: Check database configuration
echo "<h3>2. Database Configuration:</h3>";
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "<br>";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "<br>";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'Not defined') . "<br>";
    echo "DB_PASS: " . (defined('DB_PASS') ? (DB_PASS ? '[SET]' : '[EMPTY]') : 'Not defined') . "<br>";
}

// Test 3: Test database connection
echo "<h3>3. Database Connection Test:</h3>";
try {
    $db = Database::getInstance();
    echo "✅ Database instance created successfully<br>";
    echo "Database type: " . $db->getDatabaseType() . "<br>";
    echo "Using JSON: " . ($db->isUsingJson() ? 'Yes' : 'No') . "<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 4: Check data directory
echo "<h3>4. Data Directory Check:</h3>";
$dataDir = __DIR__ . '/data/';
if (file_exists($dataDir)) {
    echo "✅ Data directory exists: {$dataDir}<br>";
    if (is_writable($dataDir)) {
        echo "✅ Data directory is writable<br>";
    } else {
        echo "❌ Data directory is not writable<br>";
    }
} else {
    echo "❌ Data directory does not exist: {$dataDir}<br>";
    echo "Attempting to create...<br>";
    if (mkdir($dataDir, 0755, true)) {
        echo "✅ Data directory created successfully<br>";
    } else {
        echo "❌ Failed to create data directory<br>";
    }
}

// Test 5: Check JSON database files
echo "<h3>5. JSON Database Files:</h3>";
$jsonFiles = [
    'data/join_submissions.json',
    'data/newsletter_subscriptions.json',
    'data/email_log.json'
];

foreach ($jsonFiles as $file) {
    if (file_exists($file)) {
        echo "✅ {$file} exists<br>";
        $size = filesize($file);
        echo "&nbsp;&nbsp;&nbsp;Size: {$size} bytes<br>";
    } else {
        echo "⚠️ {$file} does not exist (will be created when needed)<br>";
    }
}

// Test 6: Simulate form data
echo "<h3>6. Form Data Simulation:</h3>";
$testData = [
    'full_name' => 'Test User',
    'age' => '25',
    'city' => 'Test City',
    'state' => 'Test State',
    'contact_number' => '1234567890',
    'email' => 'test@example.com',
    'issue_challenge' => 'Test challenge',
    'goals' => 'Test goals',
    'terms_accepted' => 'on',
    'newsletter_subscription' => 'on'
];

echo "Test data prepared:<br>";
foreach ($testData as $key => $value) {
    echo "&nbsp;&nbsp;&nbsp;{$key}: {$value}<br>";
}

// Test 7: Test database insert
echo "<h3>7. Database Insert Test:</h3>";
try {
    $db = Database::getInstance();
    
    $testInsertData = [
        'form_id' => 'TEST-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
        'full_name' => 'Test User',
        'age' => 25,
        'city' => 'Test City',
        'state' => 'Test State',
        'contact_number' => '1234567890',
        'email' => 'test@example.com',
        'issue_challenge' => 'Test challenge',
        'goals' => 'Test goals',
        'terms_accepted' => 1,
        'newsletter_subscription' => 1,
        'submission_date' => date('Y-m-d H:i:s'),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Agent',
        'status' => 'test'
    ];
    
    $insertId = $db->insert('join_submissions', $testInsertData);
    if ($insertId) {
        echo "✅ Test insert successful! ID: {$insertId}<br>";
    } else {
        echo "❌ Test insert failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Test insert error: " . $e->getMessage() . "<br>";
    echo "Error details: " . $e->getFile() . " line " . $e->getLine() . "<br>";
}

echo "<hr>";
echo "<p><strong>Debug completed!</strong> Check the results above to identify any issues.</p>";
?>

