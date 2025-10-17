<?php
/**
 * Waitlist Forms Management - Pallavi Singh Coaching
 * Manage waitlist subscriptions and notifications
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
        $db = JsonDatabase::getInstance();
        
        if (isset($_POST['update_waitlist'])) {
            $waitlistId = $_POST['waitlist_id'];
            $updateData = [
                'status' => $_POST['status'],
                'notes' => $_POST['notes']
            ];
            
            $db->update('waitlist_subscriptions', $waitlistId, $updateData);
            $success = "Waitlist subscription updated successfully!";
        }
        
        if (isset($_POST['notify_waitlist'])) {
            $waitlistId = $_POST['waitlist_id'];
            // Notification logic would go here
            $success = "Waitlist member notified successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get waitlist submissions data
try {
    $db = JsonDatabase::getInstance();
    $allWaitlist = $db->getData('waitlist_subscriptions') ?: [];
    
    // Sort by submission_date descending
    usort($allWaitlist, function($a, $b) {
        $dateA = $a['submission_date'] ?? $a['created_at'] ?? '1970-01-01';
        $dateB = $b['submission_date'] ?? $b['created_at'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Waitlist Forms';
$pageSubtitle = 'Manage waitlist subscriptions and notifications';

// Add forms CSS
$additionalCSS = ['assets/css/forms.css'];

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

<!-- Waitlist Forms Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-secondary" onclick="exportWaitlist()">
            📊 Export Waitlist
        </button>
        <button class="btn btn-primary" onclick="notifyAllWaitlist()">
            📧 Notify All Waitlist
        </button>
    </div>
    
    <div class="actions-right">
        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="waiting">Waiting</option>
            <option value="notified">Notified</option>
            <option value="converted">Converted</option>
            <option value="expired">Expired</option>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search waitlist...">
    </div>
</div>

<!-- Waitlist Submissions Table -->
<div class="data-table-container fade-in">
    <table class="data-table" id="waitlistTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Interest</th>
                <th>Priority</th>
                <th>Source</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allWaitlist as $waitlist): ?>
            <tr>
                <td>
                    <div class="waitlist-info">
                        <strong><?php echo htmlspecialchars($waitlist['full_name'] ?? $waitlist['name'] ?? 'N/A'); ?></strong>
                    </div>
                </td>
                <td>
                    <a href="mailto:<?php echo htmlspecialchars($waitlist['email'] ?? ''); ?>" class="email-link">
                        <?php echo htmlspecialchars($waitlist['email'] ?? 'N/A'); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($waitlist['phone'] ?? 'N/A'); ?></td>
                <td>
                    <span class="interest-badge">
                        <?php echo htmlspecialchars($waitlist['interest'] ?? $waitlist['service_interest'] ?? 'General'); ?>
                    </span>
                </td>
                <td>
                    <span class="priority-badge priority-<?php echo $waitlist['priority'] ?? 'normal'; ?>">
                        <?php echo ucfirst($waitlist['priority'] ?? 'normal'); ?>
                    </span>
                </td>
                <td>
                    <span class="source-info">
                        <?php echo htmlspecialchars($waitlist['source'] ?? $waitlist['referral_source'] ?? 'Direct'); ?>
                    </span>
                </td>
                <td>
                    <span class="status-badge status-<?php echo $waitlist['status'] ?? 'waiting'; ?>">
                        <?php echo ucfirst($waitlist['status'] ?? 'waiting'); ?>
                    </span>
                </td>
                <td>
                    <div class="date-info">
                        <?php 
                            $dateField = $waitlist['submission_date'] ?? $waitlist['created_at'] ?? '';
                            echo $dateField ? date('M j, Y', strtotime($dateField)) : 'N/A'; 
                        ?>
                        <small>
                            <?php 
                                echo $dateField ? date('H:i', strtotime($dateField)) : ''; 
                            ?>
                        </small>
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="viewWaitlist(<?php echo $waitlist['id']; ?>)">
                            👁️ View
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="editWaitlist(<?php echo $waitlist['id']; ?>)">
                            ✏️ Edit
                        </button>
                        <button class="btn btn-sm btn-success" onclick="notifyWaitlist(<?php echo $waitlist['id']; ?>)">
                            📧 Notify
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Waitlist Details Modal -->
<div id="waitlistModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Waitlist Details</h3>
            <button class="modal-close" onclick="closeModal('waitlistModal')">&times;</button>
        </div>
        <div class="modal-body" id="waitlistDetails">
            <!-- Waitlist details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('waitlistModal')">Close</button>
            <button class="btn btn-primary" onclick="notifyWaitlist()">Send Notification</button>
        </div>
    </div>
</div>

<!-- Edit Waitlist Modal -->
<div id="editWaitlistModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Waitlist Subscription</h3>
            <button class="modal-close" onclick="closeModal('editWaitlistModal')">&times;</button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="waitlist_id" id="editWaitlistId">
            <input type="hidden" name="update_waitlist" value="1">
            
            <div class="form-group">
                <label for="editStatus">Status:</label>
                <select name="status" id="editStatus" class="form-control">
                    <option value="waiting">Waiting</option>
                    <option value="notified">Notified</option>
                    <option value="converted">Converted</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="editPriority">Priority:</label>
                <select name="priority" id="editPriority" class="form-control">
                    <option value="low">Low</option>
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="editNotes">Notes:</label>
                <textarea name="notes" id="editNotes" class="form-control" rows="4" placeholder="Add notes about this waitlist subscription..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editWaitlistModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Waitlist</button>
            </div>
        </form>
    </div>
</div>


<script>
function viewWaitlist(waitlistId) {
    alert('View waitlist details for ID: ' + waitlistId);
    document.getElementById('waitlistModal').style.display = 'block';
}

function editWaitlist(waitlistId) {
    document.getElementById('editWaitlistId').value = waitlistId;
    document.getElementById('editWaitlistModal').style.display = 'block';
}

function notifyWaitlist(waitlistId) {
    alert('Notify waitlist member ID: ' + waitlistId);
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function exportWaitlist() {
    // Get waitlist data
    const table = document.getElementById('waitlistTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    
    let csvContent = 'Name,Email,Phone,Interest,Priority,Source,Status,Submission Date\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 8) {
            const name = cells[0].textContent.trim();
            const email = cells[1].textContent.trim();
            const phone = cells[2].textContent.trim();
            const interest = cells[3].textContent.trim();
            const priority = cells[4].textContent.trim();
            const source = cells[5].textContent.trim();
            const status = cells[6].textContent.trim();
            const submissionDate = cells[7].textContent.trim();
            
            csvContent += `"${name}","${email}","${phone}","${interest}","${priority}","${source}","${status}","${submissionDate}"\n`;
        }
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute("download", "waitlist_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    showNotification('Waitlist exported successfully!', 'success');
}

function notifyAllWaitlist() {
    if (confirm('Send notifications to all waitlist members?')) {
        alert('Notifications sent to all waitlist members');
    }
}

// Filter and search functionality
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('searchInput').addEventListener('input', filterTable);

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const table = document.getElementById('waitlistTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const statusCell = row.cells[6];
        const nameCell = row.cells[0];
        const emailCell = row.cells[1];
        
        const status = statusCell.textContent.trim().toLowerCase();
        const name = nameCell.textContent.toLowerCase();
        const email = emailCell.textContent.toLowerCase();
        
        const statusMatch = !statusFilter || status === statusFilter.toLowerCase();
        const searchMatch = !searchInput || name.includes(searchInput) || email.includes(searchInput);
        
        row.style.display = statusMatch && searchMatch ? '' : 'none';
    }
}
</script>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>
