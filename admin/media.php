<?php
/**
 * Media Library - Pallavi Singh Coaching
 * Manage images, documents, and media files
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
        
        if (isset($_POST['upload_media'])) {
            $mediaData = [
                'filename' => $_POST['filename'],
                'original_name' => $_POST['original_name'],
                'file_path' => $_POST['file_path'],
                'file_type' => $_POST['file_type'],
                'file_size' => $_POST['file_size'],
                'alt_text' => $_POST['alt_text'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'category' => $_POST['category'],
                'tags' => explode(',', $_POST['tags']),
                'uploaded_by' => $_SESSION['admin_user']['full_name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->insert('media_library', $mediaData);
            $success = "Media file added successfully!";
        }
        
        if (isset($_POST['update_media'])) {
            $mediaId = $_POST['media_id'];
            $updateData = [
                'filename' => $_POST['filename'],
                'original_name' => $_POST['original_name'],
                'file_type' => $_POST['file_type'],
                'file_size' => $_POST['file_size'],
                'url' => $_POST['url'],
                'alt_text' => $_POST['alt_text'],
                'description' => $_POST['description'],
                'category' => $_POST['category'],
                'tags' => explode(',', $_POST['tags'])
            ];
            
            $db->update('media_library', $mediaId, $updateData);
            $success = "Media file updated successfully!";
        }
        
        if (isset($_POST['delete_media'])) {
            $mediaId = $_POST['media_id'];
            $db->delete('media_library', $mediaId);
            $success = "Media file deleted successfully!";
        }
        
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get media data
try {
    $db = JsonDatabase::getInstance();
    $allMedia = $db->getData('media_library');
    
    // Generate URLs for media files
    foreach ($allMedia as &$media) {
        if (!isset($media['url']) && isset($media['file_path'])) {
            $media['url'] = '/' . $media['file_path'];
        }
    }
    
    // Sort by created_at descending
    usort($allMedia, function($a, $b) {
        $dateA = $a['created_at'] ?? '1970-01-01';
        $dateB = $b['created_at'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    });
    
    // Get media categories
    $categories = ['Images', 'Documents', 'Videos', 'Audio', 'Icons', 'Logos', 'Backgrounds', 'Other'];
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

// Set page variables
$pageTitle = 'Media Library';
$pageSubtitle = 'Manage your images, documents, and media files';

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

<!-- Media Actions -->
<div class="page-actions fade-in">
    <div class="actions-left">
        <button class="btn btn-primary" onclick="showUploadModal()">
            📁 Add Media File
        </button>
        <button class="btn btn-secondary" onclick="exportMedia()">
            📊 Export Media List
        </button>
    </div>
    
    <div class="actions-right">
        <select id="categoryFilter" class="filter-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
            <?php endforeach; ?>
        </select>
        
        <select id="typeFilter" class="filter-select">
            <option value="">All Types</option>
            <option value="image">Images</option>
            <option value="document">Documents</option>
            <option value="video">Videos</option>
            <option value="audio">Audio</option>
        </select>
        
        <input type="text" id="searchInput" class="search-input" placeholder="Search media...">
    </div>
</div>

<!-- View Toggle -->
<div class="view-toggle fade-in">
    <button class="toggle-btn active" onclick="toggleView('grid')" id="gridBtn">
        🔲 Grid View
    </button>
    <button class="toggle-btn" onclick="toggleView('list')" id="listBtn">
        📋 List View
    </button>
</div>

<!-- Media Grid -->
<div class="media-grid fade-in" id="mediaGrid">
    <?php foreach ($allMedia as $media): ?>
    <div class="media-card" data-category="<?php echo $media['category']; ?>" data-type="<?php echo $media['file_type']; ?>">
        <div class="media-preview">
            <?php if (strpos($media['file_type'], 'image') !== false): ?>
                <img src="<?php echo htmlspecialchars($media['url']); ?>" alt="<?php echo htmlspecialchars($media['alt_text']); ?>" loading="lazy">
            <?php elseif (strpos($media['file_type'], 'video') !== false): ?>
                <div class="video-preview">
                    <video>
                        <source src="<?php echo htmlspecialchars($media['url']); ?>" type="<?php echo $media['file_type']; ?>">
                    </video>
                    <div class="play-icon">▶️</div>
                </div>
            <?php elseif (strpos($media['file_type'], 'audio') !== false): ?>
                <div class="audio-preview">
                    <div class="audio-icon">🎵</div>
                </div>
            <?php else: ?>
                <div class="document-preview">
                    <div class="document-icon">📄</div>
                </div>
            <?php endif; ?>
            
            <div class="media-overlay">
                <button class="btn btn-sm btn-primary" onclick="editMedia(<?php echo $media['id']; ?>)">
                    ✏️ Edit
                </button>
                <button class="btn btn-sm btn-secondary" onclick="copyUrl('<?php echo $media['url']; ?>')">
                    📋 Copy URL
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteMedia(<?php echo $media['id']; ?>)">
                    🗑️ Delete
                </button>
            </div>
        </div>
        
        <div class="media-info">
            <h4 class="media-name"><?php echo htmlspecialchars($media['original_name']); ?></h4>
            <p class="media-description"><?php echo htmlspecialchars($media['description']); ?></p>
            
            <div class="media-meta">
                <div class="meta-item">
                    <span class="meta-label">Category:</span>
                    <span class="meta-value"><?php echo htmlspecialchars($media['category']); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Size:</span>
                    <span class="meta-value"><?php echo formatFileSize($media['file_size']); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Type:</span>
                    <span class="meta-value"><?php echo htmlspecialchars($media['file_type']); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Uploaded:</span>
                    <span class="meta-value"><?php echo $media['created_at'] ? date('M j, Y', strtotime($media['created_at'])) : 'N/A'; ?></span>
                </div>
            </div>
            
            <?php if (!empty($media['tags'])): ?>
                <div class="media-tags">
                    <?php foreach ($media['tags'] as $tag): ?>
                        <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Media List View -->
<div class="media-list fade-in" id="mediaList" style="display: none;">
    <div class="list-header">
        <div class="list-column">Preview</div>
        <div class="list-column">Name</div>
        <div class="list-column">Category</div>
        <div class="list-column">Size</div>
        <div class="list-column">Type</div>
        <div class="list-column">Date</div>
        <div class="list-column">Actions</div>
    </div>
    
    <?php foreach ($allMedia as $media): ?>
    <div class="list-item" data-category="<?php echo $media['category']; ?>" data-type="<?php echo $media['file_type']; ?>">
        <div class="list-column">
            <div class="list-preview">
                <?php if (strpos($media['file_type'], 'image') !== false): ?>
                    <img src="<?php echo htmlspecialchars($media['url']); ?>" alt="<?php echo htmlspecialchars($media['alt_text']); ?>" loading="lazy">
                <?php else: ?>
                    <div class="file-icon">📄</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="list-column">
            <div class="list-name"><?php echo htmlspecialchars($media['original_name']); ?></div>
            <div class="list-description"><?php echo htmlspecialchars($media['description']); ?></div>
        </div>
        <div class="list-column">
            <span class="category-badge"><?php echo htmlspecialchars($media['category']); ?></span>
        </div>
        <div class="list-column">
            <?php echo formatFileSize($media['file_size']); ?>
        </div>
        <div class="list-column">
            <?php echo htmlspecialchars($media['file_type']); ?>
        </div>
        <div class="list-column">
            <?php echo $media['created_at'] ? date('M j, Y', strtotime($media['created_at'])) : 'N/A'; ?>
        </div>
        <div class="list-column">
            <div class="list-actions">
                <button class="btn btn-sm btn-primary" onclick="editMedia(<?php echo $media['id']; ?>)">
                    ✏️
                </button>
                <button class="btn btn-sm btn-secondary" onclick="copyUrl('<?php echo $media['url']; ?>')">
                    📋
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteMedia(<?php echo $media['id']; ?>)">
                    🗑️
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Upload/Edit Media Modal -->
<div id="mediaModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">📁 Add Media File</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        
        <form method="POST" class="modal-body">
            <input type="hidden" name="media_id" id="mediaId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="original_name">File Name *</label>
                    <input type="text" id="original_name" name="original_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="filename">System Filename *</label>
                    <input type="text" id="filename" name="filename" class="form-control" required placeholder="e.g., hero-image-2024.jpg">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="url">File URL *</label>
                    <input type="url" id="url" name="url" class="form-control" required placeholder="https://example.com/path/to/file.jpg">
                </div>
                
                <div class="form-group">
                    <label for="file_type">File Type *</label>
                    <select id="file_type" name="file_type" class="form-control" required>
                        <option value="">Select Type</option>
                        <option value="image/jpeg">JPEG Image</option>
                        <option value="image/png">PNG Image</option>
                        <option value="image/gif">GIF Image</option>
                        <option value="image/webp">WebP Image</option>
                        <option value="video/mp4">MP4 Video</option>
                        <option value="video/webm">WebM Video</option>
                        <option value="audio/mp3">MP3 Audio</option>
                        <option value="audio/wav">WAV Audio</option>
                        <option value="application/pdf">PDF Document</option>
                        <option value="application/msword">Word Document</option>
                        <option value="application/vnd.openxmlformats-officedocument.wordprocessingml.document">Word Document (DOCX)</option>
                        <option value="text/plain">Text File</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="file_size">File Size (bytes) *</label>
                    <input type="number" id="file_size" name="file_size" class="form-control" required min="0">
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
                <label for="alt_text">Alt Text (for images)</label>
                <input type="text" id="alt_text" name="alt_text" class="form-control" placeholder="Descriptive text for accessibility">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief description of the media file"></textarea>
            </div>
            
            <div class="form-group">
                <label for="tags">Tags (comma-separated)</label>
                <input type="text" id="tags" name="tags" class="form-control" placeholder="hero, banner, logo, background">
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" name="upload_media" id="submitBtn" class="btn btn-primary">Add Media</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🗑️ Delete Media File</h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this media file? This action cannot be undone.</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="media_id" id="deleteMediaId">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            <button type="submit" form="deleteForm" name="delete_media" class="btn btn-danger">Delete Media</button>
        </div>
    </div>
</div>

<?php
// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

<style>
/* Media Library Styles */
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

