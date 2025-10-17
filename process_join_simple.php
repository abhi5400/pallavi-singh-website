<?php
/**
 * Simple Join Form Processing - For Testing
 * This version bypasses database and just logs the submission
 */

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
    // Get form data
    $full_name = $_POST['full_name'] ?? '';
    $age = $_POST['age'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $issue_challenge = $_POST['issue_challenge'] ?? '';
    $goals = $_POST['goals'] ?? '';
    $terms_accepted = isset($_POST['terms_accepted']);
    $newsletter_subscription = isset($_POST['newsletter_subscription']);
    
    // Basic validation
    if (empty($full_name) || empty($city) || empty($state) || empty($contact_number) || empty($email) || empty($issue_challenge) || !$terms_accepted) {
        $response['message'] = 'Please fill in all required fields and accept the terms.';
        echo json_encode($response);
        exit;
    }
    
    // Generate unique form ID
    $form_id = 'JOIN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    
    // Prepare submission data
    $submission_data = [
        'form_id' => $form_id,
        'full_name' => $full_name,
        'age' => $age ?: null,
        'city' => $city,
        'state' => $state,
        'contact_number' => $contact_number,
        'email' => $email,
        'issue_challenge' => $issue_challenge,
        'goals' => $goals,
        'terms_accepted' => $terms_accepted,
        'newsletter_subscription' => $newsletter_subscription,
        'submission_date' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'status' => 'new'
    ];
    
    // Ensure data directory exists
    $dataDir = __DIR__ . '/data/';
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    // Save to JSON file
    $jsonFile = $dataDir . 'join_submissions.json';
    $existingData = [];
    
    if (file_exists($jsonFile)) {
        $existingData = json_decode(file_get_contents($jsonFile), true) ?: [];
    }
    
    // Add new submission
    $submission_data['id'] = count($existingData) + 1;
    $existingData[] = $submission_data;
    
    // Save back to file
    if (file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT))) {
        $response['success'] = true;
        $response['message'] = 'Thank you for joining! Your form has been submitted successfully.';
        $response['form_id'] = $form_id;
        $response['redirect_url'] = 'thank_you.php?id=' . $form_id;
        
        // Log success
        error_log("Join form submitted successfully: Form ID {$form_id}, Email: {$email}, Name: {$full_name}");
    } else {
        $response['message'] = 'Failed to save your information. Please try again.';
    }
    
} catch (Exception $e) {
    error_log("Join form error: " . $e->getMessage());
    $response['message'] = 'An error occurred while processing your request. Please try again.';
}

echo json_encode($response);
?>

