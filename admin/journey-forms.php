<?php
/**
 * Journey Forms Management - Pallavi Singh Coaching
 * Manage transformation journey form submissions
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
        
        if (isset($_POST['update_journey'])) {
            $journeyId = $_POST['journey_id'];
            $updateData = [
                'status' => $_POST['status'],
                'notes' => $_POST['notes']
            ];
            
            $db->update('journey_submissions', $journeyId, $updateData);
            $success = "Journey submission updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get journey submissions data
try {
    $db = JsonDatabase::getInstance();
    $allJourneys = $db->getData('journey_submissions') ?: [];
    
    // Sort by submission_date descending
    usort($allJourneys, function($a, $b) {
        $dateA = $a['submission_date'] ?? '1970-01-01';
        $dateB = $b['submission_date'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Journey Forms';
$pageSubtitle = 'Manage transformation journey submissions';

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

<!-- Journey Forms Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-secondary" onclick="exportJourneys()">
            📊 Export Journeys
        </button>
        <button class="btn btn-primary" onclick="markAllAsStarted()">
            🚀 Mark All as Started
        </button>
    </div>
    
    <div class="actions-right">
        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="new">New</option>
            <option value="started">Started</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="on_hold">On Hold</option>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search journeys...">
    </div>
</div>

<!-- Journey Submissions Table -->
<div class="data-table-container fade-in">
    <table class="data-table" id="journeysTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Journey Type</th>
                <th>Current Challenge</th>
                <th>Goal</th>
                <th>Timeline</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allJourneys as $journey): ?>
            <tr>
                <td>
                    <div class="journey-info">
                        <strong><?php echo htmlspecialchars($journey['full_name'] ?? 'N/A'); ?></strong>
                    </div>
                </td>
                <td>
                    <a href="mailto:<?php echo htmlspecialchars($journey['email'] ?? ''); ?>" class="email-link">
                        <?php echo htmlspecialchars($journey['email'] ?? 'N/A'); ?>
                    </a>
                </td>
                <td>
                    <span class="journey-type-badge">
                        <?php echo htmlspecialchars($journey['journey_type'] ?? 'General'); ?>
                    </span>
                </td>
                <td>
                    <div class="challenge-preview">
                        <?php echo htmlspecialchars(substr($journey['current_challenge'] ?? 'N/A', 0, 80)); ?>
                        <?php if (strlen($journey['current_challenge'] ?? '') > 80): ?>...<?php endif; ?>
                    </div>
                </td>
                <td>
                    <div class="goal-preview">
                        <?php if (!empty($journey['goal'])): ?>
                            <?php echo htmlspecialchars(substr($journey['goal'], 0, 60)); ?>
                            <?php if (strlen($journey['goal']) > 60): ?>...<?php endif; ?>
                        <?php else: ?>
                            <span class="no-goal">No specific goal</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <span class="timeline-info">
                        <?php echo htmlspecialchars($journey['timeline'] ?? 'Flexible'); ?>
                    </span>
                </td>
                <td>
                    <span class="status-badge status-<?php echo $journey['status'] ?? 'new'; ?>">
                        <?php echo ucfirst($journey['status'] ?? 'new'); ?>
                    </span>
                </td>
                <td>
                    <div class="date-info">
                        <?php echo $journey['submission_date'] ? date('M j, Y', strtotime($journey['submission_date'])) : 'N/A'; ?>
                        <small><?php echo $journey['submission_date'] ? date('H:i', strtotime($journey['submission_date'])) : ''; ?></small>
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="viewJourney(<?php echo $journey['id']; ?>)">
                            👁️ View
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="editJourney(<?php echo $journey['id']; ?>)">
                            ✏️ Edit
                        </button>
                        <button class="btn btn-sm btn-success" onclick="startJourney(<?php echo $journey['id']; ?>)">
                            🚀 Start
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Journey Details Modal -->
<div id="journeyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Journey Details</h3>
            <button class="modal-close" onclick="closeModal('journeyModal')">&times;</button>
        </div>
        <div class="modal-body" id="journeyDetails">
            <!-- Journey details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('journeyModal')">Close</button>
            <button class="btn btn-primary" onclick="startJourney()">Start Journey</button>
        </div>
    </div>
</div>

<!-- Edit Journey Modal -->
<div id="editJourneyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Journey Submission</h3>
            <button class="modal-close" onclick="closeModal('editJourneyModal')">&times;</button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="journey_id" id="editJourneyId">
            <input type="hidden" name="update_journey" value="1">
            
            <div class="form-group">
                <label for="editStatus">Status:</label>
                <select name="status" id="editStatus" class="form-control">
                    <option value="new">New</option>
                    <option value="started">Started</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="editNotes">Notes:</label>
                <textarea name="notes" id="editNotes" class="form-control" rows="4" placeholder="Add notes about this journey..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editJourneyModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Journey</button>
            </div>
        </form>
    </div>
</div>


<script>
function viewJourney(journeyId) {
    alert('View journey details for ID: ' + journeyId);
    document.getElementById('journeyModal').style.display = 'block';
}

function editJourney(journeyId) {
    document.getElementById('editJourneyId').value = journeyId;
    document.getElementById('editJourneyModal').style.display = 'block';
}

function startJourney(journeyId) {
    alert('Start journey ID: ' + journeyId);
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function exportJourneys() {
    // Get journey data
    const table = document.getElementById('journeysTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    
    let csvContent = 'Name,Email,Journey Type,Current Challenge,Goal,Timeline,Status,Submission Date\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 8) {
            const name = cells[0].textContent.trim();
            const email = cells[1].textContent.trim();
            const journeyType = cells[2].textContent.trim();
            const challenge = cells[3].textContent.trim();
            const goal = cells[4].textContent.trim();
            const timeline = cells[5].textContent.trim();
            const status = cells[6].textContent.trim();
            const submissionDate = cells[7].textContent.trim();
            
            csvContent += `"${name}","${email}","${journeyType}","${challenge}","${goal}","${timeline}","${status}","${submissionDate}"\n`;
        }
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute("download", "journeys_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    showNotification('Journeys exported successfully!', 'success');
}

function markAllAsStarted() {
    if (confirm('Mark all journeys as started?')) {
        alert('All journeys marked as started');
    }
}

// Filter and search functionality
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('searchInput').addEventListener('input', filterTable);

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const table = document.getElementById('journeysTable');
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
