<?php
/**
 * Admin Login Page - Pallavi Singh Coaching
 * Separate login page for better organization
 */

require_once '../config/database.php';

// Simple authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $db = Database::getInstance();
        
        $users = $db->where('admin_users', ['username' => $username, 'is_active' => true]);
        $user = !empty($users) ? $users[0] : null;
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $user;
            
            // Update last login
            $db->update('admin_users', $user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            
            // Redirect to dashboard
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } catch (Exception $e) {
        $error = "Login failed: " . $e->getMessage();
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
            <h2>🔐 Admin Login</h2>
            <p>Access your coaching dashboard</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">👤 Username:</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">🔒 Password:</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit">🚀 Login to Dashboard</button>
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
