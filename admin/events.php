<?php
/**
 * Events Management - Pallavi Singh Coaching
 * Manage workshops, events, and sessions
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
        
        if (isset($_POST['create_event'])) {
            $eventData = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'event_type' => $_POST['event_type'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'duration' => $_POST['duration'],
                'location' => $_POST['location'],
                'max_participants' => $_POST['max_participants'],
                'price' => $_POST['price'],
                'registration_link' => $_POST['registration_link'],
                'featured_image' => $_POST['featured_image'],
                'status' => $_POST['status'],
                'instructor' => $_POST['instructor'],
                'requirements' => $_POST['requirements'],
                'what_you_learn' => $_POST['what_you_learn'],
                'created_by' => $_SESSION['admin_user']['full_name']
            ];
            
            $db->insert('events', $eventData);
            $success = "Event created successfully!";
        }
        
        if (isset($_POST['update_event'])) {
            $eventId = $_POST['event_id'];
            $updateData = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'event_type' => $_POST['event_type'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'duration' => $_POST['duration'],
                'location' => $_POST['location'],
                'max_participants' => $_POST['max_participants'],
                'price' => $_POST['price'],
                'registration_link' => $_POST['registration_link'],
                'featured_image' => $_POST['featured_image'],
                'status' => $_POST['status'],
                'instructor' => $_POST['instructor'],
                'requirements' => $_POST['requirements'],
                'what_you_learn' => $_POST['what_you_learn']
            ];
            
            $db->update('events', $eventId, $updateData);
            $success = "Event updated successfully!";
        }
        
        if (isset($_POST['delete_event'])) {
            $eventId = $_POST['event_id'];
            $db->delete('events', $eventId);
            $success = "Event deleted successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get events data
try {
    $db = JsonDatabase::getInstance();
    $allEvents = $db->getData('events');
    
    // Sort by date ascending
    usort($allEvents, function($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });
    
    // Get event types
    $eventTypes = ['Workshop', 'Webinar', 'Group Session', 'Individual Session', 'Retreat', 'Conference', 'Training'];
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Events & Workshops';
$pageSubtitle = 'Manage your coaching events and workshops';

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

<!-- Events Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-primary" onclick="showCreateModal()">
            📅 Create New Event
        </button>
        <button class="btn btn-secondary" onclick="exportEvents()">
            📊 Export Events
        </button>
    </div>
    
    <div class="actions-right">
        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="upcoming">Upcoming</option>
            <option value="ongoing">Ongoing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
        
        <select id="typeFilter" class="filter-select">
            <option value="">All Types</option>
            <?php foreach ($eventTypes as $type): ?>
                <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search events...">
    </div>
</div>

<!-- Events Table -->
<div class="data-table-container fade-in">
    <div class="table-header">
        <h3>📅 Events & Workshops (<?php echo count($allEvents); ?>)</h3>
        <div class="table-actions">
            <span class="table-info">Manage your events</span>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="eventsTable">
            <thead>
                <tr>
                    <th>📅 Event</th>
                    <th>🏷️ Type</th>
                    <th>📅 Date & Time</th>
                    <th>📍 Location</th>
                    <th>👥 Participants</th>
                    <th>💰 Price</th>
                    <th>🏷️ Status</th>
                    <th>⚙️ Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allEvents as $event): ?>
                <tr data-status="<?php echo $event['status']; ?>" data-type="<?php echo $event['event_type']; ?>">
                    <td>
                        <div class="event-title">
                            <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                            <?php if ($event['featured_image']): ?>
                                <span class="has-image">🖼️</span>
                            <?php endif; ?>
                        </div>
                        <div class="event-description">
                            <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . (strlen($event['description']) > 100 ? '...' : ''); ?>
                        </div>
                    </td>
                    <td>
                        <div class="type-badge">
                            <?php echo htmlspecialchars($event['event_type']); ?>
                        </div>
                    </td>
                    <td>
                        <div class="datetime-info">
                            <div class="date"><?php echo date('M j, Y', strtotime($event['date'])); ?></div>
                            <div class="time"><?php echo $event['time']; ?></div>
                            <div class="duration"><?php echo $event['duration']; ?> min</div>
                        </div>
                    </td>
                    <td>
                        <div class="location-info">
                            <?php echo htmlspecialchars($event['location']); ?>
                        </div>
                    </td>
                    <td>
                        <div class="participants-info">
                            <div class="current"><?php echo $event['registered_participants'] ?? 0; ?></div>
                            <div class="max">/ <?php echo $event['max_participants']; ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="price-info">
                            <?php if ($event['price'] == 0): ?>
                                <span class="free">Free</span>
                            <?php else: ?>
                                ₹<?php echo number_format($event['price']); ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge <?php echo $event['status']; ?>">
                            <?php echo ucfirst($event['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="editEvent(<?php echo $event['id']; ?>)">
                                ✏️ Edit
                            </button>
                            <button class="btn btn-sm btn-secondary" onclick="viewEvent(<?php echo $event['id']; ?>)">
                                👁️ View
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                🗑️ Delete
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Event Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3 id="modalTitle">📅 Create New Event</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        
        <form method="POST" class="modal-body">
            <input type="hidden" name="event_id" id="eventId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Event Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="event_type">Event Type *</label>
                    <select id="event_type" name="event_type" class="form-control" required>
                        <option value="">Select Type</option>
                        <?php foreach ($eventTypes as $type): ?>
                            <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Describe your event..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Date *</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="time">Time *</label>
                    <input type="time" id="time" name="time" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="duration">Duration (minutes) *</label>
                    <input type="number" id="duration" name="duration" class="form-control" required min="15" max="480">
                </div>
                
                <div class="form-group">
                    <label for="max_participants">Max Participants *</label>
                    <input type="number" id="max_participants" name="max_participants" class="form-control" required min="1">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-control" required placeholder="Physical location or 'Online'">
                </div>
                
                <div class="form-group">
                    <label for="instructor">Instructor *</label>
                    <input type="text" id="instructor" name="instructor" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (₹) *</label>
                    <input type="number" id="price" name="price" class="form-control" required min="0" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="registration_link">Registration Link</label>
                <input type="url" id="registration_link" name="registration_link" class="form-control" placeholder="https://example.com/register">
            </div>
            
            <div class="form-group">
                <label for="featured_image">Featured Image URL</label>
                <input type="url" id="featured_image" name="featured_image" class="form-control" placeholder="https://example.com/image.jpg">
            </div>
            
            <div class="form-group">
                <label for="requirements">Requirements</label>
                <textarea id="requirements" name="requirements" class="form-control" rows="3" placeholder="What participants need to bring or prepare..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="what_you_learn">What You'll Learn</label>
                <textarea id="what_you_learn" name="what_you_learn" class="form-control" rows="3" placeholder="Key takeaways and learning outcomes..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" name="create_event" id="submitBtn" class="btn btn-primary">Create Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🗑️ Delete Event</h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this event? This action cannot be undone.</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="event_id" id="deleteEventId">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="submit" form="deleteForm" name="delete_event" class="btn btn-danger">Delete Event</button>
        </div>
    </div>
</div>

<style>
/* Events Management Styles */
.page-actions {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.actions-left, .actions-right {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select, .search-input {
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.9em;
    transition: all 0.3s ease;
}

.filter-select:focus, .search-input:focus {
    outline: none;
    border-color: #1A535C;
    box-shadow: 0 0 0 3px rgba(26, 83, 92, 0.1);
}

.search-input {
    min-width: 250px;
}

.data-table-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.table-header {
    padding: 20px 25px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h3 {
    color: #1A535C;
    margin: 0;
    font-size: 1.3em;
}

.table-info {
    color: #666;
    font-size: 0.9em;
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e9ecef;
}

.data-table td {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: top;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.event-title {
    margin-bottom: 5px;
}

.event-title strong {
    color: #1A535C;
}

.has-image {
    margin-left: 8px;
    font-size: 0.8em;
}

.event-description {
    color: #666;
    font-size: 0.9em;
    line-height: 1.4;
}

.type-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    display: inline-block;
}

.datetime-info {
    font-size: 0.9em;
}

.datetime-info .date {
    font-weight: 600;
    color: #1A535C;
}

.datetime-info .time {
    color: #666;
}

.datetime-info .duration {
    color: #999;
    font-size: 0.8em;
}

.location-info {
    color: #666;
    font-size: 0.9em;
}

.participants-info {
    display: flex;
    align-items: center;
    font-size: 0.9em;
}

.participants-info .current {
    color: #1A535C;
    font-weight: 600;
}

.participants-info .max {
    color: #666;
}

.price-info {
    font-size: 0.9em;
}

.price-info .free {
    color: #28a745;
    font-weight: 600;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
}

.status-badge.upcoming {
    background: #e3f2fd;
    color: #1976d2;
}

.status-badge.ongoing {
    background: #e8f5e8;
    color: #388e3c;
}

.status-badge.completed {
    background: #f3e5f5;
    color: #7b1fa2;
}

.status-badge.cancelled {
    background: #ffebee;
    color: #c62828;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85em;
}

.btn-primary {
    background: #1A535C;
    color: white;
}

.btn-primary:hover {
    background: #0d3a40;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    transform: translateY(-1px);
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.8em;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: white;
    margin: 2% auto;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.modal-content.large {
    max-width: 800px;
}

.modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    color: #1A535C;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5em;
    cursor: pointer;
    color: #666;
    padding: 5px;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: #f0f0f0;
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
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
    .page-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .actions-left, .actions-right {
        justify-content: center;
    }
    
    .search-input {
        min-width: 200px;
    }
    
    .data-table {
        font-size: 0.8em;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        width: 95%;
        margin: 5% auto;
    }
}
</style>

<script>
// Events management JavaScript
let eventsData = <?php echo json_encode($allEvents); ?>;

document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeSearch();
});

function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterEvents);
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', filterEvents);
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterEvents);
    }
}

