<?php
/**
 * Blog Management - Pallavi Singh Coaching
 * Manage blog posts, categories, and content
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
        
        if (isset($_POST['create_post'])) {
            $postData = [
                'title' => $_POST['title'],
                'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title']))),
                'content' => $_POST['content'],
                'excerpt' => $_POST['excerpt'],
                'categories' => explode(',', $_POST['category']),
                'tags' => explode(',', $_POST['tags']),
                'featured_image' => $_POST['featured_image'],
                'status' => $_POST['status'],
                'author' => $_SESSION['admin_user']['full_name'],
                'published_at' => $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null,
                'meta_title' => $_POST['meta_title'],
                'meta_description' => $_POST['meta_description']
            ];
            
            $db->insert('blog_posts', $postData);
            $success = "Blog post created successfully!";
        }
        
        if (isset($_POST['update_post'])) {
            $postId = $_POST['post_id'];
            $updateData = [
                'title' => $_POST['title'],
                'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title']))),
                'content' => $_POST['content'],
                'excerpt' => $_POST['excerpt'],
                'categories' => explode(',', $_POST['category']),
                'tags' => explode(',', $_POST['tags']),
                'featured_image' => $_POST['featured_image'],
                'status' => $_POST['status'],
                'published_at' => $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null,
                'meta_title' => $_POST['meta_title'],
                'meta_description' => $_POST['meta_description']
            ];
            
            $db->update('blog_posts', $postId, $updateData);
            $success = "Blog post updated successfully!";
        }
        
        if (isset($_POST['delete_post'])) {
            $postId = $_POST['post_id'];
            $db->delete('blog_posts', $postId);
            $success = "Blog post deleted successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get blog posts data
try {
    $db = Database::getInstance();
    $allPosts = $db->getData('blog_posts');
    
    // Sort by created_at descending
    usort($allPosts, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    // Get categories
    $categories = ['Coaching', 'Personal Development', 'Habit Formation', 'Anxiety Management', 'Relationships', 'Storytelling', 'Public Speaking', 'Success Stories'];
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Blog Management';
$pageSubtitle = 'Create, edit, and manage your blog posts';

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

<!-- Blog Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-primary" onclick="showCreateModal()">
            ✍️ Create New Post
        </button>
        <button class="btn btn-secondary" onclick="exportPosts()">
            📊 Export Posts
        </button>
    </div>
    
    <div class="actions-right">
        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
        </select>
        
        <select id="categoryFilter" class="filter-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search posts...">
    </div>
</div>

<!-- Blog Posts Table -->
<div class="data-table-container fade-in">
    <div class="table-header">
        <h3>📝 Blog Posts (<?php echo count($allPosts); ?>)</h3>
        <div class="table-actions">
            <span class="table-info">Manage your content</span>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="postsTable">
            <thead>
                <tr>
                    <th><i class="fas fa-file-alt"></i> Title</th>
                    <th><i class="fas fa-folder"></i> Category</th>
                    <th><i class="fas fa-user"></i> Author</th>
                    <th><i class="fas fa-calendar"></i> Date</th>
                    <th><i class="fas fa-tag"></i> Status</th>
                    <th><i class="fas fa-eye"></i> Views</th>
                    <th><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allPosts as $post): ?>
                <tr data-status="<?php echo $post['status']; ?>" data-category="<?php echo isset($post['categories']) ? implode(',', $post['categories']) : ''; ?>">
                    <td>
                        <div class="post-title">
                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                            <?php if ($post['featured_image']): ?>
                                <span class="has-image"><i class="fas fa-image"></i></span>
                            <?php endif; ?>
                        </div>
                        <div class="post-excerpt">
                            <?php echo htmlspecialchars(substr($post['excerpt'], 0, 100)) . (strlen($post['excerpt']) > 100 ? '...' : ''); ?>
                        </div>
                    </td>
                    <td>
                        <div class="category-badge">
                            <?php 
                            if (isset($post['categories']) && is_array($post['categories'])) {
                                echo htmlspecialchars(implode(', ', $post['categories']));
                            } else {
                                echo 'Uncategorized';
                            }
                            ?>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <?php echo htmlspecialchars($post['author']); ?>
                        </div>
                    </td>
                    <td>
                        <div class="date-info">
                            <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                            <small><?php echo date('H:i', strtotime($post['created_at'])); ?></small>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge <?php echo $post['status']; ?>">
                            <?php echo ucfirst($post['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="views-count">
                            <?php echo $post['views'] ?? 0; ?>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="editPost(<?php echo $post['id']; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-secondary" onclick="viewPost(<?php echo $post['id']; ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deletePost(<?php echo $post['id']; ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Post Modal -->
<div id="postModal" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3 id="modalTitle">✍️ Create New Post</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        
        <form method="POST" class="modal-body">
            <input type="hidden" name="post_id" id="postId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Post Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Categories *</label>
                    <input type="text" id="category" name="category" class="form-control" placeholder="Enter categories separated by commas (e.g., life-coaching, transformation)" required>
                    <small class="form-text">Separate multiple categories with commas</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="featured_image">Featured Image URL</label>
                    <input type="url" id="featured_image" name="featured_image" class="form-control" placeholder="https://example.com/image.jpg">
                </div>
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" class="form-control" rows="3" placeholder="Brief description of the post..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" class="form-control" rows="10" required placeholder="Write your blog post content here..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tags">Tags (comma-separated)</label>
                    <input type="text" id="tags" name="tags" class="form-control" placeholder="coaching, personal development, habits">
                </div>
                
                <div class="form-group">
                    <label for="meta_title">SEO Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" placeholder="SEO optimized title">
                </div>
            </div>
            
            <div class="form-group">
                <label for="meta_description">SEO Description</label>
                <textarea id="meta_description" name="meta_description" class="form-control" rows="2" placeholder="Meta description for search engines..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" name="create_post" id="submitBtn" class="btn btn-primary">Create Post</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🗑️ Delete Post</h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this blog post? This action cannot be undone.</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="post_id" id="deletePostId">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="submit" form="deleteForm" name="delete_post" class="btn btn-danger">Delete Post</button>
        </div>
    </div>
</div>

<style>
/* Blog Management Styles */
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

