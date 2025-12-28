<?php
/**
 * Debug Login - Shows what's happening during login
 */

require_once '../config/database.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Login Debug Information</h2>";

// Check session
echo "<h3>Session Status:</h3>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "\n";
echo "Logged In: " . (isset($_SESSION['admin_logged_in']) ? 'Yes' : 'No') . "\n";
if (isset($_SESSION['admin_user'])) {
    echo "User: " . print_r($_SESSION['admin_user'], true) . "\n";
}
echo "</pre>";

// Check database
echo "<h3>Database Status:</h3>";
try {
    $db = Database::getInstance();
    
    // Check if using JSON or MySQL
    $reflection = new ReflectionClass($db);
    $property = $reflection->getProperty('useJson');
    $property->setAccessible(true);
    $useJson = $property->getValue($db);
    
    if ($useJson) {
        echo "<p style='color: blue;'>📁 Using JSON Database</p>";
        // Use JSON database directly
        require_once '../config/database_json.php';
        $jsonDb = JsonDatabase::getInstance();
        $users = $jsonDb->getData('admin_users');
    } else {
        echo "<p style='color: green;'>✅ Using MySQL Database</p>";
        // Use MySQL database
        $users = $db->where('admin_users', []);
    }
    
    echo "<p>Found " . count($users) . " admin user(s)</p>";
    
    if (!empty($users)) {
        echo "<h4>Admin Users:</h4>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Username</th><th>Email</th><th>Active</th><th>Has Password Hash</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . ($user['id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['username'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
            echo "<td>" . (isset($user['is_active']) && $user['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . (isset($user['password_hash']) && !empty($user['password_hash']) ? 'Yes (' . strlen($user['password_hash']) . ' chars)' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test password
        if (isset($_GET['test_password'])) {
            $testUser = $users[0];
            $testPassword = $_GET['test_password'];
            echo "<h4>Password Test Results:</h4>";
            echo "<p><strong>Testing password:</strong> " . htmlspecialchars($testPassword) . "</p>";
            echo "<p><strong>Username:</strong> " . htmlspecialchars($testUser['username']) . "</p>";
            echo "<p><strong>Hash (first 50 chars):</strong> " . htmlspecialchars(substr($testUser['password_hash'], 0, 50)) . "...</p>";
            
            if (password_verify($testPassword, $testUser['password_hash'])) {
                echo "<p style='color: green; font-weight: bold; font-size: 1.2em;'>✅ Password VERIFIED! Login should work.</p>";
            } else {
                echo "<p style='color: red; font-weight: bold; font-size: 1.2em;'>❌ Password does NOT match!</p>";
                echo "<p>Creating a new hash for 'admin123'...</p>";
                $newHash = password_hash('admin123', PASSWORD_DEFAULT);
                echo "<p>New hash created: <code style='word-break: break-all;'>" . $newHash . "</code></p>";
                
                // Update the hash
                if ($useJson) {
                    $jsonDb->update('admin_users', $testUser['id'], ['password_hash' => $newHash]);
                } else {
                    $db->update('admin_users', ['password_hash' => $newHash], 'id = ?', [$testUser['id']]);
                }
                echo "<p style='color: green; font-weight: bold;'>✅ Password hash updated in database!</p>";
                echo "<p style='color: blue;'>🔄 <strong>Please refresh this page to test again.</strong></p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ No admin users found!</p>";
        echo "<p><a href='reset_password.php' style='background: #1A535C; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Create Admin User</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<h3>Test Password:</h3>";
echo "<form method='GET'>";
echo "<input type='text' name='test_password' placeholder='Enter password to test' value='admin123'>";
echo "<button type='submit'>Test Password</button>";
echo "</form>";

echo "<hr>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>

