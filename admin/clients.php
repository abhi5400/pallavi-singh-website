<?php
/**
 * Clients Management - Pallavi Singh Coaching
 * Manage client information and sessions
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
        
        if (isset($_POST['add_client'])) {
            $clientData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'status' => $_POST['status'],
                'notes' => $_POST['notes'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->insert('clients', $clientData);
            $success = "Client added successfully!";
        }
        
        if (isset($_POST['update_client'])) {
            $clientId = $_POST['client_id'];
            $updateData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'status' => $_POST['status'],
                'notes' => $_POST['notes']
            ];
            
            $db->update('clients', $clientId, $updateData);
            $success = "Client updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get clients data
try {
    $db = Database::getInstance();
    $allClients = $db->getData('clients');
    
    // Sort by created_at descending
    usort($allClients, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Clients Management';
$pageSubtitle = 'Manage client information and relationships';

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
    <!-- Client Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($allClients); ?></h3>
                <p>Total Clients</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allClients, function($c) { return $c['status'] === 'active'; })); ?></h3>
                <p>Active Clients</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allClients, function($c) { return $c['status'] === 'prospect'; })); ?></h3>
                <p>Prospects</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allClients, function($c) { return $c['status'] === 'inactive'; })); ?></h3>
                <p>Inactive Clients</p>
            </div>
        </div>
    </div>
    
    <!-- Add Client -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-plus"></i> Add New Client</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="prospect">Prospect</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" name="add_client" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add Client
                </button>
            </form>
        </div>
    </div>
    
    <!-- Clients List -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> All Clients</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allClients as $client): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($client['name']); ?></td>
                            <td><?php echo htmlspecialchars($client['email']); ?></td>
                            <td><?php echo htmlspecialchars($client['phone'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $client['status']; ?>">
                                    <?php echo ucfirst($client['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($client['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="editClient(<?php echo $client['id']; ?>)">
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

<!-- Edit Client Modal -->
<div id="editClientModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Client</h3>
            <span class="close" onclick="closeModal('editClientModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST" id="editClientForm">
                <input type="hidden" name="client_id" id="edit_client_id">
                
                <div class="form-group">
                    <label for="edit_name">Full Name</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_phone">Phone</label>
                    <input type="tel" id="edit_phone" name="phone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="form-control">
                        <option value="prospect">Prospect</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_notes">Notes</label>
                    <textarea id="edit_notes" name="notes" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editClientModal')">Cancel</button>
                    <button type="submit" name="update_client" class="btn btn-primary">Update Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editClient(id) {
    // This would fetch client data and populate the form
    document.getElementById('edit_client_id').value = id;
    document.getElementById('editClientModal').style.display = 'block';
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
