<?php
/**
 * Debug script to test contact form processing
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Contact Form Debug Test</h1>";

// Test 1: Check if PHP is working
echo "<h2>✓ Test 1: PHP is working</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 2: Check if config files exist
echo "<h2>Test 2: Check Configuration Files</h2>";
$configFile = __DIR__ . '/config/database.php';
$jsonConfigFile = __DIR__ . '/config/database_json.php';

if (file_exists($configFile)) {
    echo "<p>✓ config/database.php exists</p>";
} else {
    echo "<p>✗ config/database.php NOT FOUND</p>";
}

if (file_exists($jsonConfigFile)) {
    echo "<p>✓ config/database_json.php exists</p>";
} else {
    echo "<p>✗ config/database_json.php NOT FOUND</p>";
}

// Test 3: Check data directory
echo "<h2>Test 3: Check Data Directory</h2>";
$dataDir = __DIR__ . '/data/';

if (file_exists($dataDir)) {
    echo "<p>✓ Data directory exists: " . $dataDir . "</p>";
    
    if (is_writable($dataDir)) {
        echo "<p>✓ Data directory is writable</p>";
    } else {
        echo "<p>✗ Data directory is NOT writable</p>";
    }
} else {
    echo "<p>✗ Data directory does NOT exist</p>";
    echo "<p>Attempting to create directory...</p>";
    if (mkdir($dataDir, 0755, true)) {
        echo "<p>✓ Data directory created successfully</p>";
    } else {
        echo "<p>✗ Failed to create data directory</p>";
    }
}

// Test 4: Load database class
echo "<h2>Test 4: Load Database Class</h2>";
try {
    require_once 'config/database.php';
    echo "<p>✓ Database class loaded successfully</p>";
    
    $db = Database::getInstance();
    echo "<p>✓ Database instance created</p>";
    echo "<p>Database type: " . $db->getDatabaseType() . "</p>";
    
} catch (Exception $e) {
    echo "<p>✗ Error loading database: " . $e->getMessage() . "</p>";
}

// Test 5: Test insert operation
echo "<h2>Test 5: Test Insert Operation</h2>";
try {
    $testData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'subject' => 'test',
        'message' => 'This is a test message',
        'submission_date' => date('Y-m-d H:i:s'),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Agent',
        'status' => 'new'
    ];
    
    $id = $db->insert('contact_submissions', $testData);
    
    if ($id) {
        echo "<p>✓ Test insert successful! ID: " . $id . "</p>";
    } else {
        echo "<p>✗ Test insert failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p>✗ Insert error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 6: Check contact_submissions.json
echo "<h2>Test 6: Check Contact Submissions File</h2>";
$contactFile = $dataDir . 'contact_submissions.json';

if (file_exists($contactFile)) {
    echo "<p>✓ contact_submissions.json exists</p>";
    
    $contents = file_get_contents($contactFile);
    $data = json_decode($contents, true);
    
    if ($data !== null) {
        echo "<p>✓ File contains valid JSON</p>";
        echo "<p>Number of records: " . count($data) . "</p>";
    } else {
        echo "<p>✗ File contains invalid JSON</p>";
    }
} else {
    echo "<p>⚠ contact_submissions.json does not exist yet (will be created on first submission)</p>";
}

// Test 7: Simulate form submission
echo "<h2>Test 7: Simulate Form Submission</h2>";
echo "<form method='POST' action='process_contact.php' id='testForm'>";
echo "<p>Name: <input type='text' name='name' value='Test User' required></p>";
echo "<p>Email: <input type='email' name='email' value='test@example.com' required></p>";
echo "<p>Phone: <input type='tel' name='phone' value='1234567890'></p>";
echo "<p>Subject: <select name='subject' required>";
echo "<option value='general-inquiry' selected>General Inquiry</option>";
echo "</select></p>";
echo "<p>Message: <textarea name='message' required>This is a test message from the debug script.</textarea></p>";
echo "<p><button type='submit'>Test Submit</button></p>";
echo "</form>";

echo "<h2>Server Information</h2>";
echo "<pre>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "PHP Extensions: " . implode(', ', get_loaded_extensions()) . "\n";
echo "</pre>";

?>
<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 {
        color: #333;
        border-bottom: 3px solid #4ECDC4;
        padding-bottom: 10px;
    }
    h2 {
        color: #1A535C;
        margin-top: 30px;
        border-left: 4px solid #4ECDC4;
        padding-left: 10px;
    }
    p {
        background: white;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
    }
    pre {
        background: white;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto;
    }
    form {
        background: white;
        padding: 20px;
        border-radius: 5px;
    }
    input, textarea, select {
        width: 100%;
        padding: 8px;
        margin: 5px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    button {
        background: #4ECDC4;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover {
        background: #3bb3ab;
    }
</style>

