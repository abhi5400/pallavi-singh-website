<?php
/**
 * Forms Overview - Pallavi Singh Coaching
 * Central hub for all form submissions
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

// Initialize variables with default values
$error = '';
$contactSubmissions = [];
$joinSubmissions = [];
$bookingSubmissions = [];
$journeySubmissions = [];
$waitlistSubscriptions = [];
$totalContacts = 0;
$totalJoins = 0;
$totalBookings = 0;
$totalJourneys = 0;
$totalWaitlist = 0;
$recentContacts = [];
$recentJoins = [];

// Get form submissions data
try {
    $db = JsonDatabase::getInstance();
    
    // Get all form data with null safety
    $contactSubmissions = $db->getData('contact_submissions') ?: [];
    $joinSubmissions = $db->getData('join_submissions') ?: [];
    $bookingSubmissions = $db->getData('booking_submissions') ?: [];
    $journeySubmissions = $db->getData('journey_submissions') ?: [];
    $waitlistSubscriptions = $db->getData('waitlist_subscriptions') ?: [];
    
    // Calculate totals
    $totalContacts = count($contactSubmissions);
    $totalJoins = count($joinSubmissions);
    $totalBookings = count($bookingSubmissions);
    $totalJourneys = count($journeySubmissions);
    $totalWaitlist = count($waitlistSubscriptions);
    
    // Get recent submissions (last 5 from each)
    $recentContacts = array_slice(array_reverse($contactSubmissions), 0, 5);
    $recentJoins = array_slice(array_reverse($joinSubmissions), 0, 5);
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Forms Overview';
$pageSubtitle = 'Manage all form submissions and inquiries';

// Create page content
ob_start();
?>

<?php if (isset($error)): ?>
    <div class="error-message fade-in">
        ⚠️ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Forms Stats Overview -->
<div class="forms-stats fade-in">
    <div class="stat-card">
        <div class="stat-icon">📧</div>
        <div class="stat-info">
            <h3><?php echo $totalContacts; ?></h3>
            <p>Contact Forms</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🤝</div>
        <div class="stat-info">
            <h3><?php echo $totalJoins; ?></h3>
            <p>Join Forms</p>
        </div>
    </div>
    
    
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
            <h3><?php echo $totalBookings; ?></h3>
            <p>Bookings</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">🌟</div>
        <div class="stat-info">
            <h3><?php echo $totalJourneys; ?></h3>
            <p>Journeys</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
            <h3><?php echo $totalWaitlist; ?></h3>
            <p>Waitlist</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="forms-actions fade-in">
    <h3>📋 Form Management</h3>
    <div class="action-grid">
        <a href="contact-forms.php" class="action-card">
            <div class="action-icon">📧</div>
            <h4>Contact Forms</h4>
            <p>View and manage contact form submissions</p>
            <span class="count"><?php echo $totalContacts; ?> submissions</span>
        </a>
        
        <a href="join-forms.php" class="action-card">
            <div class="action-icon">🤝</div>
            <h4>Join Forms</h4>
            <p>Manage "Join Now" form submissions</p>
            <span class="count"><?php echo $totalJoins; ?> submissions</span>
        </a>
        
        
        <a href="booking-forms.php" class="action-card">
            <div class="action-icon">📅</div>
            <h4>Booking Forms</h4>
            <p>View booking requests and appointments</p>
            <span class="count"><?php echo $totalBookings; ?> bookings</span>
        </a>
        
        <a href="journey-forms.php" class="action-card">
            <div class="action-icon">🌟</div>
            <h4>Journey Forms</h4>
            <p>Manage transformation journey submissions</p>
            <span class="count"><?php echo $totalJourneys; ?> journeys</span>
        </a>
        
        <a href="waitlist-forms.php" class="action-card">
            <div class="action-icon">⏳</div>
            <h4>Waitlist</h4>
            <p>Manage waitlist subscriptions</p>
            <span class="count"><?php echo $totalWaitlist; ?> waiting</span>
        </a>
    </div>
</div>

<!-- Recent Activity -->
<div class="recent-activity fade-in">
    <h3>🕒 Recent Submissions</h3>
    
    <div class="activity-tabs">
        <button class="tab-btn active" onclick="showTab('contacts')">Contacts</button>
        <button class="tab-btn" onclick="showTab('joins')">Join Forms</button>
    </div>
    
    <div class="activity-content">
        <div id="contacts-tab" class="tab-content active">
            <div class="submission-list">
                <?php foreach ($recentContacts as $contact): ?>
                <div class="submission-item">
                    <div class="submission-info">
                        <h4><?php echo htmlspecialchars($contact['name'] ?? 'N/A'); ?></h4>
                        <p><?php echo htmlspecialchars($contact['email'] ?? 'N/A'); ?></p>
                        <small><?php echo isset($contact['submission_date']) ? date('M j, Y H:i', strtotime($contact['submission_date'])) : 'N/A'; ?></small>
                    </div>
                    <div class="submission-status">
                        <span class="status-badge"><?php echo ucfirst($contact['status'] ?? 'new'); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div id="joins-tab" class="tab-content">
            <div class="submission-list">
                <?php foreach ($recentJoins as $join): ?>
                <div class="submission-item">
                    <div class="submission-info">
                        <h4><?php echo htmlspecialchars($join['full_name'] ?? 'N/A'); ?></h4>
                        <p><?php echo htmlspecialchars($join['email'] ?? 'N/A'); ?></p>
                        <small><?php echo isset($join['submission_date']) ? date('M j, Y H:i', strtotime($join['submission_date'])) : 'N/A'; ?></small>
                    </div>
                    <div class="submission-status">
                        <span class="status-badge"><?php echo ucfirst($join['status'] ?? 'new'); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    </div>
</div>

<style>
/* Forms Overview Styles */
.forms-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 2.5em;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1A535C, #4ECDC4);
    border-radius: 15px;
}

