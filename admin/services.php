<?php
/**
 * Services Management - Pallavi Singh Coaching
 * Manage coaching services and packages
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
        $db = JsonDatabase::getInstance();
        
        if (isset($_POST['create_service'])) {
            $serviceData = [
                'name' => $_POST['name'],
                'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name']))),
                'description' => $_POST['description'],
                'short_description' => $_POST['short_description'],
                'category' => $_POST['category'],
                'price' => $_POST['price'],
                'duration' => $_POST['duration'],
                'features' => explode("\n", $_POST['features']),
                'benefits' => explode("\n", $_POST['benefits']),
                'target_audience' => $_POST['target_audience'],
                'prerequisites' => $_POST['prerequisites'],
                'delivery_method' => $_POST['delivery_method'],
                'image' => $_POST['image'],
                'status' => $_POST['status'],
                'featured' => isset($_POST['featured']),
                'popular' => isset($_POST['popular']),
                'created_by' => $_SESSION['admin_user']['full_name']
            ];
            
            $db->insert('services', $serviceData);
            $success = "Service created successfully!";
        }
        
        if (isset($_POST['update_service'])) {
            $serviceId = $_POST['service_id'];
            $updateData = [
                'name' => $_POST['name'],
                'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name']))),
                'description' => $_POST['description'],
                'short_description' => $_POST['short_description'],
                'category' => $_POST['category'],
                'price' => $_POST['price'],
                'duration' => $_POST['duration'],
                'features' => explode("\n", $_POST['features']),
                'benefits' => explode("\n", $_POST['benefits']),
                'target_audience' => $_POST['target_audience'],
                'prerequisites' => $_POST['prerequisites'],
                'delivery_method' => $_POST['delivery_method'],
                'image' => $_POST['image'],
                'status' => $_POST['status'],
                'featured' => isset($_POST['featured']),
                'popular' => isset($_POST['popular'])
            ];
            
            $db->update('services', $serviceId, $updateData);
            $success = "Service updated successfully!";
        }
        
        if (isset($_POST['delete_service'])) {
            $serviceId = $_POST['service_id'];
            $db->delete('services', $serviceId);
            $success = "Service deleted successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get services data
try {
    $db = JsonDatabase::getInstance();
    $allServices = $db->getData('services');
    
    // Sort by created_at descending
    usort($allServices, function($a, $b) {
        $dateA = $a['created_at'] ?? '1970-01-01';
        $dateB = $b['created_at'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    });
    
    // Get service categories
    $categories = ['Life Coaching', 'Habit Formation', 'Anxiety Management', 'Relationship Coaching', 'Storytelling', 'Public Speaking', 'Career Coaching', 'Wellness Coaching'];
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Services Management';
$pageSubtitle = 'Manage your coaching services and packages';

// Create page content
ob_start();
?>

<?php if ($success): ?>
    <div class="success-message fade-in">
        ✅ <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error-message fade-in">
        ⚠️ <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Services Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-primary" onclick="showCreateModal()">
            ➕ Create New Service
        </button>
        <button class="btn btn-secondary" onclick="exportServices()">
            📊 Export Services
        </button>
    </div>
    
    <div class="actions-right">
        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="coming_soon">Coming Soon</option>
        </select>
        
        <select id="categoryFilter" class="filter-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search services...">
    </div>
</div>

<!-- Services Grid -->
<div class="services-grid fade-in">
    <?php foreach ($allServices as $service): ?>
    <div class="service-card" data-status="<?php echo $service['status']; ?>" data-category="<?php echo $service['category']; ?>">
        <div class="service-header">
            <?php if ($service['featured']): ?>
                <span class="featured-badge">⭐ Featured</span>
            <?php endif; ?>
            <?php if ($service['popular']): ?>
                <span class="popular-badge">🔥 Popular</span>
            <?php endif; ?>
            <div class="service-status">
                <span class="status-badge <?php echo $service['status']; ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $service['status'])); ?>
                </span>
            </div>
        </div>
        
        <div class="service-image">
            <?php if ($service['image']): ?>
                <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
            <?php else: ?>
                <div class="no-image">🖼️</div>
            <?php endif; ?>
        </div>
        
        <div class="service-content">
            <h3 class="service-name"><?php echo htmlspecialchars($service['name']); ?></h3>
            <p class="service-description"><?php echo htmlspecialchars($service['short_description']); ?></p>
            
            <div class="service-details">
                <div class="detail-item">
                    <span class="detail-label">Category:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($service['category']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value"><?php echo $service['duration']; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Price:</span>
                    <span class="detail-value price">₹<?php echo number_format($service['price']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Delivery:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($service['delivery_method']); ?></span>
                </div>
            </div>
            
            <div class="service-features">
                <h4>Key Features:</h4>
                <ul>
                    <?php 
                    $features = is_array($service['features']) ? $service['features'] : explode("\n", $service['features']);
                    foreach (array_slice($features, 0, 3) as $feature): 
                        if (trim($feature)):
                    ?>
                        <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </ul>
            </div>
        </div>
        
        <div class="service-actions">
            <button class="btn btn-sm btn-primary" onclick="editService(<?php echo $service['id']; ?>)">
                ✏️ Edit
            </button>
            <button class="btn btn-sm btn-secondary" onclick="viewService(<?php echo $service['id']; ?>)">
                👁️ View
            </button>
            <button class="btn btn-sm btn-danger" onclick="deleteService(<?php echo $service['id']; ?>)">
                🗑️ Delete
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Create/Edit Service Modal -->
<div id="serviceModal" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3 id="modalTitle">➕ Create New Service</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        
        <form method="POST" class="modal-body">
            <input type="hidden" name="service_id" id="serviceId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Service Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="short_description">Short Description *</label>
                <textarea id="short_description" name="short_description" class="form-control" rows="2" required placeholder="Brief description for cards and listings..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="description">Full Description *</label>
                <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Detailed description of the service..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (₹) *</label>
                    <input type="number" id="price" name="price" class="form-control" required min="0" step="0.01">
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration *</label>
                    <input type="text" id="duration" name="duration" class="form-control" required placeholder="e.g., 4 weeks, 2 hours, 6 months">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="delivery_method">Delivery Method *</label>
                    <select id="delivery_method" name="delivery_method" class="form-control" required>
                        <option value="">Select Method</option>
                        <option value="Online">Online</option>
                        <option value="In-Person">In-Person</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Self-Paced">Self-Paced</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="coming_soon">Coming Soon</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="features">Key Features (one per line) *</label>
                <textarea id="features" name="features" class="form-control" rows="4" required placeholder="Feature 1&#10;Feature 2&#10;Feature 3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="benefits">Benefits (one per line)</label>
                <textarea id="benefits" name="benefits" class="form-control" rows="3" placeholder="Benefit 1&#10;Benefit 2&#10;Benefit 3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="target_audience">Target Audience</label>
                <textarea id="target_audience" name="target_audience" class="form-control" rows="2" placeholder="Who is this service for?"></textarea>
            </div>
            
            <div class="form-group">
                <label for="prerequisites">Prerequisites</label>
                <textarea id="prerequisites" name="prerequisites" class="form-control" rows="2" placeholder="What do clients need before starting?"></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Service Image URL</label>
                <input type="url" id="image" name="image" class="form-control" placeholder="https://example.com/service-image.jpg">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="featured" id="featured">
                        <span class="checkmark"></span>
                        Featured Service
                    </label>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="popular" id="popular">
                        <span class="checkmark"></span>
                        Popular Service
                    </label>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" name="create_service" id="submitBtn" class="btn btn-primary">Create Service</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🗑️ Delete Service</h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this service? This action cannot be undone.</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="service_id" id="deleteServiceId">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="submit" form="deleteForm" name="delete_service" class="btn btn-danger">Delete Service</button>
        </div>
    </div>
</div>

<style>
/* Services Management Styles */
.page-actions {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.actions-left, .actions-right {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select, .search-input {
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.9em;
    transition: all 0.3s ease;
}

.filter-select:focus, .search-input:focus {
    outline: none;
    border-color: #1A535C;
    box-shadow: 0 0 0 3px rgba(26, 83, 92, 0.1);
}

.search-input {
    min-width: 250px;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 25px;
}

.service-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.service-header {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.featured-badge, .popular-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 600;
}

.featured-badge {
    background: #fff3cd;
    color: #856404;
}

.popular-badge {
    background: #f8d7da;
    color: #721c24;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.status-badge.coming_soon {
    background: #d1ecf1;
    color: #0c5460;
}

.service-image {
    height: 200px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    font-size: 3em;
    color: #ccc;
}

.service-content {
    padding: 20px;
}

.service-name {
    color: #1A535C;
    margin: 0 0 10px 0;
    font-size: 1.3em;
    font-weight: 700;
}

.service-description {
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.5;
}

.service-details {
    margin-bottom: 15px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9em;
}

.detail-label {
    color: #666;
    font-weight: 500;
}

.detail-value {
    color: #333;
    font-weight: 600;
}

.detail-value.price {
    color: #1A535C;
    font-size: 1.1em;
}

.service-features h4 {
    color: #1A535C;
    margin: 0 0 10px 0;
    font-size: 1em;
}

.service-features ul {
    margin: 0;
    padding-left: 20px;
}

.service-features li {
    color: #666;
    margin-bottom: 5px;
    font-size: 0.9em;
}

.service-actions {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85em;
}

.btn-primary {
    background: #1A535C;
    color: white;
}

.btn-primary:hover {
    background: #0d3a40;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    transform: translateY(-1px);
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.8em;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: white;
    margin: 2% auto;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.modal-content.large {
    max-width: 800px;
}

.modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    color: #1A535C;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5em;
    cursor: pointer;
    color: #666;
    padding: 5px;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: #f0f0f0;
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 0.95em;
}

.form-control {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95em;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #1A535C;
    box-shadow: 0 0 0 3px rgba(26, 83, 92, 0.1);
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    color: #333;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.2);
}

.success-message {
    background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
    color: #2e7d32;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #4caf50;
    font-weight: 500;
}

.error-message {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    border-left: 4px solid #f44336;
    font-weight: 500;
}

@media (max-width: 768px) {
    .page-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .actions-left, .actions-right {
        justify-content: center;
    }
    
    .search-input {
        min-width: 200px;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .service-actions {
        flex-direction: column;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        width: 95%;
        margin: 5% auto;
    }
}
</style>

<script>
// Services management JavaScript
let servicesData = <?php echo json_encode($allServices); ?>;

document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeSearch();
});

function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterServices);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterServices);
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterServices);
    }
}

