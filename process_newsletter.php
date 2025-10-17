<?php
/**
 * Newsletter Subscription Processing - Pallavi Singh Coaching
 * Handles newsletter subscription form submission
 */

require_once 'config/database.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Initialize response
$response = ['success' => false, 'message' => ''];

try {
    // Get and sanitize form data
    $email = Database::sanitizeInput($_POST['email'] ?? '');
    $firstName = Database::sanitizeInput($_POST['first_name'] ?? '');
    $lastName = Database::sanitizeInput($_POST['last_name'] ?? '');
    
    // Validate required fields
    $errors = [];
    
    if (empty($email) || !Database::validateEmail($email)) {
        $errors[] = 'Valid email address is required';
    }
    
    if (!empty($errors)) {
        $response['message'] = implode(', ', $errors);
        echo json_encode($response);
        exit;
    }
    
    // Get database instance
    $db = Database::getInstance();
    
    // Check if email already exists
    $existingSubscriptions = $db->where('newsletter_subscriptions', ['email' => $email]);
    
    if (!empty($existingSubscriptions)) {
        $existing = $existingSubscriptions[0];
        if ($existing['status'] === 'active') {
            $response['message'] = 'This email is already subscribed to our newsletter.';
            echo json_encode($response);
            exit;
        } else {
            // Reactivate existing subscription
            $db->update('newsletter_subscriptions', 
                ['status' => 'active', 'first_name' => $firstName, 'last_name' => $lastName], 
                'email = ?', 
                [$email]
            );
            $response['success'] = true;
            $response['message'] = 'Welcome back! Your newsletter subscription has been reactivated.';
        }
    } else {
        // Create new subscription
        $subscriptionData = [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'source' => 'website',
            'submission_date' => date('Y-m-d H:i:s'),
            'ip_address' => Database::getClientIP(),
            'status' => 'active',
            'unsubscribe_token' => bin2hex(random_bytes(32))
        ];
        
        $subscriptionId = $db->insert('newsletter_subscriptions', $subscriptionData);
        
        if ($subscriptionId) {
            $response['success'] = true;
            $response['message'] = 'Thank you for subscribing! You\'ll receive our latest updates and insights.';
        } else {
            $response['message'] = 'Failed to subscribe. Please try again.';
        }
    }
    
    // Send welcome email if successful
    if ($response['success']) {
        sendNewsletterWelcomeEmail($email, $firstName, $lastName);
    }
    
} catch (Exception $e) {
    error_log("Newsletter subscription error: " . $e->getMessage());
    $response['message'] = 'An error occurred while subscribing. Please try again.';
}

echo json_encode($response);

/**
 * Send welcome email for newsletter subscription
 */
function sendNewsletterWelcomeEmail($email, $firstName, $lastName) {
    $subject = "Welcome to Pallavi Singh's Newsletter!";
    
    $displayName = trim($firstName . ' ' . $lastName) ?: 'Friend';
    
    $messageContent = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #1A535C, #4ECDC4); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: white; padding: 30px; border: 1px solid #ddd; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
            .highlight { color: #1A535C; font-weight: bold; }
            .cta-button { display: inline-block; background: #1A535C; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to Our Community!</h1>
                <p>Your transformation journey starts here</p>
            </div>
            
            <div class='content'>
                <p>Dear <span class='highlight'>{$displayName}</span>,</p>
                
                <p>Welcome to Pallavi Singh's newsletter community! I'm thrilled that you've joined us on this journey of growth, healing, and transformation.</p>
                
                <p><strong>What you can expect:</strong></p>
                <ul>
                    <li>Weekly insights on personal growth and storytelling</li>
                    <li>Exclusive tips for overcoming life's challenges</li>
                    <li>Early access to new coaching programs and workshops</li>
                    <li>Inspiring stories from our community members</li>
                    <li>Special offers and resources just for subscribers</li>
                </ul>
                
                <p>I believe that everyone has a unique story to tell and the power to transform their life. Through our newsletter, I'll share practical tools, inspiring stories, and actionable insights to help you on your journey.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='#' class='cta-button'>Explore My Services</a>
                </div>
                
                <p>If you have any questions or would like to share your story, feel free to reply to this email. I love hearing from our community members!</p>
                
                <p>With warm regards,<br>
                <strong>Pallavi Singh</strong><br>
                Life Coach & Storyteller</p>
            </div>
            
            <div class='footer'>
                <p>This email was sent to {$email}</p>
                <p>Pallavi Singh Coaching | Transform Your Life Through Storytelling</p>
                <p><small>You can unsubscribe at any time by clicking the link in this email.</small></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $messageContent, true);
}

/**
 * Send email function (simplified for this system)
 */
function sendEmail($to, $subject, $message, $isHtml = false) {
    // For development, we'll log emails instead of sending them
    // In production, integrate with SMTP service like PHPMailer
    
    $headers = [
        'From: ' . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'noreply@pallavisingh.com'),
        'Reply-To: ' . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'noreply@pallavisingh.com'),
        'X-Mailer: PHP/' . phpversion()
    ];
    
    if ($isHtml) {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=UTF-8';
    }
    
    // Log email for development purposes
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'to' => $to,
        'subject' => $subject,
        'message_preview' => substr(strip_tags($message), 0, 100) . '...',
        'headers' => $headers,
        'status' => 'logged'
    ];
    
    // Ensure data directory exists
    $dataDir = __DIR__ . '/data/';
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    $logFile = $dataDir . 'email_log.json';
    $existingLogs = [];
    if (file_exists($logFile)) {
        $existingLogs = json_decode(file_get_contents($logFile), true) ?: [];
    }
    $existingLogs[] = $logEntry;
    file_put_contents($logFile, json_encode($existingLogs, JSON_PRETTY_PRINT));
    
    // Return true to simulate successful sending
    return true;
}
?>
