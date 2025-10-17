<?php
/**
 * Booking Forms Management - Pallavi Singh Coaching
 * Manage booking form submissions and appointments
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
        
        if (isset($_POST['update_booking'])) {
            $bookingId = $_POST['booking_id'];
            $updateData = [
                'status' => $_POST['status'],
                'notes' => $_POST['notes']
            ];
            
            $db->update('booking_submissions', $bookingId, $updateData);
            $success = "Booking updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get booking submissions data
try {
    $db = JsonDatabase::getInstance();
    $allBookings = $db->getData('booking_submissions') ?: [];
    
    // Sort by submission_date descending
    usort($allBookings, function($a, $b) {
        $dateA = $a['submission_date'] ?? '1970-01-01';
        $dateB = $b['submission_date'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    });
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Booking Forms';
$pageSubtitle = 'Manage booking requests and appointments';

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

<!-- Booking Forms Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-secondary" onclick="exportBookings()">
            📊 Export Bookings
        </button>
        <button class="btn btn-primary" onclick="markAllAsConfirmed()">
            ✅ Mark All as Confirmed
        </button>
    </div>
    
    <div class="actions-right">
        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search bookings...">
    </div>
</div>

<!-- Booking Submissions Table -->
<div class="data-table-container fade-in">
    <table class="data-table" id="bookingsTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Service Type</th>
                <th>Preferred Date</th>
                <th>Preferred Time</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allBookings as $booking): ?>
            <tr>
                <td>
                    <div class="booking-info">
                        <strong><?php echo htmlspecialchars($booking['full_name'] ?? 'N/A'); ?></strong>
                    </div>
                </td>
                <td>
                    <a href="mailto:<?php echo htmlspecialchars($booking['email'] ?? ''); ?>" class="email-link">
                        <?php echo htmlspecialchars($booking['email'] ?? 'N/A'); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($booking['phone'] ?? 'N/A'); ?></td>
                <td>
                    <span class="service-badge">
                        <?php echo htmlspecialchars($booking['service_type'] ?? 'General'); ?>
                    </span>
                </td>
                <td>
                    <div class="date-info">
                        <?php echo $booking['preferred_date'] ? date('M j, Y', strtotime($booking['preferred_date'])) : 'Flexible'; ?>
                    </div>
                </td>
                <td>
                    <span class="time-info">
                        <?php echo htmlspecialchars($booking['preferred_time'] ?? 'Any'); ?>
                    </span>
                </td>
                <td>
                    <span class="status-badge status-<?php echo $booking['status'] ?? 'pending'; ?>">
                        <?php echo ucfirst($booking['status'] ?? 'pending'); ?>
                    </span>
                </td>
                <td>
                    <div class="date-info">
                        <?php echo $booking['submission_date'] ? date('M j, Y', strtotime($booking['submission_date'])) : 'N/A'; ?>
                        <small><?php echo $booking['submission_date'] ? date('H:i', strtotime($booking['submission_date'])) : ''; ?></small>
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="viewBooking(<?php echo $booking['id']; ?>)">
                            👁️ View
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="editBooking(<?php echo $booking['id']; ?>)">
                            ✏️ Edit
                        </button>
                        <button class="btn btn-sm btn-success" onclick="confirmBooking(<?php echo $booking['id']; ?>)">
                            ✅ Confirm
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Booking Details Modal -->
<div id="bookingModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Booking Details</h3>
            <button class="modal-close" onclick="closeModal('bookingModal')">&times;</button>
        </div>
        <div class="modal-body" id="bookingDetails">
            <!-- Booking details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('bookingModal')">Close</button>
            <button class="btn btn-primary" onclick="confirmBooking()">Confirm Booking</button>
        </div>
    </div>
</div>

<!-- Edit Booking Modal -->
<div id="editBookingModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Booking</h3>
            <button class="modal-close" onclick="closeModal('editBookingModal')">&times;</button>
        </div>
        <form method="POST" class="modal-body">
            <input type="hidden" name="booking_id" id="editBookingId">
            <input type="hidden" name="update_booking" value="1">
            
            <div class="form-group">
                <label for="editStatus">Status:</label>
                <select name="status" id="editStatus" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="editNotes">Notes:</label>
                <textarea name="notes" id="editNotes" class="form-control" rows="4" placeholder="Add notes about this booking..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editBookingModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Booking</button>
            </div>
        </form>
    </div>
</div>


<script>
function viewBooking(bookingId) {
    alert('View booking details for ID: ' + bookingId);
    document.getElementById('bookingModal').style.display = 'block';
}

function editBooking(bookingId) {
    document.getElementById('editBookingId').value = bookingId;
    document.getElementById('editBookingModal').style.display = 'block';
}

function confirmBooking(bookingId) {
    alert('Confirm booking ID: ' + bookingId);
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function exportBookings() {
    // Get booking data
    const table = document.getElementById('bookingsTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    
    let csvContent = 'Name,Email,Phone,Service Type,Preferred Date,Preferred Time,Status,Submission Date\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 8) {
            const name = cells[0].textContent.trim();
            const email = cells[1].textContent.trim();
            const phone = cells[2].textContent.trim();
            const service = cells[3].textContent.trim();
            const date = cells[4].textContent.trim();
            const time = cells[5].textContent.trim();
            const status = cells[6].textContent.trim();
            const submissionDate = cells[7].textContent.trim();
            
            csvContent += `"${name}","${email}","${phone}","${service}","${date}","${time}","${status}","${submissionDate}"\n`;
        }
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute("download", "bookings_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    
    showNotification('Bookings exported successfully!', 'success');
}

function markAllAsConfirmed() {
    if (confirm('Mark all bookings as confirmed?')) {
        alert('All bookings marked as confirmed');
    }
}

// Filter and search functionality
document.getElementById('statusFilter').addEventListener('change', filterTable);
document.getElementById('searchInput').addEventListener('input', filterTable);

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const table = document.getElementById('bookingsTable');
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