function filterServices() {
    const statusFilter = document.getElementById('statusFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const cards = document.querySelectorAll('.service-card');
    
    cards.forEach(card => {
        const status = card.dataset.status;
        const category = card.dataset.category;
        const name = card.querySelector('.service-name').textContent.toLowerCase();
        const description = card.querySelector('.service-description').textContent.toLowerCase();
        
        let show = true;
        
        if (statusFilter && status !== statusFilter) show = false;
        if (categoryFilter && category !== categoryFilter) show = false;
        if (searchTerm && !name.includes(searchTerm) && !description.includes(searchTerm)) show = false;
        
        card.style.display = show ? '' : 'none';
    });
}

function showCreateModal() {
    document.getElementById('modalTitle').textContent = '➕ Create New Service';
    document.getElementById('submitBtn').textContent = 'Create Service';
    document.getElementById('submitBtn').name = 'create_service';
    document.getElementById('serviceId').value = '';
    document.querySelector('form').reset();
    document.getElementById('serviceModal').style.display = 'block';
}

function editService(serviceId) {
    const service = servicesData.find(s => s.id == serviceId);
    if (!service) return;
    
    document.getElementById('modalTitle').textContent = '✏️ Edit Service';
    document.getElementById('submitBtn').textContent = 'Update Service';
    document.getElementById('submitBtn').name = 'update_service';
    document.getElementById('serviceId').value = serviceId;
    
    // Fill form with service data
    document.getElementById('name').value = service.name;
    document.getElementById('category').value = service.category;
    document.getElementById('short_description').value = service.short_description || '';
    document.getElementById('description').value = service.description;
    document.getElementById('price').value = service.price;
    document.getElementById('duration').value = service.duration;
    document.getElementById('delivery_method').value = service.delivery_method;
    document.getElementById('status').value = service.status;
    document.getElementById('image').value = service.image || '';
    document.getElementById('target_audience').value = service.target_audience || '';
    document.getElementById('prerequisites').value = service.prerequisites || '';
    document.getElementById('featured').checked = service.featured || false;
    document.getElementById('popular').checked = service.popular || false;
    
    // Handle features and benefits arrays
    const features = Array.isArray(service.features) ? service.features : [];
    const benefits = Array.isArray(service.benefits) ? service.benefits : [];
    
    document.getElementById('features').value = features.join('\n');
    document.getElementById('benefits').value = benefits.join('\n');
    
    document.getElementById('serviceModal').style.display = 'block';
}

function viewService(serviceId) {
    const service = servicesData.find(s => s.id == serviceId);
    if (service) {
        alert('Service: ' + service.name + '\nCategory: ' + service.category + '\nPrice: ₹' + service.price);
    }
}

function deleteService(serviceId) {
    document.getElementById('deleteServiceId').value = serviceId;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('serviceModal').style.display = 'none';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function exportServices() {
    const csvContent = "data:text/csv;charset=utf-8," + 
        "Name,Category,Price,Duration,Status,Featured,Popular\n" +
        servicesData.map(service => 
            `"${service.name}","${service.category}","${service.price}","${service.duration}","${service.status}","${service.featured ? 'Yes' : 'No'}","${service.popular ? 'Yes' : 'No'}"`
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "services_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const serviceModal = document.getElementById('serviceModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === serviceModal) {
        closeModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
}
</script>

<?php
$pageContent = ob_get_clean();

// Include the layout
include 'includes/layout.php';
?>