.stat-info h3 {
    font-size: 2em;
    margin: 0;
    color: #1A535C;
}

.stat-info p {
    margin: 0;
    color: #666;
    font-weight: 500;
}

.forms-actions {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.forms-actions h3 {
    margin: 0 0 25px 0;
    color: #1A535C;
    font-size: 1.5em;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.action-card {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 25px;
    border-radius: 15px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.action-card:hover {
    transform: translateY(-5px);
    border-color: #4ECDC4;
    box-shadow: 0 10px 25px rgba(78, 205, 196, 0.2);
}

.action-icon {
    font-size: 2.5em;
    margin-bottom: 15px;
}

.action-card h4 {
    margin: 0 0 10px 0;
    color: #1A535C;
    font-size: 1.2em;
}

.action-card p {
    margin: 0 0 15px 0;
    color: #666;
    font-size: 0.95em;
}

.count {
    background: linear-gradient(135deg, #1A535C, #4ECDC4);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
}

.recent-activity {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.recent-activity h3 {
    margin: 0 0 25px 0;
    color: #1A535C;
    font-size: 1.5em;
}

.activity-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}

.tab-btn {
    background: none;
    border: none;
    padding: 12px 24px;
    border-radius: 8px 8px 0 0;
    cursor: pointer;
    font-weight: 500;
    color: #666;
    transition: all 0.3s ease;
}

.tab-btn.active {
    background: linear-gradient(135deg, #1A535C, #4ECDC4);
    color: white;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.submission-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.submission-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #4ECDC4;
}

.submission-info h4 {
    margin: 0 0 5px 0;
    color: #1A535C;
    font-size: 1.1em;
}

.submission-info p {
    margin: 0 0 5px 0;
    color: #666;
    font-size: 0.95em;
}

.submission-info small {
    color: #999;
    font-size: 0.85em;
}

.status-badge {
    background: linear-gradient(135deg, #4ECDC4, #44A08D);
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: 600;
}
</style>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}
</script>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>