.data-table-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.table-header {
    padding: 20px 25px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h3 {
    color: #1A535C;
    margin: 0;
    font-size: 1.3em;
}

.table-info {
    color: #666;
    font-size: 0.9em;
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e9ecef;
}

.data-table td {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: top;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.post-title {
    margin-bottom: 5px;
}

.post-title strong {
    color: #1A535C;
}

.has-image {
    margin-left: 8px;
    font-size: 0.8em;
}

.post-excerpt {
    color: #666;
    font-size: 0.9em;
    line-height: 1.4;
}

.category-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    display: inline-block;
}

.author-info {
    color: #666;
    font-size: 0.9em;
}

.date-info {
    font-size: 0.9em;
}

.date-info small {
    display: block;
    color: #999;
    font-size: 0.8em;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
}

.status-badge.published {
    background: #e8f5e8;
    color: #388e3c;
}

.status-badge.draft {
    background: #fff3e0;
    color: #f57c00;
}

.status-badge.archived {
    background: #ffebee;
    color: #c62828;
}

.views-count {
    color: #666;
    font-size: 0.9em;
}

.action-buttons {
    display: flex;
    gap: 8px;
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
    
    .data-table {
        font-size: 0.8em;
    }
    
    .action-buttons {
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
// Blog management JavaScript
let postsData = <?php echo json_encode($allPosts); ?>;

document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeSearch();
});

function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterPosts);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterPosts);
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterPosts);
    }
}

function filterPosts() {
    const statusFilter = document.getElementById('statusFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('#postsTable tbody tr');
    
    rows.forEach(row => {
        const status = row.dataset.status;
        const category = row.dataset.category;
        const title = row.querySelector('.post-title strong').textContent.toLowerCase();
        const excerpt = row.querySelector('.post-excerpt').textContent.toLowerCase();
        
        let show = true;
        
        if (statusFilter && status !== statusFilter) show = false;
        if (categoryFilter && !category.includes(categoryFilter)) show = false;
        if (searchTerm && !title.includes(searchTerm) && !excerpt.includes(searchTerm)) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

function showCreateModal() {
    document.getElementById('modalTitle').textContent = '✍️ Create New Post';
    document.getElementById('submitBtn').textContent = 'Create Post';
    document.getElementById('submitBtn').name = 'create_post';
    document.getElementById('postId').value = '';
    document.querySelector('form').reset();
    document.getElementById('postModal').style.display = 'block';
}

function editPost(postId) {
    const post = postsData.find(p => p.id == postId);
    if (!post) return;
    
    document.getElementById('modalTitle').textContent = '✏️ Edit Post';
    document.getElementById('submitBtn').textContent = 'Update Post';
    document.getElementById('submitBtn').name = 'update_post';
    document.getElementById('postId').value = postId;
    
    // Fill form with post data
    document.getElementById('title').value = post.title;
    document.getElementById('category').value = post.categories ? post.categories.join(',') : '';
    document.getElementById('featured_image').value = post.featured_image || '';
    document.getElementById('status').value = post.status;
    document.getElementById('excerpt').value = post.excerpt || '';
    document.getElementById('content').value = post.content;
    document.getElementById('tags').value = Array.isArray(post.tags) ? post.tags.join(', ') : '';
    document.getElementById('meta_title').value = post.meta_title || '';
    document.getElementById('meta_description').value = post.meta_description || '';
    
    document.getElementById('postModal').style.display = 'block';
}

function viewPost(postId) {
    const post = postsData.find(p => p.id == postId);
    if (post) {
        window.open(`../blog/${post.slug}.html`, '_blank');
    }
}

function deletePost(postId) {
    document.getElementById('deletePostId').value = postId;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('postModal').style.display = 'none';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function exportPosts() {
    const csvContent = "data:text/csv;charset=utf-8," + 
        "Title,Category,Author,Status,Date,Views\n" +
        postsData.map(post => 
            `"${post.title}","${post.categories ? post.categories.join(', ') : 'Uncategorized'}","${post.author}","${post.status}","${post.created_at}","${post.views || 0}"`
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "blog_posts_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const postModal = document.getElementById('postModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === postModal) {
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
