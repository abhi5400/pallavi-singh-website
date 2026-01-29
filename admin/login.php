<?php
/**
 * Admin Login Page - Pallavi Singh Coaching
 * Separate login page for better organization
 */

require_once '../config/database.php';

// Start session with proper settings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $db = Database::getInstance();
        
        // Get all users for debugging
        $allUsers = $db->where('admin_users', []);
        
        // Find user by username
        $users = $db->where('admin_users', ['username' => $username]);
        $user = !empty($users) ? $users[0] : null;
        
        // Debug: Check if user exists
        if (!$user) {
            $error = "User not found. Username: " . htmlspecialchars($username);
        } elseif (isset($user['is_active']) && !$user['is_active']) {
            $error = "User account is inactive";
        } elseif (!isset($user['password_hash']) || empty($user['password_hash'])) {
            $error = "User account error: password hash missing";
        } else {
            // Verify password
            $passwordValid = password_verify($password, $user['password_hash']);
            
            if (!$passwordValid) {
                $error = "Invalid password. Please check your password.";
            } else {
                // Login successful - set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = $user;
                
                // Save session immediately
                session_write_close();
                session_start(); // Reopen for potential updates
                
                // Update last login (don't let this block login)
                try {
                    $db->update('admin_users', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
                } catch (Exception $e) {
                    error_log("Failed to update last login: " . $e->getMessage());
                }
                
                // Final session save and redirect
                session_write_close();
                header('Location: dashboard.php');
                exit;
            }
        }
    } catch (Exception $e) {
        $error = "Login failed: " . $e->getMessage();
        error_log("Login error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Pallavi Singh Coaching</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container fade-in">
        <div class="login-header">
            <h2>Admin Login</h2>
            <p>Access your coaching dashboard</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit">Sign In</button>
        </form>
        
        <div class="login-footer">
            <p>Pallavi Singh Coaching Admin Panel</p>
        </div>
    </div>
    
    <script>
        // Add some interactive features to login page
        document.addEventListener('DOMContentLoaded', function() {
            // Focus on username field
            const usernameField = document.getElementById('username');
            if (usernameField && !usernameField.value) {
                usernameField.focus();
            }
            
            // Add enter key support
            document.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const form = document.querySelector('form');
                    if (form) {
                        form.submit();
                    }
                }
            });
        });
    </script>
</body>
</html>