.view-toggle {
    background: white;
    padding: 15px 20px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    display: flex;
    gap: 10px;
}

.toggle-btn {
    padding: 8px 16px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.toggle-btn.active {
    background: #1A535C;
    color: white;
    border-color: #1A535C;
}

.toggle-btn:hover:not(.active) {
    background: #f8f9fa;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.media-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.media-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.media-preview {
    position: relative;
    height: 200px;
    background: #f8f9fa;
    overflow: hidden;
}

.media-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-preview, .audio-preview, .document-preview {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.video-preview {
    position: relative;
}

.video-preview video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.play-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 2em;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 10px;
    border-radius: 50%;
}

.audio-icon, .document-icon {
    font-size: 3em;
    color: #666;
}

.media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.media-card:hover .media-overlay {
    opacity: 1;
}

.media-info {
    padding: 20px;
}

.media-name {
    color: #1A535C;
    margin: 0 0 10px 0;
    font-size: 1.1em;
    font-weight: 700;
}

.media-description {
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.5;
    font-size: 0.9em;
}

.media-meta {
    margin-bottom: 15px;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 0.9em;
}

.meta-label {
    color: #666;
    font-weight: 500;
}

.meta-value {
    color: #333;
    font-weight: 600;
}

.media-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.tag {
    background: #e3f2fd;
    color: #1976d2;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 500;
}

/* List View Styles */
.media-list {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.list-header {
    display: grid;
    grid-template-columns: 80px 2fr 1fr 1fr 1fr 1fr 120px;
    gap: 15px;
    padding: 15px 20px;
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
    border-bottom: 1px solid #e9ecef;
}

.list-item {
    display: grid;
    grid-template-columns: 80px 2fr 1fr 1fr 1fr 1fr 120px;
    gap: 15px;
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    align-items: center;
    transition: background 0.3s ease;
}

.list-item:hover {
    background: #f8f9fa;
}

.list-column {
    display: flex;
    align-items: center;
}

.list-preview {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.list-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-icon {
    font-size: 1.5em;
    color: #666;
}

.list-name {
    font-weight: 600;
    color: #1A535C;
    margin-bottom: 3px;
}

.list-description {
    color: #666;
    font-size: 0.9em;
}

.category-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 600;
}

.list-actions {
    display: flex;
    gap: 5px;
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
    
    .media-grid {
        grid-template-columns: 1fr;
    }
    
    .list-header, .list-item {
        grid-template-columns: 1fr;
        gap: 10px;
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
// Media library JavaScript
let mediaData = <?php echo json_encode($allMedia); ?>;

document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeSearch();
});

function initializeFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterMedia);
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', filterMedia);
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterMedia);
    }
}

