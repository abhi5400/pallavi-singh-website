<?php
/**
 * Join Form Processing - Pallavi Singh Coaching
 * Handles form submission and sends confirmation email
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
    $full_name = Database::sanitizeInput($_POST['full_name'] ?? '');
    $ageRaw = $_POST['age'] ?? '';
    $age = ($ageRaw === '' || $ageRaw === null) ? null : intval($ageRaw);
    $city = Database::sanitizeInput($_POST['city'] ?? '');
    $state = Database::sanitizeInput($_POST['state'] ?? '');
    $contact_number = Database::sanitizeInput($_POST['contact_number'] ?? '');
    $email = Database::sanitizeInput($_POST['email'] ?? '');
    $issue_challenge = Database::sanitizeInput($_POST['issue_challenge'] ?? '');
    $goals = Database::sanitizeInput($_POST['goals'] ?? '');
    $terms_accepted = isset($_POST['terms_accepted']);
    $newsletter_subscription = isset($_POST['newsletter_subscription']);
    
    // Validate required fields
    $errors = [];
    
    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    }
    
    // Age is optional; validate only if provided
    if ($age !== null) {
        if ($age < 1 || $age > 150) {
            $errors[] = 'Age must be between 1 and 150';
        }
    }
    
    if (empty($city)) {
        $errors[] = 'City is required';
    }
    
    if (empty($state)) {
        $errors[] = 'State/Province is required';
    }
    
    if (empty($contact_number)) {
        $errors[] = 'Contact number is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email address is required';
    }
    
    if (empty($issue_challenge)) {
        $errors[] = 'Please describe the challenge or issue you are facing';
    }
    
    if (!$terms_accepted) {
        $errors[] = 'You must accept the terms and conditions';
    }
    
    if (!empty($errors)) {
        $response['message'] = implode(', ', $errors);
        echo json_encode($response);
        exit;
    }
    
    // Get database instance
    $db = JsonDatabase::getInstance();
    
    // Generate unique form ID
    $form_id = 'JOIN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    
    // Prepare data for storage
    $joinData = [
        'form_id' => $form_id,
        'full_name' => $full_name,
        'age' => $age,
        'city' => $city,
        'state' => $state,
        'contact_number' => $contact_number,
        'email' => $email,
        'issue_challenge' => $issue_challenge,
        'goals' => $goals,
        'terms_accepted' => $terms_accepted,
        'newsletter_subscription' => $newsletter_subscription,
        'submission_date' => date('Y-m-d H:i:s'),
        'ip_address' => Database::getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'status' => 'new'
    ];
    
    // Save to database
    $joinId = $db->insert('join_submissions', $joinData);
    
    if ($joinId) {
        // If newsletter subscription is selected, add to newsletter
        if ($newsletter_subscription) {
            $newsletterData = [
                'email' => $email,
                'first_name' => explode(' ', $full_name)[0],
                'last_name' => implode(' ', array_slice(explode(' ', $full_name), 1)),
                'source' => 'join_form',
                'subscription_date' => date('Y-m-d H:i:s'),
                'ip_address' => Database::getClientIP(),
                'status' => 'active',
                'unsubscribe_token' => bin2hex(random_bytes(16))
            ];
            
            // Check if email already exists in newsletter
            $existingNewsletter = $db->where('newsletter_subscriptions', ['email' => $email]);
            if (empty($existingNewsletter)) {
                $db->insert('newsletter_subscriptions', $newsletterData);
            }
        }
        
        // Send confirmation email
        $emailSent = sendConfirmationEmail($email, $full_name, $form_id, $issue_challenge);
        
        // Prepare success response
        $response['success'] = true;
        $response['message'] = 'Thank you for joining! Your form has been submitted successfully.';
        $response['form_id'] = $form_id;
        $response['redirect_url'] = 'thank_you.php?id=' . $form_id;
        
        // Log the submission
        error_log("Join form submitted: Form ID {$form_id}, Email: {$email}, Name: {$full_name}");
        
    } else {
        $response['message'] = 'Failed to save your information. Please try again.';
    }
    
} catch (Exception $e) {
    error_log("Join form error: " . $e->getMessage());
    error_log("Join form error file: " . $e->getFile());
    error_log("Join form error line: " . $e->getLine());
    error_log("Join form error trace: " . $e->getTraceAsString());
    $response['message'] = 'An error occurred while processing your request. Please try again.';
    // Remove debug info for production
    // $response['debug'] = [
    //     'error' => $e->getMessage(),
    //     'file' => $e->getFile(),
    //     'line' => $e->getLine()
    // ];
}

echo json_encode($response);

/**
 * Send confirmation email
 */
function sendConfirmationEmail($email, $name, $formId, $issue) {
    $subject = "Welcome to Pallavi Singh Coaching - Form Confirmation";
    
    $message = "
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
            .form-id { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: center; font-weight: bold; }
            .highlight { color: #1A535C; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to Your Transformation Journey!</h1>
                <p>Thank you for joining Pallavi Singh Coaching</p>
            </div>
            
            <div class='content'>
                <p>Dear <span class='highlight'>{$name}</span>,</p>
                
                <p>Thank you for taking the first step towards transforming your life! We're thrilled that you've joined our community.</p>
                
                <p><strong>Your Form Details:</strong></p>
                <div class='form-id'>
                    Form ID: <span class='highlight'>{$formId}</span>
                </div>
                
                <p><strong>Challenge you mentioned:</strong><br>
                " . nl2br(htmlspecialchars($issue)) . "</p>
                
                <p>We've received your information and will be in touch within 24-48 hours to discuss how we can support you on your journey.</p>
                
                <p><strong>What happens next?</strong></p>
                <ul>
                    <li>Our team will review your submission</li>
                    <li>We'll contact you to schedule a consultation</li>
                    <li>Together, we'll create a personalized plan for your growth</li>
                </ul>
                
                <p>If you have any questions, please don't hesitate to reach out to us.</p>
                
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
    
    return sendEmail($email, $subject, $message, true);
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
