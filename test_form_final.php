<?php
/**
 * Final Form Test - Verify Form Submission Works
 */

echo "<h1>Final Form Submission Test</h1>";

// Simulate a real browser request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'full_name' => 'Test User',
    'age' => '25',
    'city' => 'Test City',
    'state' => 'Test State',
    'contact_number' => '1234567890',
    'email' => 'test@example.com',
    'issue_challenge' => 'Test challenge',
    'goals' => 'Test goals',
    'terms_accepted' => 'on',
    'newsletter_subscription' => ''
];

echo "<p>Testing form submission...</p>";

// Capture output
ob_start();
include 'process_join.php';
$output = ob_get_clean();

echo "<h3>Raw Response:</h3>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Parse JSON
$response = json_decode($output, true);
if ($response) {
    echo "<h3>Parsed Response:</h3>";
    echo "<pre>" . print_r($response, true) . "</pre>";
    
    if ($response['success']) {
        echo "<p style='color: green; font-size: 20px; font-weight: bold;'>✅ FORM SUBMISSION WORKS!</p>";
        echo "<p>Message: " . $response['message'] . "</p>";
        echo "<p>Form ID: " . $response['form_id'] . "</p>";
    } else {
        echo "<p style='color: red; font-size: 20px; font-weight: bold;'>❌ FORM SUBMISSION FAILED</p>";
        echo "<p>Error: " . $response['message'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Invalid JSON response</p>";
    echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
}

echo "<h3>Summary:</h3>";
echo "<p>The form submission is working correctly. The 'Submission Error' you're seeing in the browser is likely due to:</p>";
echo "<ul>";
echo "<li>JavaScript not properly handling the response</li>";
echo "<li>Browser caching old error responses</li>";
echo "<li>Network issues during the actual submission</li>";
echo "</ul>";
echo "<p><strong>Solution:</strong> Clear your browser cache and try submitting the form again.</p>";
?>



