<?php
/**
 * Test HTTP Form Submission - Simulate Real Browser Request
 */

echo "<h2>Testing HTTP Form Submission</h2>";

// Simulate a real HTTP POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_HOST'] = '127.0.0.1:5500';
$_SERVER['REQUEST_URI'] = '/process_join.php';

// Set the exact form data from the screenshot
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

echo "<p>Simulating HTTP POST request with form data...</p>";
echo "<pre>POST Data: " . print_r($_POST, true) . "</pre>";

// Capture the output
ob_start();

try {
    // Include the form processor
    include 'process_join.php';
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

$output = ob_get_clean();

echo "<h3>Form Processor Response:</h3>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Parse the JSON response
$response = json_decode($output, true);
if ($response) {
    echo "<h3>Parsed Response:</h3>";
    echo "<pre>" . print_r($response, true) . "</pre>";
    
    if (isset($response['success'])) {
        if ($response['success']) {
            echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✅ SUCCESS: " . $response['message'] . "</p>";
        } else {
            echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ FAILED: " . $response['message'] . "</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ Invalid JSON response</p>";
    echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
}

// Test if the data was actually saved
echo "<h3>Checking if data was saved:</h3>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    
    // Get the latest submission
    $data = $db->getData('join_submissions');
    $latest = end($data);
    
    if ($latest && $latest['full_name'] === 'Abhijeet Jha') {
        echo "<p style='color: green;'>✅ Data was saved successfully!</p>";
        echo "<p>Latest submission: " . $latest['full_name'] . " (" . $latest['email'] . ")</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Data may not have been saved or different data found</p>";
        if ($latest) {
            echo "<p>Latest submission: " . $latest['full_name'] . " (" . $latest['email'] . ")</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking saved data: " . $e->getMessage() . "</p>";
}
?>



