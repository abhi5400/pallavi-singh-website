<?php
/**
 * Export data to CSV - Pallavi Singh Coaching Admin
 */

require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(403);
    exit('Unauthorized');
}

$type = $_GET['type'] ?? '';
$allowed = ['contact_submissions', 'join_submissions', 'booking_submissions', 'journey_submissions', 'newsletter_subscriptions', 'waitlist_subscriptions'];
if (!in_array($type, $allowed, true)) {
    http_response_code(400);
    exit('Invalid type');
}

try {
    $db = Database::getInstance();
    $rows = $db->getData($type) ?: [];
} catch (Exception $e) {
    http_response_code(500);
    exit('Export failed');
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $type . '_export.csv"');

$out = fopen('php://output', 'w');
if (empty($rows)) {
    fputcsv($out, ['No data']);
    fclose($out);
    exit;
}

fputcsv($out, array_keys($rows[0]));
foreach ($rows as $row) {
    fputcsv($out, $row);
}
fclose($out);
