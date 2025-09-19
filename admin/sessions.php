<?php
/**
 * Sessions Management - Pallavi Singh Coaching
 * Manage coaching sessions and appointments
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
        
        if (isset($_POST['add_session'])) {
            $sessionData = [
                'client_id' => $_POST['client_id'],
                'session_type' => $_POST['session_type'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'duration' => $_POST['duration'],
                'status' => $_POST['status'],
                'notes' => $_POST['notes'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->insert('sessions', $sessionData);
            $success = "Session added successfully!";
        }
        
        if (isset($_POST['update_session'])) {
            $sessionId = $_POST['session_id'];
            $updateData = [
                'client_id' => $_POST['client_id'],
                'session_type' => $_POST['session_type'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'duration' => $_POST['duration'],
                'status' => $_POST['status'],
                'notes' => $_POST['notes']
            ];
            
            $db->update('sessions', $sessionId, $updateData);
            $success = "Session updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get sessions and clients data
try {
    $db = Database::getInstance();
    $allSessions = $db->getData('sessions');
    $allClients = $db->getData('clients');
    
    // Sort sessions by date descending
    usort($allSessions, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Sessions Management';
$pageSubtitle = 'Manage coaching sessions and appointments';

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
    <!-- Session Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($allSessions); ?></h3>
                <p>Total Sessions</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allSessions, function($s) { return $s['status'] === 'completed'; })); ?></h3>
                <p>Completed</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allSessions, function($s) { return $s['status'] === 'scheduled'; })); ?></h3>
                <p>Scheduled</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count(array_filter($allSessions, function($s) { return $s['status'] === 'cancelled'; })); ?></h3>
                <p>Cancelled</p>
            </div>
        </div>
    </div>
    
    <!-- Add Session -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-plus"></i> Schedule New Session</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="client_id">Client</label>
                        <select id="client_id" name="client_id" class="form-control" required>
                            <option value="">Select Client</option>
                            <?php foreach ($allClients as $client): ?>
                            <option value="<?php echo $client['id']; ?>"><?php echo htmlspecialchars($client['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="session_type">Session Type</label>
                        <select id="session_type" name="session_type" class="form-control" required>
                            <option value="life-coaching">Life Coaching</option>
                            <option value="anxiety-management">Anxiety Management</option>
                            <option value="public-speaking">Public Speaking</option>
                            <option value="consultation">Consultation</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" id="time" name="time" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <select id="duration" name="duration" class="form-control" required>
                            <option value="30">30 minutes</option>
                            <option value="60" selected>60 minutes</option>
                            <option value="90">90 minutes</option>
                            <option value="120">120 minutes</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" name="add_session" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Schedule Session
                </button>
            </form>
        </div>
    </div>
    
    <!-- Sessions List -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> All Sessions</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allSessions as $session): ?>
                        <?php 
                        $client = null;
                        foreach ($allClients as $c) {
                            if ($c['id'] == $session['client_id']) {
                                $client = $c;
                                break;
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo $client ? htmlspecialchars($client['name']) : 'Unknown Client'; ?></td>
                            <td><?php echo ucwords(str_replace('-', ' ', $session['session_type'])); ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($session['date'] . ' ' . $session['time'])); ?></td>
                            <td><?php echo $session['duration']; ?> min</td>
                            <td>
                                <span class="status-badge status-<?php echo $session['status']; ?>">
                                    <?php echo ucfirst($session['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="editSession(<?php echo $session['id']; ?>)">
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

<!-- Edit Session Modal -->
<div id="editSessionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Session</h3>
            <span class="close" onclick="closeModal('editSessionModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST" id="editSessionForm">
                <input type="hidden" name="session_id" id="edit_session_id">
                
                <div class="form-group">
                    <label for="edit_client_id">Client</label>
                    <select id="edit_client_id" name="client_id" class="form-control" required>
                        <?php foreach ($allClients as $client): ?>
                        <option value="<?php echo $client['id']; ?>"><?php echo htmlspecialchars($client['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_session_type">Session Type</label>
                    <select id="edit_session_type" name="session_type" class="form-control" required>
                        <option value="life-coaching">Life Coaching</option>
                        <option value="anxiety-management">Anxiety Management</option>
                        <option value="public-speaking">Public Speaking</option>
                        <option value="consultation">Consultation</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_date">Date</label>
                        <input type="date" id="edit_date" name="date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_time">Time</label>
                        <input type="time" id="edit_time" name="time" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_duration">Duration (minutes)</label>
                    <select id="edit_duration" name="duration" class="form-control" required>
                        <option value="30">30 minutes</option>
                        <option value="60">60 minutes</option>
                        <option value="90">90 minutes</option>
                        <option value="120">120 minutes</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="form-control">
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_notes">Notes</label>
                    <textarea id="edit_notes" name="notes" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editSessionModal')">Cancel</button>
                    <button type="submit" name="update_session" class="btn btn-primary">Update Session</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSession(id) {
    // This would fetch session data and populate the form
    document.getElementById('edit_session_id').value = id;
    document.getElementById('editSessionModal').style.display = 'block';
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
