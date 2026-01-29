<?php
/**
 * Newsletter Subscriptions - Pallavi Singh Coaching
 * Manage newsletter/email list
 */

require_once '../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_POST) {
    try {
        $db = Database::getInstance();
        if (isset($_POST['update_newsletter'])) {
            $id = (int)$_POST['newsletter_id'];
            $updateData = [
                'status' => $_POST['status'] ?? 'active',
                'notes' => $_POST['notes'] ?? ''
            ];
            $db->update('newsletter_subscriptions', $id, $updateData);
            $success = "Subscription updated successfully.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

try {
    $db = Database::getInstance();
    $allSubs = $db->getData('newsletter_subscriptions') ?: [];
    usort($allSubs, function ($a, $b) {
        $da = $a['subscription_date'] ?? $a['submission_date'] ?? $a['created_at'] ?? '1970-01-01';
        $db = $b['subscription_date'] ?? $b['submission_date'] ?? $b['created_at'] ?? '1970-01-01';
        return strtotime($db) - strtotime($da);
    });
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
    $allSubs = [];
}

$pageTitle = 'Newsletter';
$pageSubtitle = 'Manage newsletter subscriptions';
$additionalCSS = ['assets/css/forms.css'];

ob_start();
?>

<?php if ($success): ?>
    <div class="success-message fade-in">✅ <?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="error-message fade-in">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="page-actions fade-in">
    <input type="text" id="searchInput" class="search-input" placeholder="Search by email or name...">
    <select id="statusFilter" class="filter-select">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="unsubscribed">Unsubscribed</option>
    </select>
</div>

<div class="data-table-container fade-in">
    <table class="data-table" id="newsletterTable">
        <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Source</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allSubs as $sub): ?>
            <tr>
                <td><a href="mailto:<?php echo htmlspecialchars($sub['email'] ?? ''); ?>"><?php echo htmlspecialchars($sub['email'] ?? 'N/A'); ?></a></td>
                <td><?php echo htmlspecialchars(trim(($sub['first_name'] ?? '') . ' ' . ($sub['last_name'] ?? '')) ?: '—'); ?></td>
                <td><?php echo htmlspecialchars($sub['source'] ?? '—'); ?></td>
                <td><span class="status-badge status-<?php echo $sub['status'] ?? 'active'; ?>"><?php echo ucfirst($sub['status'] ?? 'active'); ?></span></td>
                <td><?php echo isset($sub['subscription_date']) ? date('M j, Y', strtotime($sub['subscription_date'])) : (isset($sub['created_at']) ? date('M j, Y', strtotime($sub['created_at'])) : '—'); ?></td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editNewsletter(<?php echo (int)($sub['id'] ?? 0); ?>)">✏️ Edit</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (empty($allSubs)): ?>
    <p class="no-data">No newsletter subscriptions yet.</p>
<?php endif; ?>

<script>
document.getElementById('searchInput')?.addEventListener('input', filterTable);
document.getElementById('statusFilter')?.addEventListener('change', filterTable);
function filterTable() {
    const q = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const status = (document.getElementById('statusFilter')?.value || '').toLowerCase();
    document.querySelectorAll('#newsletterTable tbody tr').forEach(function(row) {
        const text = row.textContent.toLowerCase();
        const matchSearch = !q || text.indexOf(q) >= 0;
        const matchStatus = !status || row.querySelector('.status-badge')?.textContent?.toLowerCase().indexOf(status) >= 0;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}
function editNewsletter(id) { window.location.href = 'newsletter.php?edit=' + id; }
</script>

<?php
$pageContent = ob_get_clean();
include 'includes/layout.php';
?>
