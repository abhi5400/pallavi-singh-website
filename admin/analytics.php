<?php
/**
 * Analytics - Pallavi Singh Coaching
 * View detailed analytics and reports
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

// Get analytics data
try {
    $db = Database::getInstance();
    
    // Get counts
    $contactCount = $db->count('contact_submissions');
    $bookingCount = $db->count('booking_submissions');
    $journeyCount = $db->count('journey_submissions');
    $newsletterCount = $db->count('newsletter_subscriptions', ['status' => 'active']);
    
    // Get analytics data
    $analyticsData = $db->getData('analytics');
    
    // Calculate monthly data
    $currentMonth = date('Y-m');
    $lastMonth = date('Y-m', strtotime('-1 month'));
    
    $currentMonthContacts = $db->count('contact_submissions', ['submission_date' => $currentMonth]);
    $lastMonthContacts = $db->count('contact_submissions', ['submission_date' => $lastMonth]);
    
    $currentMonthBookings = $db->count('booking_submissions', ['submission_date' => $currentMonth]);
    $lastMonthBookings = $db->count('booking_submissions', ['submission_date' => $lastMonth]);
    
    // Calculate growth percentages
    $contactGrowth = $lastMonthContacts > 0 ? (($currentMonthContacts - $lastMonthContacts) / $lastMonthContacts) * 100 : 0;
    $bookingGrowth = $lastMonthBookings > 0 ? (($currentMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100 : 0;
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Analytics';
$pageSubtitle = 'Detailed insights and performance metrics';

// Create page content
ob_start();
?>

<?php if (isset($error)): ?>
    <div class="error-message fade-in">
        ⚠️ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Analytics Overview -->
<div class="analytics-overview fade-in">
    <div class="analytics-grid">
        <div class="analytics-card">
            <div class="analytics-header">
                <h3>📊 Overall Performance</h3>
                <span class="analytics-period">Last 30 days</span>
            </div>
            <div class="analytics-metrics">
                <div class="metric-item">
                    <div class="metric-label">Total Contacts</div>
                    <div class="metric-value"><?php echo $contactCount; ?></div>
                    <div class="metric-change positive">+<?php echo $currentMonthContacts; ?> this month</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Total Bookings</div>
                    <div class="metric-value"><?php echo $bookingCount; ?></div>
                    <div class="metric-change positive">+<?php echo $currentMonthBookings; ?> this month</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Journey Signups</div>
                    <div class="metric-value"><?php echo $journeyCount; ?></div>
                    <div class="metric-change">Active participants</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Newsletter Subscribers</div>
                    <div class="metric-value"><?php echo $newsletterCount; ?></div>
                    <div class="metric-change">Growing community</div>
                </div>
            </div>
        </div>
        
        <div class="analytics-card">
            <div class="analytics-header">
                <h3>📈 Conversion Rates</h3>
                <span class="analytics-period">Current month</span>
            </div>
            <div class="conversion-metrics">
                <div class="conversion-item">
                    <div class="conversion-label">Contact to Booking</div>
                    <div class="conversion-bar">
                        <div class="conversion-fill" style="width: 25%"></div>
                    </div>
                    <div class="conversion-value">25%</div>
                </div>
                <div class="conversion-item">
                    <div class="conversion-label">Booking to Journey</div>
                    <div class="conversion-bar">
                        <div class="conversion-fill" style="width: 40%"></div>
                    </div>
                    <div class="conversion-value">40%</div>
                </div>
                <div class="conversion-item">
                    <div class="conversion-label">Newsletter Engagement</div>
                    <div class="conversion-bar">
                        <div class="conversion-fill" style="width: 60%"></div>
                    </div>
                    <div class="conversion-value">60%</div>
                </div>
            </div>
        </div>
        
        <div class="analytics-card">
            <div class="analytics-header">
                <h3>⏱️ Response Times</h3>
                <span class="analytics-period">Average</span>
            </div>
            <div class="response-metrics">
                <div class="response-item">
                    <div class="response-icon">📧</div>
                    <div class="response-content">
                        <div class="response-label">Contact Forms</div>
                        <div class="response-value">2.3 hours</div>
                    </div>
                </div>
                <div class="response-item">
                    <div class="response-icon">📅</div>
                    <div class="response-content">
                        <div class="response-label">Booking Requests</div>
                        <div class="response-value">1.8 hours</div>
                    </div>
                </div>
                <div class="response-item">
                    <div class="response-icon">🌟</div>
                    <div class="response-content">
                        <div class="response-label">Journey Signups</div>
                        <div class="response-value">4.2 hours</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="analytics-card">
            <div class="analytics-header">
                <h3>⭐ Client Satisfaction</h3>
                <span class="analytics-period">Overall rating</span>
            </div>
            <div class="satisfaction-metrics">
                <div class="satisfaction-score">
                    <div class="score-number">4.8</div>
                    <div class="score-label">out of 5</div>
                </div>
                <div class="satisfaction-breakdown">
                    <div class="rating-item">
                        <span class="rating-label">Excellent (5★)</span>
                        <span class="rating-percentage">75%</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-label">Good (4★)</span>
                        <span class="rating-percentage">20%</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-label">Average (3★)</span>
                        <span class="rating-percentage">5%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Reports -->
<div class="reports-section fade-in">
    <h3>📋 Quick Reports</h3>
    <div class="reports-grid">
        <div class="report-card">
            <div class="report-icon">📊</div>
            <div class="report-content">
                <h4>Monthly Summary</h4>
                <p>Complete overview of this month's performance</p>
                <button class="btn btn-primary">Generate Report</button>
            </div>
        </div>
        
        <div class="report-card">
            <div class="report-icon">📈</div>
            <div class="report-content">
                <h4>Growth Analysis</h4>
                <p>Track growth trends and patterns</p>
                <button class="btn btn-primary">View Trends</button>
            </div>
        </div>
        
        <div class="report-card">
            <div class="report-icon">👥</div>
            <div class="report-content">
                <h4>Client Demographics</h4>
                <p>Understand your client base better</p>
                <button class="btn btn-primary">View Demographics</button>
            </div>
        </div>
        
        <div class="report-card">
            <div class="report-icon">📧</div>
            <div class="report-content">
                <h4>Email Performance</h4>
                <p>Newsletter and campaign analytics</p>
                <button class="btn btn-primary">View Performance</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Analytics Page Styles */
