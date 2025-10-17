<?php
/**
 * Testimonials Management - Pallavi Singh Coaching
 * Manage client testimonials and reviews
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
        
        if (isset($_POST['create_testimonial'])) {
            $testimonialData = [
                'client_name' => $_POST['client_name'],
                'client_title' => $_POST['client_title'],
                'client_company' => $_POST['client_company'],
                'content' => $_POST['content'],
                'rating' => $_POST['rating'],
                'service_type' => $_POST['service_type'],
                'client_image' => $_POST['client_image'],
                'status' => $_POST['status'],
                'featured' => isset($_POST['featured']),
                'created_by' => $_SESSION['admin_user']['full_name']
            ];
            
            $db->insert('testimonials', $testimonialData);
            $success = "Testimonial created successfully!";
        }
        
        if (isset($_POST['update_testimonial'])) {
            $testimonialId = $_POST['testimonial_id'];
            $updateData = [
                'client_name' => $_POST['client_name'],
                'client_title' => $_POST['client_title'],
                'client_company' => $_POST['client_company'],
                'content' => $_POST['content'],
                'rating' => $_POST['rating'],
                'service_type' => $_POST['service_type'],
                'client_image' => $_POST['client_image'],
                'status' => $_POST['status'],
                'featured' => isset($_POST['featured'])
            ];
            
            $db->update('testimonials', $testimonialId, $updateData);
            $success = "Testimonial updated successfully!";
        }
        
        if (isset($_POST['delete_testimonial'])) {
            $testimonialId = $_POST['testimonial_id'];
            $db->delete('testimonials', $testimonialId);
            $success = "Testimonial deleted successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get testimonials data
try {
    $db = JsonDatabase::getInstance();
    $allTestimonials = $db->getData('testimonials');
    
    // Sort by created_at descending
    usort($allTestimonials, function($a, $b) {
        $dateA = $a['created_at'] ?? '1970-01-01';
        $dateB = $b['created_at'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    });
    
    // Get service types
    $serviceTypes = ['Life Coaching', 'Habit Formation', 'Anxiety Management', 'Relationship Coaching', 'Storytelling', 'Public Speaking', 'Career Coaching', 'Wellness Coaching'];
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Testimonials Management';
$pageSubtitle = 'Manage client testimonials and reviews';

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

<!-- Testimonials Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-primary" onclick="showCreateModal()">
            ⭐ Add New Testimonial
        </button>
        <button class="btn btn-secondary" onclick="exportTestimonials()">
            📊 Export Testimonials
        </button>
    </div>
    
    <div class="actions-right">
        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
        </select>
        
        <select id="ratingFilter" class="filter-select">
            <option value="">All Ratings</option>
            <option value="5">5 Stars</option>
            <option value="4">4 Stars</option>
            <option value="3">3 Stars</option>
            <option value="2">2 Stars</option>
            <option value="1">1 Star</option>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search testimonials...">
    </div>
</div>

<!-- Testimonials Grid -->
<div class="testimonials-grid fade-in">
    <?php foreach ($allTestimonials as $testimonial): ?>
    <div class="testimonial-card" data-status="<?php echo $testimonial['status']; ?>" data-rating="<?php echo $testimonial['rating']; ?>">
        <div class="testimonial-header">
            <?php if ($testimonial['featured']): ?>
                <span class="featured-badge">⭐ Featured</span>
            <?php endif; ?>
            <div class="testimonial-status">
                <span class="status-badge <?php echo $testimonial['status']; ?>">
                    <?php echo ucfirst($testimonial['status']); ?>
                </span>
            </div>
        </div>
        
        <div class="testimonial-content">
            <div class="client-info">
                <div class="client-avatar">
                    <?php if ($testimonial['client_image']): ?>
                        <img src="<?php echo htmlspecialchars($testimonial['client_image']); ?>" alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>">
                    <?php else: ?>
                        <div class="no-image"><?php echo strtoupper(substr($testimonial['client_name'], 0, 1)); ?></div>
                    <?php endif; ?>
                </div>
                <div class="client-details">
                    <h4 class="client-name"><?php echo htmlspecialchars($testimonial['client_name']); ?></h4>
                    <p class="client-title"><?php echo htmlspecialchars($testimonial['client_title']); ?></p>
                    <?php if ($testimonial['client_company']): ?>
                        <p class="client-company"><?php echo htmlspecialchars($testimonial['client_company']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="testimonial-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star <?php echo $i <= $testimonial['rating'] ? 'filled' : ''; ?>">⭐</span>
                <?php endfor; ?>
                <span class="rating-text">(<?php echo $testimonial['rating']; ?>/5)</span>
            </div>
            
            <div class="testimonial-text">
                <p>"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
            </div>
            
            <div class="testimonial-meta">
                <div class="service-type">
                    <span class="service-badge"><?php echo htmlspecialchars($testimonial['service_type']); ?></span>
                </div>
                <div class="testimonial-date">
                    <?php echo $testimonial['created_at'] ? date('M j, Y', strtotime($testimonial['created_at'])) : 'N/A'; ?>
                </div>
            </div>
        </div>
        
        <div class="testimonial-actions">
            <button class="btn btn-sm btn-primary" onclick="editTestimonial(<?php echo $testimonial['id']; ?>)">
                ✏️ Edit
            </button>
            <button class="btn btn-sm btn-secondary" onclick="viewTestimonial(<?php echo $testimonial['id']; ?>)">
                👁️ View
            </button>
            <button class="btn btn-sm btn-danger" onclick="deleteTestimonial(<?php echo $testimonial['id']; ?>)">
                🗑️ Delete
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Create/Edit Testimonial Modal -->
<div id="testimonialModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">⭐ Add New Testimonial</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        
        <form method="POST" class="modal-body">
            <input type="hidden" name="testimonial_id" id="testimonialId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="client_name">Client Name *</label>
                    <input type="text" id="client_name" name="client_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="client_title">Client Title *</label>
                    <input type="text" id="client_title" name="client_title" class="form-control" required placeholder="e.g., CEO, Manager, Student">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="client_company">Company/Organization</label>
                    <input type="text" id="client_company" name="client_company" class="form-control" placeholder="Optional">
                </div>
                
                <div class="form-group">
                    <label for="service_type">Service Type *</label>
                    <select id="service_type" name="service_type" class="form-control" required>
                        <option value="">Select Service</option>
                        <?php foreach ($serviceTypes as $type): ?>
                            <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="rating">Rating *</label>
                    <select id="rating" name="rating" class="form-control" required>
                        <option value="">Select Rating</option>
                        <option value="5">5 Stars - Excellent</option>
                        <option value="4">4 Stars - Very Good</option>
                        <option value="3">3 Stars - Good</option>
                        <option value="2">2 Stars - Fair</option>
                        <option value="1">1 Star - Poor</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="content">Testimonial Content *</label>
                <textarea id="content" name="content" class="form-control" rows="4" required placeholder="What did the client say about your service?"></textarea>
            </div>
            
            <div class="form-group">
                <label for="client_image">Client Image URL</label>
                <input type="url" id="client_image" name="client_image" class="form-control" placeholder="https://example.com/client-photo.jpg">
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="featured" id="featured">
                    <span class="checkmark"></span>
                    Featured Testimonial
                </label>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" name="create_testimonial" id="submitBtn" class="btn btn-primary">Add Testimonial</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🗑️ Delete Testimonial</h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this testimonial? This action cannot be undone.</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="testimonial_id" id="deleteTestimonialId">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="submit" form="deleteForm" name="delete_testimonial" class="btn btn-danger">Delete Testimonial</button>
        </div>
    </div>
</div>

<style>
/* Testimonials Management Styles */
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

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 25px;
}

