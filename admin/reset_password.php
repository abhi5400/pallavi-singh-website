<?php
/**
 * Password Reset Script - Creates/Resets Admin Password
 * Run this once to set up admin credentials
 */

require_once '../config/database_json.php';

// Default admin credentials
$username = 'admin';
$password = 'admin123'; // Change this to your desired password
$email = 'admin@pallavi-coaching.com';
$fullName = 'Pallavi Singh';

// Hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Create admin user data
$adminUser = [
    'username' => $username,
    'email' => $email,
    'password_hash' => $passwordHash,
    'full_name' => $fullName,
    'role' => 'admin',
    'is_active' => true,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

// Get existing users
$jsonDb = JsonDatabase::getInstance();
$users = $jsonDb->getData('admin_users');

// Check if admin user exists
$existingUser = null;
$userIndex = -1;
foreach ($users as $index => $user) {
    if ($user['username'] === $username) {
        $existingUser = $user;
        $userIndex = $index;
        break;
    }
}

if ($existingUser) {
    // Update existing user
    $adminUser['id'] = $existingUser['id'];
    $adminUser['last_login'] = $existingUser['last_login'] ?? null;
    $users[$userIndex] = $adminUser;
    echo "<h2>✅ Admin user updated!</h2>";
} else {
    // Add new user
    $adminUser['id'] = count($users) + 1;
    $users[] = $adminUser;
    echo "<h2>✅ Admin user created!</h2>";
}

// Save to file
$result = $jsonDb->saveData('admin_users', $users);

if ($result) {
    echo "<p><strong>Username:</strong> $username</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Full Name:</strong> $fullName</p>";
    echo "<hr>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
} else {
    echo "<p style='color: red;'>❌ Failed to save admin user. Check file permissions.</p>";
}
?>

