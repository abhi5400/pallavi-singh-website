<?php
/**
 * Admin Settings - Pallavi Singh Coaching
 * System settings and configuration management
 */

require_once '../config/database_json.php';

// Check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle form submissions
if ($_POST) {
    try {
        $db = Database::getInstance();
        
        if (isset($_POST['update_profile'])) {
            // Update admin profile
            $userId = $_SESSION['admin_user']['id'];
            $updateData = [
                'full_name' => $_POST['full_name'],
                'email' => $_POST['email']
            ];
            
            if (!empty($_POST['new_password'])) {
                $updateData['password_hash'] = password_hash($_POST['new_password'], PASSWORD_BCRYPT, ['cost' => 12]);
            }
            
            $db->update('admin_users', $userId, $updateData);
            $_SESSION['admin_user'] = array_merge($_SESSION['admin_user'], $updateData);
            $success = "Profile updated successfully!";
        }
        
        if (isset($_POST['update_settings'])) {
            // Update system settings (stored in a simple JSON file)
            $settings = [
                'site_name' => $_POST['site_name'],
                'admin_email' => $_POST['admin_email'],
                'notification_email' => $_POST['notification_email'],
                'auto_response' => isset($_POST['auto_response']),
                'email_notifications' => isset($_POST['email_notifications']),
                'backup_frequency' => $_POST['backup_frequency'],
                'timezone' => $_POST['timezone']
            ];
            
            file_put_contents(__DIR__ . '/../data/settings.json', json_encode($settings, JSON_PRETTY_PRINT));
            $success = "Settings updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Load current settings
$settingsFile = __DIR__ . '/../data/settings.json';
$settings = [];
if (file_exists($settingsFile)) {
    $settings = json_decode(file_get_contents($settingsFile), true) ?: [];
}

// Default settings
$settings = array_merge([
    'site_name' => 'Pallavi Singh Coaching',
    'admin_email' => 'admin@pallavi-coaching.com',
    'notification_email' => 'notifications@pallavi-coaching.com',
    'auto_response' => true,
    'email_notifications' => true,
    'backup_frequency' => 'daily',
    'timezone' => 'Asia/Kolkata'
], $settings);

// Set page variables
$pageTitle = 'Settings';
$pageSubtitle = 'Manage system settings and your profile';

// Create page content
ob_start();
?>

<?php if ($success): ?>
    <div class="success-message fade-in">
        ✅ <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error-message fade-in">
        ⚠️ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="settings-container">
    <!-- Profile Settings -->
    <div class="settings-section fade-in">
        <div class="section-header">
            <h3>👤 Profile Settings</h3>
            <p>Update your personal information and password</p>
        </div>
        
        <form method="POST" class="settings-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" 
                           value="<?php echo htmlspecialchars($_SESSION['admin_user']['full_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($_SESSION['admin_user']['email']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" 
                           placeholder="Leave blank to keep current password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           placeholder="Confirm new password">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="update_profile" class="btn btn-primary">
                    💾 Update Profile
                </button>
            </div>
        </form>
    </div>
    
    <!-- System Settings -->
    <div class="settings-section fade-in">
        <div class="section-header">
            <h3>⚙️ System Settings</h3>
            <p>Configure system-wide settings and preferences</p>
        </div>
        
        <form method="POST" class="settings-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="site_name">Site Name:</label>
                    <input type="text" id="site_name" name="site_name" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="timezone">Timezone:</label>
                    <select id="timezone" name="timezone" class="form-control">
                        <option value="Asia/Kolkata" <?php echo $settings['timezone'] === 'Asia/Kolkata' ? 'selected' : ''; ?>>Asia/Kolkata (IST)</option>
                        <option value="UTC" <?php echo $settings['timezone'] === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                        <option value="America/New_York" <?php echo $settings['timezone'] === 'America/New_York' ? 'selected' : ''; ?>>America/New_York (EST)</option>
                        <option value="Europe/London" <?php echo $settings['timezone'] === 'Europe/London' ? 'selected' : ''; ?>>Europe/London (GMT)</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="admin_email">Admin Email:</label>
                    <input type="email" id="admin_email" name="admin_email" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="notification_email">Notification Email:</label>
                    <input type="email" id="notification_email" name="notification_email" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['notification_email']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="backup_frequency">Backup Frequency:</label>
                    <select id="backup_frequency" name="backup_frequency" class="form-control">
                        <option value="daily" <?php echo $settings['backup_frequency'] === 'daily' ? 'selected' : ''; ?>>Daily</option>
                        <option value="weekly" <?php echo $settings['backup_frequency'] === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                        <option value="monthly" <?php echo $settings['backup_frequency'] === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Email Settings:</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="auto_response" <?php echo $settings['auto_response'] ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Enable Auto-Response Emails
                        </label>
                        
                        <label class="checkbox-label">
                            <input type="checkbox" name="email_notifications" <?php echo $settings['email_notifications'] ? 'checked' : ''; ?>>
                            <span class="checkmark"></span>
                            Email Notifications for New Submissions
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="update_settings" class="btn btn-primary">
                    💾 Save Settings
                </button>
            </div>
        </form>
    </div>
    
    <!-- System Information -->
    <div class="settings-section fade-in">
        <div class="section-header">
            <h3>📊 System Information</h3>
            <p>Current system status and information</p>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <div class="info-icon">🐘</div>
                <div class="info-content">
                    <div class="info-label">PHP Version</div>
                    <div class="info-value"><?php echo PHP_VERSION; ?></div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon">💾</div>
                <div class="info-content">
                    <div class="info-label">Database Type</div>
                    <div class="info-value">JSON File System</div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon">📁</div>
                <div class="info-content">
                    <div class="info-label">Data Directory</div>
                    <div class="info-value"><?php echo is_writable(__DIR__ . '/../data/') ? 'Writable' : 'Read Only'; ?></div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-icon">🕒</div>
                <div class="info-content">
                    <div class="info-label">Server Time</div>
                    <div class="info-value"><?php echo date('Y-m-d H:i:s'); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Management -->
    <div class="settings-section fade-in">
        <div class="section-header">
            <h3>🗄️ Data Management</h3>
            <p>Manage your data and perform maintenance tasks</p>
        </div>
        
        <div class="data-actions">
            <button class="btn btn-secondary" onclick="exportAllData()">
                📊 Export All Data
            </button>
            
            <button class="btn btn-secondary" onclick="clearOldData()">
                🗑️ Clear Old Data
            </button>
            
            <button class="btn btn-warning" onclick="backupData()">
                💾 Create Backup
            </button>
            
            <button class="btn btn-danger" onclick="resetSystem()">
                ⚠️ Reset System
            </button>
        </div>
    </div>
</div>

<style>
/* Settings Page Specific Styles */
.settings-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.settings-section {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.section-header {
    padding: 25px 30px;
    border-bottom: 1px solid #f0f0f0;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

.section-header h3 {
    color: #1A535C;
    margin: 0 0 8px 0;
    font-size: 1.4em;
}

.section-header p {
    color: #666;
    margin: 0;
    font-size: 0.95em;
}

.settings-form {
    padding: 30px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 0.95em;
}

.form-control {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95em;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #1A535C;
    box-shadow: 0 0 0 3px rgba(26, 83, 92, 0.1);
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    color: #333;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.2);
}

.form-actions {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid #f0f0f0;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95em;
}

.btn-primary {
    background: #1A535C;
    color: white;
}

.btn-primary:hover {
    background: #0d3a40;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    transform: translateY(-2px);
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background: #e0a800;
    transform: translateY(-2px);
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 30px;
}

.info-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #1A535C;
}

.info-icon {
    font-size: 2em;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.info-content {
    flex: 1;
}

.info-label {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 3px;
}

.info-value {
    color: #1A535C;
    font-weight: 700;
    font-size: 1.1em;
}

.data-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    padding: 30px;
}

.success-message {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #4caf50;
    font-weight: 500;
}

.error-message {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #f44336;
    font-weight: 500;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .data-actions {
        grid-template-columns: 1fr;
    }
    
    .settings-form {
        padding: 20px;
    }
}
</style>

<script>
// Settings page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeFormValidation();
});

function initializeFormValidation() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (newPassword && confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });
    }
}