function filterMedia() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    const cards = document.querySelectorAll('.media-card');
    const listItems = document.querySelectorAll('.list-item');
    
    cards.forEach(card => {
        const category = card.dataset.category;
        const type = card.dataset.type;
        const name = card.querySelector('.media-name').textContent.toLowerCase();
        const description = card.querySelector('.media-description').textContent.toLowerCase();
        
        let show = true;
        
        if (categoryFilter && category !== categoryFilter) show = false;
        if (typeFilter && !type.includes(typeFilter)) show = false;
        if (searchTerm && !name.includes(searchTerm) && !description.includes(searchTerm)) show = false;
        
        card.style.display = show ? '' : 'none';
    });
    
    listItems.forEach(item => {
        const category = item.dataset.category;
        const type = item.dataset.type;
        const name = item.querySelector('.list-name').textContent.toLowerCase();
        const description = item.querySelector('.list-description').textContent.toLowerCase();
        
        let show = true;
        
        if (categoryFilter && category !== categoryFilter) show = false;
        if (typeFilter && !type.includes(typeFilter)) show = false;
        if (searchTerm && !name.includes(searchTerm) && !description.includes(searchTerm)) show = false;
        
        item.style.display = show ? '' : 'none';
    });
}

function toggleView(view) {
    const gridBtn = document.getElementById('gridBtn');
    const listBtn = document.getElementById('listBtn');
    const mediaGrid = document.getElementById('mediaGrid');
    const mediaList = document.getElementById('mediaList');
    
    if (view === 'grid') {
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        mediaGrid.style.display = 'grid';
        mediaList.style.display = 'none';
    } else {
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        mediaGrid.style.display = 'none';
        mediaList.style.display = 'block';
    }
}

