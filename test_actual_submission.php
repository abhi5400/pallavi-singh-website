<?php
/**
 * Test Actual Form Submission with Real Data
 */

// Simulate the exact form data from the screenshot
$_POST = [
    'full_name' => 'Abhijeet Jha',
    'age' => '',
    'city' => 'Jaipur',
    'state' => 'Rajasthan',
    'contact_number' => '07891307864',
    'email' => 'abhijeetjha5400@gmail.com',
    'issue_challenge' => 'asdfghjklkjhgfdsaASDFGHJKL',
    'goals' => '',
    'terms_accepted' => 'on',
    'newsletter_subscription' => ''
];

echo "<h2>Testing Form Submission with Real Data</h2>";
echo "<p>Simulating form submission with the exact data from the screenshot...</p>";

// Capture output
ob_start();

try {
    // Include the actual form processor
    include 'process_join.php';
} catch (Exception $e) {
    echo "<p>❌ Error including process_join.php: " . $e->getMessage() . "</p>";
}

$output = ob_get_clean();

echo "<h3>Form Processor Output:</h3>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Try to parse the JSON response
$jsonData = json_decode($output, true);
if ($jsonData) {
    echo "<h3>Parsed Response:</h3>";
    echo "<pre>" . print_r($jsonData, true) . "</pre>";
    
    if (isset($jsonData['success']) && $jsonData['success']) {
        echo "<p style='color: green;'>✅ Form submission would be successful!</p>";
    } else {
        echo "<p style='color: red;'>❌ Form submission would fail: " . ($jsonData['message'] ?? 'Unknown error') . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Invalid JSON response from form processor</p>";
    echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
}

// Test database directly
echo "<h3>Direct Database Test:</h3>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    
    $testData = [
        'form_id' => 'TEST-' . date('YmdHis'),
        'full_name' => 'Abhijeet Jha',
        'age' => null,
        'city' => 'Jaipur',
        'state' => 'Rajasthan',
        'contact_number' => '07891307864',
        'email' => 'abhijeetjha5400@gmail.com',
        'issue_challenge' => 'asdfghjklkjhgfdsaASDFGHJKL',
        'goals' => '',
        'terms_accepted' => true,
        'newsletter_subscription' => false,
        'submission_date' => date('Y-m-d H:i:s'),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Agent',
        'status' => 'new'
    ];
    
    $result = $db->insert('join_submissions', $testData);
    if ($result) {
        echo "<p style='color: green;'>✅ Direct database insert successful (ID: $result)</p>";
    } else {
        echo "<p style='color: red;'>❌ Direct database insert failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}
?>