.testimonial-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.testimonial-header {
    padding: 15px 20px;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.featured-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 600;
    background: #fff3cd;
    color: #856404;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.published {
    background: #d4edda;
    color: #155724;
}

.status-badge.draft {
    background: #fff3cd;
    color: #856404;
}

.status-badge.archived {
    background: #f8d7da;
    color: #721c24;
}

.testimonial-content {
    padding: 20px;
}

.client-info {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.client-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.client-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    font-size: 1.5em;
    font-weight: 700;
    color: #1A535C;
}

.client-details {
    flex: 1;
}

.client-name {
    color: #1A535C;
    margin: 0 0 5px 0;
    font-size: 1.1em;
    font-weight: 700;
}

.client-title {
    color: #666;
    margin: 0 0 3px 0;
    font-size: 0.9em;
}

.client-company {
    color: #999;
    margin: 0;
    font-size: 0.8em;
}

.testimonial-rating {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 15px;
}

.star {
    font-size: 1.2em;
    opacity: 0.3;
    transition: opacity 0.3s ease;
}

.star.filled {
    opacity: 1;
}

.rating-text {
    color: #666;
    font-size: 0.9em;
    margin-left: 5px;
}

.testimonial-text {
    margin-bottom: 15px;
}

.testimonial-text p {
    color: #333;
    line-height: 1.6;
    margin: 0;
    font-style: italic;
}