function filterEvents() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('#eventsTable tbody tr');
    
    rows.forEach(row => {
        const status = row.dataset.status;
        const type = row.dataset.type;
        const title = row.querySelector('.event-title strong').textContent.toLowerCase();
        const description = row.querySelector('.event-description').textContent.toLowerCase();
        
        let show = true;
        
        if (statusFilter && status !== statusFilter) show = false;
        if (typeFilter && type !== typeFilter) show = false;
        if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm)) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

function showCreateModal() {
    document.getElementById('modalTitle').textContent = '📅 Create New Event';
    document.getElementById('submitBtn').textContent = 'Create Event';
    document.getElementById('submitBtn').name = 'create_event';
    document.getElementById('eventId').value = '';
    document.querySelector('form').reset();
    document.getElementById('eventModal').style.display = 'block';
}

function editEvent(eventId) {
    const event = eventsData.find(e => e.id == eventId);
    if (!event) return;
    
    document.getElementById('modalTitle').textContent = '✏️ Edit Event';
    document.getElementById('submitBtn').textContent = 'Update Event';
    document.getElementById('submitBtn').name = 'update_event';
    document.getElementById('eventId').value = eventId;
    
    // Fill form with event data
    document.getElementById('title').value = event.title;
    document.getElementById('event_type').value = event.event_type;
    document.getElementById('description').value = event.description;
    document.getElementById('date').value = event.date;
    document.getElementById('time').value = event.time;
    document.getElementById('duration').value = event.duration;
    document.getElementById('location').value = event.location;
    document.getElementById('max_participants').value = event.max_participants;
    document.getElementById('price').value = event.price;
    document.getElementById('registration_link').value = event.registration_link || '';
    document.getElementById('featured_image').value = event.featured_image || '';
    document.getElementById('status').value = event.status;
    document.getElementById('instructor').value = event.instructor;
    document.getElementById('requirements').value = event.requirements || '';
    document.getElementById('what_you_learn').value = event.what_you_learn || '';
    
    document.getElementById('eventModal').style.display = 'block';
}

function viewEvent(eventId) {
    const event = eventsData.find(e => e.id == eventId);
    if (event) {
        alert('Event: ' + event.title + '\nDate: ' + event.date + '\nLocation: ' + event.location);
    }
}

function deleteEvent(eventId) {
    document.getElementById('deleteEventId').value = eventId;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('eventModal').style.display = 'none';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function exportEvents() {
    const csvContent = "data:text/csv;charset=utf-8," + 
        "Title,Type,Date,Time,Location,Max Participants,Price,Status\n" +
        eventsData.map(event => 
            `"${event.title}","${event.event_type}","${event.date}","${event.time}","${event.location}","${event.max_participants}","${event.price}","${event.status}"`
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "events_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const eventModal = document.getElementById('eventModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === eventModal) {
        closeModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
}
</script>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>
