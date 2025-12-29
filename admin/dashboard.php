<?php
/**
 * Admin Dashboard - Pallavi Singh Coaching
 * Comprehensive dashboard with analytics and management tools
 */

require_once '../config/database.php';

// Check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get form submissions data
try {
    $db = Database::getInstance();
    
    // Get counts
    $contactCount = $db->count('contact_submissions');
    $joinCount = $db->count('join_submissions');
    $newsletterCount = $db->count('newsletter_subscriptions', 'status = ?', ['active']);
    
    // Get recent submissions
    $recentContacts = $db->select('contact_submissions', '', [], 'submission_date DESC', '5');
    $recentJoins = $db->select('join_submissions', '', [], 'submission_date DESC', '5');
    
    // Calculate growth metrics
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    $todayContacts = $db->count('contact_submissions', 'DATE(submission_date) = ?', [$today]);
    $todayJoins = $db->count('join_submissions', 'DATE(submission_date) = ?', [$today]);
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Dashboard';
$pageSubtitle = 'Welcome back! Here\'s your coaching business overview.';

// Create dashboard content
ob_start();
?>

<?php if (isset($error)): ?>
    <div class="error-message fade-in">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Stats Overview -->
<div class="dashboard-stats fade-in">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
        <div class="stat-number"><?php echo $contactCount; ?></div>
        <div class="stat-label">Contact Submissions</div>
        <div class="stat-change positive">+<?php echo $todayContacts; ?> today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-handshake"></i></div>
        <div class="stat-number"><?php echo $joinCount; ?></div>
        <div class="stat-label">Join Submissions</div>
        <div class="stat-change positive">+<?php echo $todayJoins; ?> today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
        <div class="stat-number"><?php echo $newsletterCount; ?></div>
        <div class="stat-label">Newsletter Subscribers</div>
        <div class="stat-change">Growing community</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions fade-in">
    <h3>Quick Actions</h3>
    <div class="action-buttons">
        <a href="contact-forms.php" class="action-btn">
            <div class="action-icon"><i class="fas fa-envelope"></i></div>
            <div class="action-text">View Contacts</div>
        </a>
        <a href="users.php" class="action-btn">
            <div class="action-icon"><i class="fas fa-users"></i></div>
            <div class="action-text">View Submissions</div>
        </a>
        <a href="analytics.php" class="action-btn">
            <div class="action-icon"><i class="fas fa-chart-line"></i></div>
            <div class="action-text">View Analytics</div>
        </a>
        <a href="settings.php" class="action-btn">
            <div class="action-icon"><i class="fas fa-cog"></i></div>
            <div class="action-text">Settings</div>
        </a>
    </div>
</div>

<!-- Recent Activity -->
<div class="dashboard-grid">
    <div class="dashboard-widget fade-in">
        <div class="widget-header">
            <h3>Recent Contact Submissions</h3>
            <a href="contact-forms.php" class="widget-link">View All</a>
        </div>
        <div class="widget-content">
            <?php if (!empty($recentContacts)): ?>
                <div class="activity-list">
                    <?php foreach ($recentContacts as $contact): ?>
                    <div class="activity-item">
                        <div class="activity-icon"><i class="fas fa-envelope"></i></div>
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($contact['name']); ?></div>
                            <div class="activity-subtitle"><?php echo htmlspecialchars($contact['email']); ?></div>
                            <div class="activity-meta">
                                <?php echo $contact['service_interest'] ? ucfirst(str_replace('-', ' ', $contact['service_interest'])) : 'General inquiry'; ?>
                                • <?php echo date('M j, H:i', strtotime($contact['submission_date'])); ?>
                            </div>
                        </div>
                        <div class="activity-status">
                            <span class="status-badge <?php echo $contact['status']; ?>"><?php echo ucfirst($contact['status']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-widget">
                    <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                    <div class="empty-text">No contact submissions yet</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="dashboard-widget fade-in">
        <div class="widget-header">
            <h3>Recent Join Submissions</h3>
            <a href="users.php" class="widget-link">View All</a>
        </div>
        <div class="widget-content">
            <?php if (!empty($recentJoins)): ?>
                <div class="activity-list">
                    <?php foreach ($recentJoins as $join): ?>
                    <div class="activity-item">
                        <div class="activity-icon"><i class="fas fa-handshake"></i></div>
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($join['full_name']); ?></div>
                            <div class="activity-subtitle"><?php echo htmlspecialchars($join['email']); ?></div>
                            <div class="activity-meta">
                                <?php echo htmlspecialchars($join['city'] . ', ' . $join['state']); ?>
                                • <?php echo date('M j, H:i', strtotime($join['submission_date'])); ?>
                            </div>
                        </div>
                        <div class="activity-status">
                            <span class="status-badge <?php echo $join['status']; ?>"><?php echo ucfirst($join['status']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-widget">
                    <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="empty-text">No join submissions yet</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="dashboard-widget fade-in">
        <div class="widget-header">
            <h3>Analytics Overview</h3>
            <a href="analytics.php" class="widget-link">View Details</a>
        </div>
        <div class="widget-content">
            <div class="analytics-summary">
                <div class="analytics-item">
                    <div class="analytics-label">Conversion Rate</div>
                    <div class="analytics-value">12.5%</div>
                </div>
                <div class="analytics-item">
                    <div class="analytics-label">Avg Response Time</div>
                    <div class="analytics-value">2.3 hours</div>
                </div>
                <div class="analytics-item">
                    <div class="analytics-label">Client Satisfaction</div>
                    <div class="analytics-value">4.8/5</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Specific Styles */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    text-align: left;
    transition: box-shadow 0.2s ease;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #4299e1;
}

.stat-card:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-icon {
    font-size: 1.5rem;
    margin-bottom: 12px;
    color: #4299e1;
}

.stat-icon i {
    font-size: 1.5rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 4px;
}

.stat-label {
    color: #718096;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 8px;
}

.stat-change {
    font-size: 0.875rem;
    color: #48bb78;
    font-weight: 500;
}

.stat-change.positive {
    color: #48bb78;
}

.quick-actions {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e2e8f0;
    margin-bottom: 24px;
}

.quick-actions h3 {
    color: #1a202c;
    margin-bottom: 16px;
    font-size: 1.25rem;
    font-weight: 600;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: #f7fafc;
    border-radius: 6px;
    text-decoration: none;
    color: #2d3748;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}

.action-btn:hover {
    background: #4299e1;
    color: white;
    border-color: #4299e1;
}

.action-icon {
    font-size: 1.5rem;
    margin-bottom: 8px;
}

.action-icon i {
    font-size: 1.5rem;
}

.action-text {
    font-weight: 500;
    font-size: 0.875rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.dashboard-widget {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.widget-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f7fafc;
}

.widget-header h3 {
    color: #1a202c;
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.widget-link {
    color: #4299e1;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
}

.widget-link:hover {
    text-decoration: underline;
}

.widget-content {
    padding: 20px 25px;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f7fafc;
    border-radius: 6px;
    transition: background-color 0.2s ease;
    border: 1px solid #e2e8f0;
}

.activity-item:hover {
    background: #edf2f7;
}

.activity-icon {
    font-size: 1rem;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    color: #4299e1;
}

.activity-icon i {
    font-size: 1rem;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 3px;
}

.activity-subtitle {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 3px;
}

.activity-meta {
    color: #999;
    font-size: 0.8em;
}

.activity-status {
    margin-left: auto;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.new {
    background: #ebf8ff;
    color: #2c5282;
}

.status-badge.pending {
    background: #fffaf0;
    color: #c05621;
}

.status-badge.completed {
    background: #f0fff4;
    color: #22543d;
}

.empty-widget {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-icon {
    font-size: 2.5rem;
    margin-bottom: 12px;
    opacity: 0.4;
    color: #a0aec0;
}

.empty-icon i {
    font-size: 2.5rem;
}

.empty-text {
    font-size: 1.1em;
}

.analytics-summary {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.analytics-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f7fafc;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}

.analytics-label {
    color: #666;
    font-weight: 500;
}

.analytics-value {
    color: #1a202c;
    font-weight: 600;
    font-size: 1.125rem;
}

.error-message {
    background: #fed7d7;
    color: #c53030;
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    border-left: 4px solid #e53e3e;
    font-weight: 500;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .dashboard-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .dashboard-stats {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>