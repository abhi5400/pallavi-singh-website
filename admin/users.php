<?php
/**
 * Users Management - Pallavi Singh Coaching
 * Manage admin users and permissions
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
        
        if (isset($_POST['add_user'])) {
            $userData = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'full_name' => $_POST['full_name'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'role' => $_POST['role'],
                'status' => $_POST['status'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->insert('admin_users', $userData);
            $success = "User added successfully!";
        }
        
        if (isset($_POST['update_user'])) {
            $userId = $_POST['user_id'];
            $updateData = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'full_name' => $_POST['full_name'],
                'role' => $_POST['role'],
                'status' => $_POST['status']
            ];
            
            // Only update password if provided
            if (!empty($_POST['password'])) {
                $updateData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            $db->update('admin_users', $userId, $updateData);
            $success = "User updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get users data
try {
    $db = Database::getInstance();
    $allUsers = $db->getData('admin_users');
    
    // Sort by created_at descending
    usort($allUsers, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Users Management';
$pageSubtitle = 'Manage admin users and permissions';

// Create page content
ob_start();
?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="dashboard-grid">
    <!-- User Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($allUsers); ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allUsers, function($u) { return $u['role'] === 'admin'; })); ?></h3>
                <p>Administrators</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allUsers, function($u) { return $u['status'] === 'active'; })); ?></h3>
                <p>Active Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allUsers, function($u) { return $u['status'] === 'inactive'; })); ?></h3>
                <p>Inactive Users</p>
            </div>
        </div>
    </div>
    
    <!-- Add User -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-plus"></i> Add New User</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" class="form-control">
                            <option value="admin">Administrator</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="add_user" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add User
                </button>
            </form>
        </div>
    </div>
    
    <!-- Users List -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> All Users</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUsers as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $user['status']; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="editUser(<?php echo $user['id']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit User</h3>
            <span class="close" onclick="closeModal('editUserModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST" id="editUserForm">
                <input type="hidden" name="user_id" id="edit_user_id">
                
                <div class="form-group">
                    <label for="edit_username">Username</label>
                    <input type="text" id="edit_username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_full_name">Full Name</label>
                    <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">New Password (leave blank to keep current)</label>
                    <input type="password" id="edit_password" name="password" class="form-control">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_role">Role</label>
                        <select id="edit_role" name="role" class="form-control">
                            <option value="admin">Administrator</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
                    <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(id) {
    // This would fetch user data and populate the form
    document.getElementById('edit_user_id').value = id;
    document.getElementById('editUserModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>
