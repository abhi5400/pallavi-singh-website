<?php
/**
 * Quick Form Test - Test the join form submission
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Quick Form Test</h1>";

// Test data
$testData = [
    'full_name' => 'Abhijeet Jha',
    'age' => '25',
    'city' => 'Jaipur',
    'state' => 'Rajasthan',
    'contact_number' => '07891307864',
    'email' => 'abhijeetjha5400@gmail.com',
    'issue_challenge' => 'I am testing the form submission to ensure it works correctly.',
    'goals' => 'Testing goals field',
    'terms_accepted' => 'on',
    'newsletter_subscription' => 'on'
];

// Simulate POST request
$_POST = $testData;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_USER_AGENT'] = 'Test Browser';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

echo "<h2>Processing form with the same data...</h2>";

try {
    // Capture output
    ob_start();
    include 'process_join.php';
    $output = ob_get_clean();
    
    echo "<h3>Response:</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace;'>";
    echo htmlspecialchars($output);
    echo "</div>";
    
    // Parse JSON response
    $response = json_decode($output, true);
    if ($response) {
        if ($response['success']) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>✅ SUCCESS!</h3>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($response['message']) . "</p>";
            if (isset($response['form_id'])) {
                echo "<p><strong>Form ID:</strong> " . htmlspecialchars($response['form_id']) . "</p>";
                echo "<p><a href='" . htmlspecialchars($response['redirect_url']) . "' target='_blank'>View Thank You Page</a></p>";
            }
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>❌ FAILED!</h3>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars($response['message']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>⚠️ WARNING!</h3>";
        echo "<p>Could not parse JSON response</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>💥 ERROR!</h3>";
    echo "<p><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}

echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li><a href='index.html' target='_blank'>Test the actual form on the website</a></li>";
echo "<li><a href='admin/' target='_blank'>Check admin panel for submissions</a></li>";
echo "<li><a href='data/join_submissions.json' target='_blank'>View raw data</a></li>";
echo "</ul>";
?>
