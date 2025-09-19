<?php
/**
 * JSON Database Setup Script
 * Run this script to set up the JSON-based storage system
 */

require_once 'config/database_json.php';

echo "<h2>Pallavi Singh Coaching - JSON Database Setup</h2>";

try {
    $db = Database::getInstance();
    
    echo "<p>✓ JSON database system initialized</p>";
    
    // Create initial admin user
    $adminUsers = $db->getData('admin_users');
    if (empty($adminUsers)) {
        $adminUser = [
            'username' => 'admin',
            'email' => 'admin@pallavi-coaching.com',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'full_name' => 'Pallavi Singh',
            'role' => 'admin',
            'is_active' => true,
            'last_login' => null
        ];
        $db->insert('admin_users', $adminUser);
        echo "<p>✓ Default admin user created</p>";
    } else {
        echo "<p>✓ Admin users already exist</p>";
    }
    
    // Create sample data for testing
    $contactSubmissions = $db->getData('contact_submissions');
    if (empty($contactSubmissions)) {
        $sampleContacts = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'service_interest' => 'coaching',
                'message' => 'I am interested in life coaching sessions.',
                'submission_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'status' => 'new',
                'notes' => null
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'service_interest' => 'anxiety',
                'message' => 'I need help with anxiety management.',
                'submission_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'status' => 'contacted',
                'notes' => 'Initial contact made'
            ]
        ];
        
        foreach ($sampleContacts as $contact) {
            $db->insert('contact_submissions', $contact);
        }
        echo "<p>✓ Sample contact submissions created</p>";
    } else {
        echo "<p>✓ Contact submissions already exist</p>";
    }
    
    $bookingSubmissions = $db->getData('booking_submissions');
    if (empty($bookingSubmissions)) {
        $sampleBookings = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'alice@example.com',
                'service_type' => 'habit-mastery',
                'session_type' => 'discovery',
                'preferred_date' => date('Y-m-d', strtotime('+1 week')),
                'preferred_time' => 'morning',
                'timezone' => 'UTC+5:30',
                'goals' => 'I want to develop better daily habits and routines.',
                'experience_level' => 'some',
                'additional_notes' => 'Available on weekends',
                'terms_accepted' => true,
                'newsletter_subscription' => true,
                'submission_date' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'status' => 'pending',
                'scheduled_date' => null,
                'session_notes' => null
            ]
        ];
        
        foreach ($sampleBookings as $booking) {
            $db->insert('booking_submissions', $booking);
        }
        echo "<p>✓ Sample booking submissions created</p>";
    } else {
        echo "<p>✓ Booking submissions already exist</p>";
    }
    
    $journeySubmissions = $db->getData('journey_submissions');
    if (empty($journeySubmissions)) {
        $sampleJourneys = [
            [
                'name' => 'Bob Wilson',
                'age' => 28,
                'city' => 'Mumbai',
                'issue_challenge' => 'I struggle with public speaking and confidence.',
                'terms_accepted' => true,
                'submission_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'status' => 'new',
                'follow_up_notes' => null
            ]
        ];
        
        foreach ($sampleJourneys as $journey) {
            $db->insert('journey_submissions', $journey);
        }
        echo "<p>✓ Sample journey submissions created</p>";
    } else {
        echo "<p>✓ Journey submissions already exist</p>";
    }
    
    $newsletterSubscriptions = $db->getData('newsletter_subscriptions');
    if (empty($newsletterSubscriptions)) {
        $sampleNewsletters = [
            [
                'email' => 'subscriber1@example.com',
                'first_name' => 'Sarah',
                'last_name' => 'Davis',
                'source' => 'contact_form',
                'subscription_date' => date('Y-m-d H:i:s', strtotime('-2 weeks')),
                'ip_address' => '127.0.0.1',
                'status' => 'active',
                'unsubscribe_token' => generateUnsubscribeToken()
            ],
            [
                'email' => 'subscriber2@example.com',
                'first_name' => 'Mike',
                'last_name' => 'Brown',
                'source' => 'booking_form',
                'subscription_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'ip_address' => '127.0.0.1',
                'status' => 'active',
                'unsubscribe_token' => generateUnsubscribeToken()
            ]
        ];
        
        foreach ($sampleNewsletters as $newsletter) {
            $db->insert('newsletter_subscriptions', $newsletter);
        }
        echo "<p>✓ Sample newsletter subscriptions created</p>";
    } else {
        echo "<p>✓ Newsletter subscriptions already exist</p>";
    }
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>Your JSON-based database has been set up successfully. Here's what you need to know:</p>";
    echo "<ul>";
    echo "<li><strong>Admin Login:</strong> Username: 'admin', Password: 'admin123' - CHANGE THIS IMMEDIATELY!</li>";
    echo "<li><strong>Data Storage:</strong> All data is stored in JSON files in the 'data' directory</li>";
    echo "<li><strong>Admin Panel:</strong> <a href='admin/'>Access Admin Dashboard</a></li>";
    echo "<li><strong>Sample Data:</strong> Sample submissions have been created for testing</li>";
    echo "</ul>";
    
    echo "<h3>Security Recommendations:</h3>";
    echo "<ul>";
    echo "<li>Change the default admin password immediately</li>";
    echo "<li>Keep your PHP software updated</li>";
    echo "<li>Consider using HTTPS for your website</li>";
    echo "<li>Regularly backup your data directory</li>";
    echo "</ul>";
    
    echo "<p><strong>Admin Panel:</strong> <a href='admin/'>Access Admin Dashboard</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Setup failed: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h2, h3 { color: #1A535C; }
p { line-height: 1.6; }
ul { line-height: 1.8; }
a { color: #1A535C; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
