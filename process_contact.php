<?php
/**
 * Contact Form Processing - Pallavi Singh Coaching
 * Handles contact form submission and sends confirmation email
 */

require_once 'config/database_json.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON for AJAX responses
if (!headers_sent()) {
    header('Content-Type: application/json');
}

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
    $name = Database::sanitizeInput($_POST['name'] ?? '');
    $email = Database::sanitizeInput($_POST['email'] ?? '');
    $phone = Database::sanitizeInput($_POST['phone'] ?? '');
    $subject = Database::sanitizeInput($_POST['subject'] ?? '');
    $message = Database::sanitizeInput($_POST['message'] ?? '');
    
    // Validate required fields
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email address is required';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    } else if (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }
    
    if (!empty($errors)) {
        $response['message'] = implode(', ', $errors);
        echo json_encode($response);
        exit;
    }
    
    // Get database instance
    $db = JsonDatabase::getInstance();
    
    // Prepare data for storage
    $contactData = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'submission_date' => date('Y-m-d H:i:s'),
        'ip_address' => Database::getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'status' => 'new'
    ];
    
    // Save to database
    $contactId = $db->insert('contact_submissions', $contactData);
    
    if ($contactId) {
        // Send confirmation email
        $emailSent = sendContactConfirmationEmail($email, $name, $subject, $message);
        
        // Prepare success response
        $response['success'] = true;
        $response['message'] = 'Thank you for your message! I\'ll get back to you within 24 hours.';
        $response['contact_id'] = $contactId;
        
        // Log the submission
        error_log("Contact form submitted: ID {$contactId}, Email: {$email}, Name: {$name}, Subject: {$subject}");
        
    } else {
        $response['message'] = 'Failed to send your message. Please try again.';
    }
    
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    error_log("Contact form error trace: " . $e->getTraceAsString());
    $response['message'] = 'An error occurred while sending your message. Please try again.';
    $response['debug'] = $e->getMessage(); // Enable for debugging
    $response['trace'] = $e->getTraceAsString(); // Enable for debugging
}

echo json_encode($response);

/**
 * Send confirmation email
 */
function sendContactConfirmationEmail($email, $name, $subject, $message) {
    $subjectLine = "Thank you for contacting Pallavi Singh Coaching";
    
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
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Thank You for Reaching Out!</h1>
                <p>Your message has been received</p>
            </div>
            
            <div class='content'>
                <p>Dear <span class='highlight'>{$name}</span>,</p>
                
                <p>Thank you for taking the time to contact me! I've received your message and will respond within 24 hours.</p>
                
                <p><strong>Your Message Details:</strong></p>
                <ul>
                    <li><strong>Subject:</strong> " . ucfirst(str_replace('-', ' ', $subject)) . "</li>
                    <li><strong>Message:</strong> " . nl2br(htmlspecialchars($message)) . "</li>
                </ul>
                
                <p><strong>What happens next?</strong></p>
                <ul>
                    <li>I'll review your message carefully</li>
                    <li>I'll respond with personalized guidance</li>
                    <li>If you're interested in coaching, we can schedule a discovery call</li>
                </ul>
                
                <p>In the meantime, feel free to explore my website to learn more about my coaching services and approach.</p>
                
                <p>With warm regards,<br>
                <strong>Pallavi Singh</strong><br>
                Life Coach & Storyteller</p>
            </div>
            
            <div class='footer'>
                <p>This email was sent to {$email}</p>
                <p>Pallavi Singh Coaching | Transform Your Life Through Storytelling</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subjectLine, $messageContent, true);
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
