<?php
/**
 * Join Forms Management - Pallavi Singh Coaching
 * Manage "Join Now" form submissions
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
        
        if (isset($_POST['update_join'])) {
            $joinId = $_POST['join_id'];
            $updateData = [
                'status' => $_POST['status'],
                'notes' => $_POST['notes']
            ];
            
            $db->update('join_submissions', $joinId, $updateData);
            $success = "Join submission updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get join submissions data
try {
    $db = JsonDatabase::getInstance();
    $allJoins = $db->getData('join_submissions');
    
    // Sort by submission_date descending
    usort($allJoins, function($a, $b) {
        $dateA = $a['submission_date'] ?? '1970-01-01';
        $dateB = $b['submission_date'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Join Forms';
$pageSubtitle = 'Manage "Join Now" form submissions';

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

<!-- Join Forms Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-secondary" onclick="exportJoins()">
            📊 Export Joins
        </button>
        <button class="btn btn-primary" onclick="markAllAsContacted()">
            ✅ Mark All as Contacted
        </button>
    </div>
    
    <div class="actions-right">
        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="new">New</option>
            <option value="contacted">Contacted</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search joins...">
    </div>
</div>

<!-- Join Submissions Table -->
<div class="data-table-container fade-in">
    <table class="data-table" id="joinsTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Location</th>
                <th>Challenge/Issue</th>
                <th>Goals</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allJoins as $join): ?>
            <tr>
                <td>
                    <div class="join-info">
                        <strong><?php echo htmlspecialchars($join['full_name'] ?? 'N/A'); ?></strong>
                        <?php if (!empty($join['age'])): ?>
                            <small>(<?php echo $join['age']; ?> years old)</small>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <a href="mailto:<?php echo htmlspecialchars($join['email'] ?? ''); ?>" class="email-link">
                        <?php echo htmlspecialchars($join['email'] ?? 'N/A'); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($join['contact_number'] ?? 'N/A'); ?></td>
                <td>
                    <span class="location-info">
                        <?php echo htmlspecialchars($join['city'] ?? 'N/A'); ?>, 
                        <?php echo htmlspecialchars($join['state'] ?? 'N/A'); ?>
                    </span>
                </td>
                <td>
                    <div class="challenge-preview">
                        <?php echo htmlspecialchars(substr($join['issue_challenge'] ?? 'N/A', 0, 100)); ?>
                        <?php if (strlen($join['issue_challenge'] ?? '') > 100): ?>...<?php endif; ?>
                    </div>
                </td>
                <td>
                    <div class="goals-preview">
                        <?php if (!empty($join['goals'])): ?>
                            <?php echo htmlspecialchars(substr($join['goals'], 0, 80)); ?>
                            <?php if (strlen($join['goals']) > 80): ?>...<?php endif; ?>
                        <?php else: ?>
                            <span class="no-goals">No specific goals</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <span class="status-badge status-<?php echo $join['status'] ?? 'new'; ?>">
                        <?php echo ucfirst($join['status'] ?? 'new'); ?>
                    </span>
                </td>
                <td>
                    <div class="date-info">
                        <?php echo $join['submission_date'] ? date('M j, Y', strtotime($join['submission_date'])) : 'N/A'; ?>
                        <small><?php echo $join['submission_date'] ? date('H:i', strtotime($join['submission_date'])) : ''; ?></small>
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="viewJoin(<?php echo $join['id']; ?>)">
                            👁️ View
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="editJoin(<?php echo $join['id']; ?>)">
                            ✏️ Edit
                        </button>
                        <button class="btn btn-sm btn-success" onclick="contactJoin(<?php echo $join['id']; ?>)">
                            📞 Contact
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Join Details Modal -->
<div id="joinModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Join Form Details</h3>
            <button class="modal-close" onclick="closeModal('joinModal')">&times;</button>
        </div>
        <div class="modal-body" id="joinDetails">
            <!-- Join details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('joinModal')">Close</button>
            <button class="btn btn-primary" onclick="contactJoin()">Contact</button>
        </div>
    </div>
</div>

<!-- Edit Join Modal -->
<div id="editJoinModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Join Submission</h3>
            <button class="modal-close" onclick="closeModal('editJoinModal')">&times;</button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="join_id" id="editJoinId">
            <input type="hidden" name="update_join" value="1">
            
            <div class="form-group">
                <label for="editStatus">Status:</label>
                <select name="status" id="editStatus" class="form-control">
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="editNotes">Notes:</label>
                <textarea name="notes" id="editNotes" class="form-control" rows="4" placeholder="Add notes about this submission..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editJoinModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Submission</button>
            </div>
        </form>
    </div>
</div>


<script>
function viewJoin(joinId) {
    // Get the row data for this join ID
    const table = document.getElementById('joinsTable');
    const rows = table.querySelectorAll('tbody tr');
    
    let joinData = null;
    rows.forEach(row => {
        const viewButton = row.querySelector('button[onclick*="viewJoin"]');
        if (viewButton && viewButton.getAttribute('onclick').includes(joinId)) {
            const cells = row.querySelectorAll('td');
            joinData = {
                name: cells[0].textContent.trim(),
                email: cells[1].textContent.trim(),
                contact: cells[2].textContent.trim(),
                location: cells[3].textContent.trim(),
                challenge: cells[4].textContent.trim(),
                goals: cells[5].textContent.trim(),
                status: cells[6].textContent.trim(),
                date: cells[7].textContent.trim()
            };
        }
    });
    
    if (joinData) {
        // Populate modal with data
        const modalBody = document.getElementById('joinDetails');
        modalBody.innerHTML = `
            <div class="join-details">
                <div class="detail-section">
                    <h4>👤 Personal Information</h4>
                    <div class="detail-row">
                        <strong>Name:</strong> ${joinData.name}
                    </div>
                    <div class="detail-row">
                        <strong>Email:</strong> <a href="mailto:${joinData.email}">${joinData.email}</a>
                    </div>
                    <div class="detail-row">
                        <strong>Contact:</strong> ${joinData.contact}
                    </div>
                    <div class="detail-row">
                        <strong>Location:</strong> ${joinData.location}
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4>🎯 Challenge & Goals</h4>
                    <div class="detail-row">
                        <strong>Challenge/Issue:</strong>
                        <p class="challenge-text">${joinData.challenge}</p>
                    </div>
                    <div class="detail-row">
                        <strong>Goals:</strong>
                        <p class="goals-text">${joinData.goals}</p>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4>📊 Status & Timeline</h4>
                    <div class="detail-row">
                        <strong>Current Status:</strong> 
                        <span class="status-badge status-${joinData.status.toLowerCase()}">${joinData.status}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Submission Date:</strong> ${joinData.date}
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('joinModal').style.display = 'block';
    } else {
        alert('Could not load join details for ID: ' + joinId);
    }
}

function editJoin(joinId) {
    document.getElementById('editJoinId').value = joinId;
    document.getElementById('editJoinModal').style.display = 'block';
}

function contactJoin(joinId) {
    alert('Contact join submission ID: ' + joinId);
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function exportJoins() {
    // Get join data
    const table = document.getElementById('joinsTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    
    let csvContent = 'Name,Email,Contact,Location,Challenge/Issue,Goals,Status,Submission Date\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 8) {
            const name = cells[0].textContent.trim();
            const email = cells[1].textContent.trim();
            const contact = cells[2].textContent.trim();
            const location = cells[3].textContent.trim();
            const challenge = cells[4].textContent.trim();
            const goals = cells[5].textContent.trim();
            const status = cells[6].textContent.trim();
            const date = cells[7].textContent.trim();
            
            csvContent += `"${name}","${email}","${contact}","${location}","${challenge}","${goals}","${status}","${date}"\n`;
        }
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute("download", "joins_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    showNotification('Join submissions exported successfully!', 'success');
}

function markAllAsContacted() {
    if (confirm('Mark all join submissions as contacted?')) {
        alert('All submissions marked as contacted');
    }
}

// Filter and search functionality
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('searchInput').addEventListener('input', filterTable);

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const table = document.getElementById('joinsTable');
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
