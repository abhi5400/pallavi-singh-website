<?php
/**
 * Database Setup Script
 * Run this script to set up the database and initial data
 */

// Database configuration - UPDATE THESE VALUES
$db_host = 'localhost';
$db_name = 'pallavi_coaching_db';
$db_user = 'root'; // Change this to your database username
$db_pass = ''; // Change this to your database password

echo "<h2>Pallavi Singh Coaching - Database Setup</h2>";

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✓ Connected to MySQL server</p>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Database '$db_name' created or already exists</p>";
    
    // Use the database
    $pdo->exec("USE `$db_name`");
    
    // Read and execute SQL schema
    $sql = file_get_contents('database_schema.sql');
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<p>✓ Database schema created successfully</p>";
    
    // Test database connection
    $testQuery = $pdo->query("SELECT COUNT(*) FROM contact_submissions");
    echo "<p>✓ Database tables created and accessible</p>";
    
    // Update config file with database credentials
    $configContent = file_get_contents('config/database.php');
    
    // Replace database credentials in config file
    $configContent = preg_replace("/define\('DB_HOST', '[^']*'\);/", "define('DB_HOST', '$db_host');", $configContent);
    $configContent = preg_replace("/define\('DB_NAME', '[^']*'\);/", "define('DB_NAME', '$db_name');", $configContent);
    $configContent = preg_replace("/define\('DB_USER', '[^']*'\);/", "define('DB_USER', '$db_user');", $configContent);
    $configContent = preg_replace("/define\('DB_PASS', '[^']*'\);/", "define('DB_PASS', '$db_pass');", $configContent);
    
    file_put_contents('config/database.php', $configContent);
    echo "<p>✓ Configuration file updated</p>";
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>Your database has been set up successfully. Here's what you need to do next:</p>";
    echo "<ul>";
    echo "<li><strong>Update Email Settings:</strong> Edit config/database.php and update the SMTP settings with your email credentials</li>";
    echo "<li><strong>Change Admin Password:</strong> The default admin login is username: 'admin', password: 'admin123' - CHANGE THIS IMMEDIATELY!</li>";
    echo "<li><strong>Update Site URL:</strong> Change the SITE_URL constant in config/database.php to your actual domain</li>";
    echo "<li><strong>Test Forms:</strong> Test all forms on your website to ensure they're working correctly</li>";
    echo "<li><strong>Access Admin Panel:</strong> Visit /admin/ to manage form submissions</li>";
    echo "</ul>";
    
    echo "<h3>Security Recommendations:</h3>";
    echo "<ul>";
    echo "<li>Change the default admin password immediately</li>";
    echo "<li>Use strong, unique passwords for database access</li>";
    echo "<li>Keep your PHP and database software updated</li>";
    echo "<li>Consider using HTTPS for your website</li>";
    echo "<li>Regularly backup your database</li>";
    echo "</ul>";
    
    echo "<p><strong>Admin Panel:</strong> <a href='admin/'>Access Admin Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database setup failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database credentials and try again.</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Setup failed: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #1A535C; }
p { line-height: 1.6; }
ul { line-height: 1.8; }
a { color: #1A535C; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>

