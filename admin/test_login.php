<?php
/**
 * Test Login - Verify password hash
 */

$hash = '$2y$12$vom31xY.ODU4f147er38VeZDIoTHJWGGPzBgqupwFDO5cIq3DP4Jq';
$password = 'admin123';

echo "<h2>Password Verification Test</h2>";
echo "<p><strong>Testing password:</strong> admin123</p>";
echo "<p><strong>Hash:</strong> $hash</p>";

if (password_verify($password, $hash)) {
    echo "<p style='color: green;'>✅ Password matches!</p>";
} else {
    echo "<p style='color: red;'>❌ Password does NOT match!</p>";
    echo "<p>Let's create a new password hash...</p>";
    
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    echo "<p><strong>New hash:</strong> $newHash</p>";
    
    // Update the admin_users.json file
    require_once '../config/database_json.php';
    $jsonDb = JsonDatabase::getInstance();
    $users = $jsonDb->getData('admin_users');
    
    foreach ($users as &$user) {
        if ($user['username'] === 'admin') {
            $user['password_hash'] = $newHash;
            $user['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    
    $result = $jsonDb->saveData('admin_users', $users);
    if ($result) {
        echo "<p style='color: green;'>✅ Password hash updated in database!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to update password hash.</p>";
    }
}

echo "<hr>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>