function showUploadModal() {
    document.getElementById('modalTitle').textContent = '📁 Add Media File';
    document.getElementById('submitBtn').textContent = 'Add Media';
    document.getElementById('submitBtn').name = 'upload_media';
    document.getElementById('mediaId').value = '';
    document.querySelector('form').reset();
    document.getElementById('mediaModal').style.display = 'block';
}

function editMedia(mediaId) {
    const media = mediaData.find(m => m.id == mediaId);
    if (!media) return;
    
    document.getElementById('modalTitle').textContent = '✏️ Edit Media File';
    document.getElementById('submitBtn').textContent = 'Update Media';
    document.getElementById('submitBtn').name = 'update_media';
    document.getElementById('mediaId').value = mediaId;
    
    // Fill form with media data
    document.getElementById('original_name').value = media.original_name;
    document.getElementById('filename').value = media.filename;
    document.getElementById('url').value = media.url;
    document.getElementById('file_type').value = media.file_type;
    document.getElementById('file_size').value = media.file_size;
    document.getElementById('category').value = media.category;
    document.getElementById('alt_text').value = media.alt_text || '';
    document.getElementById('description').value = media.description || '';
    document.getElementById('tags').value = Array.isArray(media.tags) ? media.tags.join(', ') : '';
    
    document.getElementById('mediaModal').style.display = 'block';
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert('URL copied to clipboard!');
    });
}

function deleteMedia(mediaId) {
    document.getElementById('deleteMediaId').value = mediaId;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('mediaModal').style.display = 'none';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function exportMedia() {
    const csvContent = "data:text/csv;charset=utf-8," + 
        "Name,Filename,Category,Type,Size,URL,Description,Date\n" +
        mediaData.map(media => 
            `"${media.original_name}","${media.filename}","${media.category}","${media.file_type}","${media.file_size}","${media.url}","${media.description || ''}","${media.created_at}"`
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "media_library_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const mediaModal = document.getElementById('mediaModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === mediaModal) {
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
