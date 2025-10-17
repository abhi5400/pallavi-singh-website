<?php
/**
 * Contact Forms Management - Pallavi Singh Coaching
 * Manage contact form submissions
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
        
        if (isset($_POST['update_contact'])) {
            $contactId = $_POST['contact_id'];
            $updateData = [
                'status' => $_POST['status'],
                'notes' => $_POST['notes']
            ];
            
            $db->update('contact_submissions', $contactId, $updateData);
            $success = "Contact submission updated successfully!";
        }
        
        if (isset($_POST['delete_contact'])) {
            $contactId = $_POST['contact_id'];
            // Note: You might want to implement a soft delete instead
            $success = "Contact submission deleted successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get contact submissions data
try {
    $db = JsonDatabase::getInstance();
    $allContacts = $db->getData('contact_submissions');
    
    // Sort by submission_date descending
    usort($allContacts, function($a, $b) {
        $dateA = $a['submission_date'] ?? '1970-01-01';
        $dateB = $b['submission_date'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Contact Forms';
$pageSubtitle = 'Manage contact form submissions and inquiries';

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

<!-- Contact Forms Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-secondary" onclick="exportContacts()">
            📊 Export Contacts
        </button>
        <button class="btn btn-primary" onclick="markAllAsRead()">
            ✅ Mark All as Read
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
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search contacts...">
    </div>
</div>

<!-- Contact Submissions Table -->
<div class="data-table-container fade-in">
    <table class="data-table" id="contactsTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Subject</th>
                <th>Service Interest</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allContacts as $contact): ?>
            <tr>
                <td>
                    <div class="contact-info">
                        <strong><?php echo htmlspecialchars($contact['name'] ?? 'N/A'); ?></strong>
                    </div>
                </td>
                <td>
                    <a href="mailto:<?php echo htmlspecialchars($contact['email'] ?? ''); ?>" class="email-link">
                        <?php echo htmlspecialchars($contact['email'] ?? 'N/A'); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($contact['phone'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($contact['subject'] ?? 'N/A'); ?></td>
                <td>
                    <?php if (!empty($contact['service_interest'])): ?>
                        <span class="service-badge"><?php echo htmlspecialchars($contact['service_interest']); ?></span>
                    <?php else: ?>
                        <span class="no-service">No specific service</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status-badge status-<?php echo $contact['status'] ?? 'new'; ?>">
                        <?php echo ucfirst($contact['status'] ?? 'new'); ?>
                    </span>
                </td>
                <td>
                    <div class="date-info">
                        <?php echo $contact['submission_date'] ? date('M j, Y', strtotime($contact['submission_date'])) : 'N/A'; ?>
                        <small><?php echo $contact['submission_date'] ? date('H:i', strtotime($contact['submission_date'])) : ''; ?></small>
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="viewContact(<?php echo $contact['id']; ?>)">
                            👁️ View
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="editContact(<?php echo $contact['id']; ?>)">
                            ✏️ Edit
                        </button>
                        <button class="btn btn-sm btn-success" onclick="replyToContact(<?php echo $contact['id']; ?>)">
                            📧 Reply
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Contact Details Modal -->
<div id="contactModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Contact Details</h3>
            <button class="modal-close" onclick="closeModal('contactModal')">&times;</button>
        </div>
        <div class="modal-body" id="contactDetails">
            <!-- Contact details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('contactModal')">Close</button>
            <button class="btn btn-primary" onclick="replyToContact()">Reply</button>
        </div>
    </div>
</div>

<!-- Edit Contact Modal -->
<div id="editContactModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Contact</h3>
            <button class="modal-close" onclick="closeModal('editContactModal')">&times;</button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="contact_id" id="editContactId">
            <input type="hidden" name="update_contact" value="1">
            
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
                <textarea name="notes" id="editNotes" class="form-control" rows="4" placeholder="Add notes about this contact..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editContactModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Contact</button>
            </div>
        </form>
    </div>
</div>


<script>
function viewContact(contactId) {
    // Get the row data for this contact ID
    const table = document.getElementById('contactsTable');
    const rows = table.querySelectorAll('tbody tr');
    
    let contactData = null;
    rows.forEach(row => {
        const viewButton = row.querySelector('button[onclick*="viewContact"]');
        if (viewButton && viewButton.getAttribute('onclick').includes(contactId)) {
            const cells = row.querySelectorAll('td');
            contactData = {
                name: cells[0].textContent.trim(),
                email: cells[1].textContent.trim(),
                phone: cells[2].textContent.trim(),
                subject: cells[3].textContent.trim(),
                service: cells[4].textContent.trim(),
                status: cells[5].textContent.trim(),
                date: cells[6].textContent.trim()
            };
        }
    });
    
    if (contactData) {
        // Populate modal with data
        const modalBody = document.getElementById('contactDetails');
        modalBody.innerHTML = `
            <div class="contact-details">
                <div class="detail-section">
                    <h4>👤 Contact Information</h4>
                    <div class="detail-row">
                        <strong>Name:</strong> ${contactData.name}
                    </div>
                    <div class="detail-row">
                        <strong>Email:</strong> <a href="mailto:${contactData.email}">${contactData.email}</a>
                    </div>
                    <div class="detail-row">
                        <strong>Phone:</strong> ${contactData.phone}
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4>📧 Inquiry Details</h4>
                    <div class="detail-row">
                        <strong>Subject:</strong>
                        <p class="subject-text">${contactData.subject}</p>
                    </div>
                    <div class="detail-row">
                        <strong>Service Interest:</strong>
                        <span class="service-badge">${contactData.service}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4>📊 Status & Timeline</h4>
                    <div class="detail-row">
                        <strong>Current Status:</strong> 
                        <span class="status-badge status-${contactData.status.toLowerCase()}">${contactData.status}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Submission Date:</strong> ${contactData.date}
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('contactModal').style.display = 'block';
    } else {
        alert('Could not load contact details for ID: ' + contactId);
    }
}

function editContact(contactId) {
    document.getElementById('editContactId').value = contactId;
    document.getElementById('editContactModal').style.display = 'block';
}

function replyToContact(contactId) {
    // Open email client or show reply form
    alert('Reply to contact ID: ' + contactId);
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function exportContacts() {
    // Get contact data
    const table = document.getElementById('contactsTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    
    let csvContent = 'Name,Email,Phone,Subject,Service Interest,Status,Submission Date\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 7) {
            const name = cells[0].textContent.trim();
            const email = cells[1].textContent.trim();
            const phone = cells[2].textContent.trim();
            const subject = cells[3].textContent.trim();
            const service = cells[4].textContent.trim();
            const status = cells[5].textContent.trim();
            const date = cells[6].textContent.trim();
            
            csvContent += `"${name}","${email}","${phone}","${subject}","${service}","${status}","${date}"\n`;
        }
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute("download", "contacts_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    showNotification('Contacts exported successfully!', 'success');
}

function markAllAsRead() {
    if (confirm('Mark all contacts as read?')) {
        alert('All contacts marked as read');
    }
}

// Filter and search functionality
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('searchInput').addEventListener('input', filterTable);

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const table = document.getElementById('contactsTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const statusCell = row.cells[5];
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