.analytics-overview {
    margin-bottom: 30px;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.analytics-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    padding: 25px;
}

.analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.analytics-header h3 {
    color: #1A535C;
    margin: 0;
    font-size: 1.2em;
}

.analytics-period {
    color: #666;
    font-size: 0.9em;
    background: #f8f9fa;
    padding: 4px 12px;
    border-radius: 15px;
}

.analytics-metrics {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.metric-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
}

.metric-label {
    color: #666;
    font-weight: 500;
}

.metric-value {
    color: #1A535C;
    font-weight: 700;
    font-size: 1.3em;
}

.metric-change {
    font-size: 0.8em;
    color: #4ECDC4;
    font-weight: 600;
}

.metric-change.positive {
    color: #28a745;
}

.conversion-metrics {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.conversion-item {
    display: flex;
    align-items: center;
    gap: 15px;
}

.conversion-label {
    min-width: 120px;
    color: #666;
    font-size: 0.9em;
}

.conversion-bar {
    flex: 1;
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.conversion-fill {
    height: 100%;
    background: linear-gradient(90deg, #1A535C, #4ECDC4);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.conversion-value {
    min-width: 40px;
    color: #1A535C;
    font-weight: 700;
    text-align: right;
}

.response-metrics {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.response-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
}

.response-icon {
    font-size: 1.5em;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 50%;
}

.response-content {
    flex: 1;
}

.response-label {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 3px;
}

.response-value {
    color: #1A535C;
    font-weight: 700;
    font-size: 1.1em;
}

.satisfaction-metrics {
    text-align: center;
}

.satisfaction-score {
    margin-bottom: 20px;
}

.score-number {
    font-size: 3em;
    font-weight: 800;
    color: #1A535C;
    line-height: 1;
}

.score-label {
    color: #666;
    font-size: 0.9em;
}

.satisfaction-breakdown {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.rating-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
}

.rating-label {
    color: #666;
    font-size: 0.9em;
}

.rating-percentage {
    color: #1A535C;
    font-weight: 600;
}

.reports-section {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    padding: 25px;
}

.reports-section h3 {
    color: #1A535C;
    margin-bottom: 20px;
    font-size: 1.3em;
}

.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.report-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.report-card:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.report-icon {
    font-size: 2em;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.report-content {
    flex: 1;
}

.report-content h4 {
    color: #1A535C;
    margin: 0 0 5px 0;
    font-size: 1.1em;
}

.report-content p {
    color: #666;
    margin: 0 0 10px 0;
    font-size: 0.9em;
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
    .analytics-grid {
        grid-template-columns: 1fr;
    }
    
    .reports-grid {
        grid-template-columns: 1fr;
    }
    
    .conversion-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .conversion-label {
        min-width: auto;
    }
}
</style>

<script>
// Analytics page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Animate conversion bars
    const conversionBars = document.querySelectorAll('.conversion-fill');
    conversionBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
});

function generateReport(type) {
    alert('Generating ' + type + ' report...');
}
</script>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>
