<?php
/**
 * Waitlist Form Processing - The Story Tree Community
 * Handles waitlist form submission
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
    
    // Validate required fields
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !Database::validateEmail($email)) {
        $errors[] = 'Valid email address is required';
    }
    
    if (!empty($errors)) {
        $response['message'] = implode(', ', $errors);
        echo json_encode($response);
        exit;
    }
    
    // Get database instance
    $db = JsonDatabase::getInstance();
    
    // Generate unique waitlist ID
    $waitlist_id = 'WAIT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    
    // Prepare data for storage
    $waitlistData = [
        'waitlist_id' => $waitlist_id,
        'name' => $name,
        'email' => $email,
        'submission_date' => date('Y-m-d H:i:s'),
        'ip_address' => Database::getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'status' => 'active'
    ];
    
    // Save to database
    $waitlistId = $db->insert('waitlist_subscriptions', $waitlistData);
    
    if ($waitlistId) {
        // Prepare success response
        $response['success'] = true;
        $response['message'] = 'Thank you for joining our waitlist! We\'ll notify you when The Story Tree Community launches.';
        $response['waitlist_id'] = $waitlistId;
        
        // Log the submission
        error_log("Waitlist form submitted: ID {$waitlistId}, Email: {$email}, Name: {$name}");
        
    } else {
        $response['message'] = 'Failed to join waitlist. Please try again.';
    }
    
} catch (Exception $e) {
    error_log("Waitlist form error: " . $e->getMessage());
    $response['message'] = 'An error occurred while joining the waitlist. Please try again.';
}

echo json_encode($response);
?>
