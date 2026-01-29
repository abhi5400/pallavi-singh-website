<?php
/**
 * Maintenance actions - Pallavi Singh Coaching Admin
 * clear_old_data, backup, reset
 */

require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'clear_old_data') {
    header('Content-Type: application/json');
    try {
        $db = Database::getInstance();
        $cutoff = date('Y-m-d H:i:s', strtotime('-1 year'));
        $tables = ['contact_submissions', 'join_submissions', 'booking_submissions', 'journey_submissions'];
        $deleted = 0;
        if ($db->isUsingJson()) {
            require_once __DIR__ . '/../config/database_json.php';
            $jsonDb = JsonDatabase::getInstance();
            foreach ($tables as $table) {
                $data = $db->getData($table) ?: [];
                $keep = array_filter($data, function ($r) use ($cutoff) {
                    $d = $r['submission_date'] ?? $r['created_at'] ?? '';
                    return $d && $d >= $cutoff;
                });
                $deleted += count($data) - count($keep);
                $jsonDb->saveData($table, array_values($keep));
            }
        }
        echo json_encode(['success' => true, 'message' => 'Old data cleared.', 'deleted' => $deleted]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'backup') {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="backup_' . date('Y-m-d') . '.zip"');
    $dataDir = __DIR__ . '/../data/';
    if (!is_dir($dataDir)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Data dir not found']);
        exit;
    }
    $zip = new ZipArchive();
    $tmp = tempnam(sys_get_temp_dir(), 'backup');
    if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cannot create zip']);
        exit;
    }
    foreach (glob($dataDir . '*.json') as $file) {
        $zip->addFile($file, 'data/' . basename($file));
    }
    $zip->close();
    readfile($tmp);
    unlink($tmp);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'reset') {
    header('Content-Type: application/json');
    try {
        $db = Database::getInstance();
        if ($db->isUsingJson()) {
            require_once __DIR__ . '/../config/database_json.php';
            $j = JsonDatabase::getInstance();
            $tables = ['contact_submissions', 'join_submissions', 'booking_submissions', 'journey_submissions', 'newsletter_subscriptions', 'waitlist_subscriptions'];
            foreach ($tables as $t) {
                $j->saveData($t, []);
            }
        }
        echo json_encode(['success' => true, 'message' => 'System reset.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid action']);
