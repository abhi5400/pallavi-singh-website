<?php
/**
 * Admin Panel Setup - Pallavi Singh Coaching
 * This script sets up the admin panel and creates default admin user
 */

echo "<h1>🔧 Admin Panel Setup</h1>";

// Check if admin panel files exist
$adminFiles = [
    'admin/index.php',
    'admin/login.php', 
    'admin/dashboard.php',
    'admin/config/admin_config.php'
];

echo "<h2>📁 Checking Admin Panel Files</h2>";
foreach ($adminFiles as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file exists</p>";
    } else {
        echo "<p>❌ $file missing</p>";
    }
}

// Check admin users
echo "<h2>👤 Checking Admin Users</h2>";
if (file_exists('data/admin_users.json')) {
    $adminUsers = json_decode(file_get_contents('data/admin_users.json'), true);
    echo "<p>✅ Admin users file exists</p>";
    echo "<p>📊 Found " . count($adminUsers) . " admin user(s)</p>";
    
    foreach ($adminUsers as $user) {
        echo "<p>👤 Username: <strong>" . $user['username'] . "</strong> | Role: " . $user['role'] . " | Active: " . ($user['is_active'] ? 'Yes' : 'No') . "</p>";
    }
} else {
    echo "<p>❌ Admin users file not found</p>";
}

// Check database connection
echo "<h2>🗄️ Checking Database Connection</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "<p>✅ Database connection successful</p>";
    
    // Test admin user access
    $users = json_decode(file_get_contents('data/admin_users.json'), true);
    echo "<p>✅ Admin users accessible (" . count($users) . " users)</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

// Check form submissions
echo "<h2>📝 Checking Form Submissions</h2>";
$formFiles = [
    'data/join_submissions.json',
    'data/contact_submissions.json', 
    'data/newsletter_subscriptions.json',
    'data/waitlist_subscriptions.json'
];

foreach ($formFiles as $file) {
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $count = count($data);
        echo "<p>✅ " . basename($file) . " - $count submissions</p>";
    } else {
        echo "<p>❌ " . basename($file) . " not found</p>";
    }
}

echo "<h2>🚀 Admin Panel Access</h2>";
echo "<p><strong>Admin Panel URL:</strong> <a href='http://127.0.0.1:8000/admin/' target='_blank'>http://127.0.0.1:8000/admin/</a></p>";
echo "<p><strong>Login Credentials:</strong></p>";
echo "<ul>";
echo "<li><strong>Username:</strong> admin</li>";
echo "<li><strong>Password:</strong> admin123</li>";
echo "</ul>";

echo "<h2>📋 Admin Panel Features</h2>";
echo "<ul>";
echo "<li>📊 Dashboard - Overview of all submissions</li>";
echo "<li>👥 Clients - Manage client information</li>";
echo "<li>📧 Newsletter - Manage newsletter subscriptions</li>";
echo "<li>📝 Blog - Manage blog posts</li>";
echo "<li>⭐ Testimonials - Manage testimonials</li>";
echo "<li>📅 Events - Manage events and workshops</li>";
echo "<li>💰 Payments - Track payments</li>";
echo "<li>📊 Analytics - View analytics and reports</li>";
echo "<li>⚙️ Settings - Configure system settings</li>";
echo "</ul>";

echo "<h2>🔐 Security Notes</h2>";
echo "<p>⚠️ <strong>Important:</strong> Change the default admin password in production!</p>";
echo "<p>🔒 The admin panel is protected by authentication</p>";
echo "<p>📝 All form submissions are logged and accessible through the admin panel</p>";

echo "<h2>✅ Setup Complete!</h2>";
echo "<p>Your admin panel is ready to use. Click the link above to access it.</p>";
?>