.testimonial-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.service-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 600;
}

.testimonial-date {
    color: #999;
    font-size: 0.8em;
}

.testimonial-actions {
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
    margin: 5% auto;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
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
    
    .testimonials-grid {
        grid-template-columns: 1fr;
    }
    
    .testimonial-actions {
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
// Testimonials management JavaScript
let testimonialsData = <?php echo json_encode($allTestimonials); ?>;

document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeSearch();
});

function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const ratingFilter = document.getElementById('ratingFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTestimonials);
    }
    
    if (ratingFilter) {
        ratingFilter.addEventListener('change', filterTestimonials);
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterTestimonials);
    }
}

function filterTestimonials() {
    const statusFilter = document.getElementById('statusFilter').value;
    const ratingFilter = document.getElementById('ratingFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const cards = document.querySelectorAll('.testimonial-card');
    
    cards.forEach(card => {
        const status = card.dataset.status;
        const rating = card.dataset.rating;
        const name = card.querySelector('.client-name').textContent.toLowerCase();
        const content = card.querySelector('.testimonial-text p').textContent.toLowerCase();
        
        let show = true;
        
        if (statusFilter && status !== statusFilter) show = false;
        if (ratingFilter && rating !== ratingFilter) show = false;
        if (searchTerm && !name.includes(searchTerm) && !content.includes(searchTerm)) show = false;
        
        card.style.display = show ? '' : 'none';
    });
}

function showCreateModal() {
    document.getElementById('modalTitle').textContent = '⭐ Add New Testimonial';
    document.getElementById('submitBtn').textContent = 'Add Testimonial';
    document.getElementById('submitBtn').name = 'create_testimonial';
    document.getElementById('testimonialId').value = '';
    document.querySelector('form').reset();
    document.getElementById('testimonialModal').style.display = 'block';
}

function editTestimonial(testimonialId) {
    const testimonial = testimonialsData.find(t => t.id == testimonialId);
    if (!testimonial) return;
    
    document.getElementById('modalTitle').textContent = '✏️ Edit Testimonial';
    document.getElementById('submitBtn').textContent = 'Update Testimonial';
    document.getElementById('submitBtn').name = 'update_testimonial';
    document.getElementById('testimonialId').value = testimonialId;
    
    // Fill form with testimonial data
    document.getElementById('client_name').value = testimonial.client_name;
    document.getElementById('client_title').value = testimonial.client_title;
    document.getElementById('client_company').value = testimonial.client_company || '';
    document.getElementById('content').value = testimonial.content;
    document.getElementById('rating').value = testimonial.rating;
    document.getElementById('service_type').value = testimonial.service_type;
    document.getElementById('client_image').value = testimonial.client_image || '';
    document.getElementById('status').value = testimonial.status;
    document.getElementById('featured').checked = testimonial.featured || false;
    
    document.getElementById('testimonialModal').style.display = 'block';
}

function viewTestimonial(testimonialId) {
    const testimonial = testimonialsData.find(t => t.id == testimonialId);
    if (testimonial) {
        alert('Testimonial by: ' + testimonial.client_name + '\nRating: ' + testimonial.rating + '/5\nService: ' + testimonial.service_type);
    }
}

function deleteTestimonial(testimonialId) {
    document.getElementById('deleteTestimonialId').value = testimonialId;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('testimonialModal').style.display = 'none';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function exportTestimonials() {
    const csvContent = "data:text/csv;charset=utf-8," + 
        "Client Name,Title,Company,Rating,Service Type,Status,Featured,Date\n" +
        testimonialsData.map(testimonial => 
            `"${testimonial.client_name}","${testimonial.client_title}","${testimonial.client_company || ''}","${testimonial.rating}","${testimonial.service_type}","${testimonial.status}","${testimonial.featured ? 'Yes' : 'No'}","${testimonial.created_at}"`
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "testimonials_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const testimonialModal = document.getElementById('testimonialModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === testimonialModal) {
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