function exportAllData() {
    if (confirm('This will export all data to CSV files. Continue?')) {
        // Create multiple CSV files for different data types
        const dataTypes = ['contact_submissions', 'booking_submissions', 'journey_submissions', 'newsletter_subscriptions'];
        
        dataTypes.forEach(type => {
            fetch(`export.php?type=${type}`)
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${type}_export.csv`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                });
        });
        
        showNotification('Data export started. Files will download automatically.', 'success');
    }
}

function clearOldData() {
    if (confirm('This will delete data older than 1 year. This action cannot be undone. Continue?')) {
        fetch('maintenance.php?action=clear_old_data', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Old data cleared successfully.', 'success');
            } else {
                showNotification('Error clearing old data: ' + data.message, 'error');
            }
        });
    }
}

function backupData() {
    if (confirm('Create a backup of all current data?')) {
        fetch('maintenance.php?action=backup', {
            method: 'POST'
        })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `backup_${new Date().toISOString().split('T')[0]}.zip`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        });
        
        showNotification('Backup created successfully.', 'success');
    }
}

function resetSystem() {
    if (confirm('⚠️ WARNING: This will reset the entire system and delete ALL data. This action cannot be undone. Are you absolutely sure?')) {
        const confirmation = prompt('Type "RESET" to confirm system reset:');
        if (confirmation === 'RESET') {
            fetch('maintenance.php?action=reset', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('System reset completed. Redirecting to setup...', 'success');
                    setTimeout(() => {
                        window.location.href = 'setup.php';
                    }, 2000);
                } else {
                    showNotification('Error resetting system: ' + data.message, 'error');
                }
            });
        }
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    const bgColor = type === 'error' ? '#ff6b6b' : type === 'success' ? '#4ECDC4' : '#1A535C';
    
    notification.innerHTML = `
        <div style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${bgColor};
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        ">
            ${message}
        </div>
        <style>
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        </style>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}
</script>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>
