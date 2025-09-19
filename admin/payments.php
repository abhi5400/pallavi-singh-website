<?php
/**
 * Payments Management - Pallavi Singh Coaching
 * Manage payments and financial records
 */

require_once '../config/database_json.php';

// Check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle form submissions
if ($_POST) {
    try {
        $db = Database::getInstance();
        
        if (isset($_POST['add_payment'])) {
            $paymentData = [
                'client_id' => $_POST['client_id'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'payment_method' => $_POST['payment_method'],
                'status' => $_POST['status'],
                'description' => $_POST['description'],
                'payment_date' => $_POST['payment_date'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->insert('payments', $paymentData);
            $success = "Payment recorded successfully!";
        }
        
        if (isset($_POST['update_payment'])) {
            $paymentId = $_POST['payment_id'];
            $updateData = [
                'client_id' => $_POST['client_id'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'payment_method' => $_POST['payment_method'],
                'status' => $_POST['status'],
                'description' => $_POST['description'],
                'payment_date' => $_POST['payment_date']
            ];
            
            $db->update('payments', $paymentId, $updateData);
            $success = "Payment updated successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get payments and clients data
try {
    $db = Database::getInstance();
    $allPayments = $db->getData('payments');
    $allClients = $db->getData('clients');
    
    // Sort payments by payment_date descending
    usort($allPayments, function($a, $b) {
        return strtotime($b['payment_date']) - strtotime($a['payment_date']);
    });
    
    // Calculate totals
    $totalRevenue = array_sum(array_column($allPayments, 'amount'));
    $paidAmount = array_sum(array_column(array_filter($allPayments, function($p) { return $p['status'] === 'paid'; }), 'amount'));
    $pendingAmount = array_sum(array_column(array_filter($allPayments, function($p) { return $p['status'] === 'pending'; }), 'amount'));
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Payments Management';
$pageSubtitle = 'Manage payments and financial records';

// Create page content
ob_start();
?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="dashboard-grid">
    <!-- Payment Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>$<?php echo number_format($paidAmount, 2); ?></h3>
                <p>Paid Amount</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>$<?php echo number_format($pendingAmount, 2); ?></h3>
                <p>Pending Amount</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($allPayments); ?></h3>
                <p>Total Payments</p>
            </div>
        </div>
    </div>
    
    <!-- Add Payment -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-plus"></i> Record New Payment</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="client_id">Client</label>
                        <select id="client_id" name="client_id" class="form-control" required>
                            <option value="">Select Client</option>
                            <?php foreach ($allClients as $client): ?>
                            <option value="<?php echo $client['id']; ?>"><?php echo htmlspecialchars($client['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" id="amount" name="amount" step="0.01" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" class="form-control">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="form-control">
                            <option value="credit-card">Credit Card</option>
                            <option value="bank-transfer">Bank Transfer</option>
                            <option value="paypal">PayPal</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" id="payment_date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="e.g., Life coaching session, Monthly package, etc."></textarea>
                </div>
                
                <button type="submit" name="add_payment" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Record Payment
                </button>
            </form>
        </div>
    </div>
    
    <!-- Payments List -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-receipt"></i> All Payments</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allPayments as $payment): ?>
                        <?php 
                        $client = null;
                        foreach ($allClients as $c) {
                            if ($c['id'] == $payment['client_id']) {
                                $client = $c;
                                break;
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo $client ? htmlspecialchars($client['name']) : 'Unknown Client'; ?></td>
                            <td><?php echo $payment['currency']; ?> <?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo ucwords(str_replace('-', ' ', $payment['payment_method'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $payment['status']; ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($payment['description'] ?? 'N/A'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="editPayment(<?php echo $payment['id']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Payment Modal -->
<div id="editPaymentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Payment</h3>
            <span class="close" onclick="closeModal('editPaymentModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form method="POST" id="editPaymentForm">
                <input type="hidden" name="payment_id" id="edit_payment_id">
                
                <div class="form-group">
                    <label for="edit_client_id">Client</label>
                    <select id="edit_client_id" name="client_id" class="form-control" required>
                        <?php foreach ($allClients as $client): ?>
                        <option value="<?php echo $client['id']; ?>"><?php echo htmlspecialchars($client['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_amount">Amount</label>
                        <input type="number" id="edit_amount" name="amount" step="0.01" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_currency">Currency</label>
                        <select id="edit_currency" name="currency" class="form-control">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_payment_method">Payment Method</label>
                        <select id="edit_payment_method" name="payment_method" class="form-control">
                            <option value="credit-card">Credit Card</option>
                            <option value="bank-transfer">Bank Transfer</option>
                            <option value="paypal">PayPal</option>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status" class="form-control">
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_payment_date">Payment Date</label>
                    <input type="date" id="edit_payment_date" name="payment_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editPaymentModal')">Cancel</button>
                    <button type="submit" name="update_payment" class="btn btn-primary">Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPayment(id) {
    // This would fetch payment data and populate the form
    document.getElementById('edit_payment_id').value = id;
    document.getElementById('editPaymentModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>
