<?php
/**
 * Admin Panel Entry Point - Pallavi Singh Coaching
 * Routes to appropriate admin pages
 */

require_once '../config/database_json.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    // User is logged in, redirect to dashboard
    header('Location: dashboard.php');
    exit;
} else {
    // User is not logged in, redirect to login
    header('Location: login.php');
    exit;
}
?>

