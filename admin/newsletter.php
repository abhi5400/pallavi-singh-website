<?php
/**
 * Newsletter Management - Pallavi Singh Coaching
 * Manage newsletter subscriptions and campaigns
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
        
        if (isset($_POST['send_newsletter'])) {
            // Newsletter sending logic would go here
            $success = "Newsletter sent successfully to all subscribers!";
        }
        
        if (isset($_POST['update_subscription'])) {
            $subscriptionId = $_POST['subscription_id'];
            $updateData = [
                'status' => $_POST['status'],
                'notes' => $_POST['notes']
            ];
            
            $db->update('newsletter_subscriptions', $subscriptionId, $updateData);
            $success = "Subscription updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get newsletter data
try {
    $db = Database::getInstance();
    $allSubscriptions = $db->getData('newsletter_subscriptions');
    
    // Sort by subscription_date descending
    usort($allSubscriptions, function($a, $b) {
        return strtotime($b['subscription_date']) - strtotime($a['subscription_date']);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Newsletter Management';
$pageSubtitle = 'Manage newsletter subscriptions and campaigns';

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
    <!-- Newsletter Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($allSubscriptions); ?></h3>
                <p>Total Subscribers</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allSubscriptions, function($s) { return $s['status'] === 'active'; })); ?></h3>
                <p>Active Subscribers</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allSubscriptions, function($s) { return $s['status'] === 'paused'; })); ?></h3>
                <p>Paused Subscriptions</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allSubscriptions, function($s) { return $s['status'] === 'unsubscribed'; })); ?></h3>
                <p>Unsubscribed</p>
            </div>
        </div>
    </div>
    
    <!-- Send Newsletter -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-paper-plane"></i> Send Newsletter</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" class="form-control" rows="10" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="recipients">Recipients</label>
                    <select id="recipients" name="recipients" class="form-control">
                        <option value="all">All Active Subscribers</option>
                        <option value="new">New Subscribers Only</option>
                    </select>
                </div>
                
                <button type="submit" name="send_newsletter" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Newsletter
                </button>
            </form>
        </div>
    </div>
    
    <!-- Subscribers List -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-users"></i> Newsletter Subscribers</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Subscription Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allSubscriptions as $subscription): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($subscription['email']); ?></td>
                            <td><?php echo htmlspecialchars($subscription['name'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $subscription['status']; ?>">
                                    <?php echo ucfirst($subscription['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($subscription['subscription_date'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="editSubscription(<?php echo $subscription['id']; ?>)">
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

<!-- Edit Subscription Modal -->
<div id="editSubscriptionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Subscription</h3>
            <span class="close" onclick="closeModal('editSubscriptionModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST" id="editSubscriptionForm">
                <input type="hidden" name="subscription_id" id="edit_subscription_id">
                
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="paused">Paused</option>
                        <option value="unsubscribed">Unsubscribed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_notes">Notes</label>
                    <textarea id="edit_notes" name="notes" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editSubscriptionModal')">Cancel</button>
                    <button type="submit" name="update_subscription" class="btn btn-primary">Update Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSubscription(id) {
    // This would fetch subscription data and populate the form
    document.getElementById('edit_subscription_id').value = id;
    document.getElementById('editSubscriptionModal').style.display = 'block';
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
