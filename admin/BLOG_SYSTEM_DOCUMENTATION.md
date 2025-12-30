# Blog System Documentation

## Overview
The blog management system allows you to create, edit, delete, and manage blog posts through the admin panel.

## Database Connection

### Connection Flow:
1. **Entry Point**: `admin/blog.php`
2. **Database Class**: Uses `Database::getInstance()` from `config/database.php`
3. **Connection Strategy**:
   - **Primary**: Tries to connect to MySQL database (`pallavi_singh`)
   - **Fallback**: If MySQL fails, automatically uses JSON file-based storage (`data/blog_posts.json`)

### Database Structure

#### MySQL Table Structure:
```sql
CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `content` LONGTEXT NOT NULL,
    `excerpt` TEXT NULL,
    `featured_image` VARCHAR(255) NULL,
    `categories` JSON NULL,           -- Stores array as JSON
    `tags` JSON NULL,                 -- Stores array as JSON
    `status` VARCHAR(20) DEFAULT 'draft',
    `author` VARCHAR(100) DEFAULT 'Pallavi Singh',
    `published_at` DATETIME NULL,
    `meta_title` VARCHAR(200) NULL,
    `meta_description` TEXT NULL,
    `views` INT DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
);
```

#### JSON File Structure (Fallback):
File: `data/blog_posts.json`
```json
[
    {
        "id": 1,
        "title": "Post Title",
        "slug": "post-title",
        "content": "Full content...",
        "excerpt": "Brief description...",
        "categories": ["Coaching", "Personal Development"],
        "tags": ["coaching", "habits"],
        "featured_image": "assets/images/image.jpg",
        "status": "published",
        "author": "Pallavi Singh",
        "published_at": "2025-01-01 10:00:00",
        "meta_title": "SEO Title",
        "meta_description": "SEO description",
        "views": 0,
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:00:00"
    }
]
```

## Core Functions

### 1. **Create Blog Post**
```php
// In admin/blog.php
$db = Database::getInstance();

$postData = [
    'title' => $_POST['title'],
    'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title']))),
    'content' => $_POST['content'],
    'excerpt' => $_POST['excerpt'],
    'categories' => json_encode(explode(',', $_POST['category'])),  // JSON for MySQL
    'tags' => json_encode(explode(',', $_POST['tags'])),            // JSON for MySQL
    'featured_image' => $_POST['featured_image'],
    'status' => $_POST['status'],
    'author' => $_SESSION['admin_user']['full_name'],
    'published_at' => $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null,
    'meta_title' => $_POST['meta_title'],
    'meta_description' => $_POST['meta_description'],
    'views' => 0
];

$postId = $db->insert('blog_posts', $postData);
```

**How it works:**
- `Database::getInstance()` automatically detects MySQL or JSON mode
- `insert()` method:
  - **MySQL**: Executes `INSERT INTO blog_posts ...` SQL query
  - **JSON**: Adds record to array and saves to `data/blog_posts.json`
- Returns the new post ID

### 2. **Update Blog Post**
```php
$db = Database::getInstance();

$updateData = [
    'title' => $_POST['title'],
    'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title']))),
    'content' => $_POST['content'],
    'excerpt' => $_POST['excerpt'],
    'categories' => json_encode(explode(',', $_POST['category'])),
    'tags' => json_encode(explode(',', $_POST['tags'])),
    'featured_image' => $_POST['featured_image'],
    'status' => $_POST['status'],
    'published_at' => $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null,
    'meta_title' => $_POST['meta_title'],
    'meta_description' => $_POST['meta_description']
];

$db->update('blog_posts', $updateData, 'id = ?', [$postId]);
```

**How it works:**
- `update()` method:
  - **MySQL**: Executes `UPDATE blog_posts SET ... WHERE id = ?`
  - **JSON**: Finds record by ID and updates it in the array, then saves file

### 3. **Delete Blog Post**
```php
$db = Database::getInstance();
$db->delete('blog_posts', 'id = ?', [$postId]);
```

**How it works:**
- `delete()` method:
  - **MySQL**: Executes `DELETE FROM blog_posts WHERE id = ?`
  - **JSON**: Removes record from array and saves file

### 4. **Retrieve Blog Posts**
```php
$db = Database::getInstance();
$allPosts = $db->getData('blog_posts');

// For MySQL: Returns array of associative arrays
// For JSON: Returns array from JSON file

// Process categories and tags
foreach ($allPosts as &$post) {
    // Decode JSON if using MySQL
    if (is_string($post['categories'])) {
        $post['categories'] = json_decode($post['categories'], true) ?: [];
    }
    if (is_string($post['tags'])) {
        $post['tags'] = json_decode($post['tags'], true) ?: [];
    }
}
```

## Data Flow

### Creating a Post:
1. User fills form in admin panel → `admin/blog.php`
2. Form submits POST data
3. PHP processes data:
   - Generates slug from title
   - Converts comma-separated categories/tags to arrays
   - Sets author from session
   - Sets published_at if status is 'published'
4. `Database::insert()` stores data:
   - **MySQL**: Encodes arrays as JSON, inserts into database
   - **JSON**: Keeps as arrays, adds to JSON file
5. Success message shown

### Updating a Post:
1. User clicks "Edit" → JavaScript opens modal with post data
2. User modifies fields → Form submits
3. PHP processes updates (same as create, but with existing post ID)
4. `Database::update()` modifies record
5. Success message shown

### Viewing Posts:
1. Page loads → `$db->getData('blog_posts')` retrieves all posts
2. PHP sorts by created_at (newest first)
3. Categories and tags decoded from JSON if needed
4. HTML table displays posts with filters/search

## Important Notes

### Array Handling:
- **For MySQL**: Arrays (categories, tags) must be JSON encoded before storing
- **For JSON Database**: Arrays can be stored directly
- The Database class handles this automatically based on connection type

### Slug Generation:
- Automatically generated from title
- Lowercase, spaces replaced with hyphens
- Special characters removed
- Example: "How to Overcome Anxiety" → "how-to-overcome-anxiety"

### Status Values:
- `draft`: Not published, only visible in admin
- `published`: Live on website
- `archived`: Hidden but preserved

### Database Compatibility:
The system works with BOTH MySQL and JSON:
- If MySQL is available → Uses MySQL (preferred)
- If MySQL fails → Falls back to JSON files automatically
- Same code works for both!

## Frontend Integration

### Displaying Posts on Website:
The blog posts can be displayed on the frontend by:
1. Reading from `data/blog_posts.json` (if using JSON)
2. Or querying MySQL database
3. Filtering by `status = 'published'`
4. Rendering in HTML template

### Example Frontend Code:
```php
// In your blog listing page
require_once 'config/database.php';
$db = Database::getInstance();

// Get only published posts
$publishedPosts = array_filter(
    $db->getData('blog_posts'),
    fn($post) => $post['status'] === 'published'
);

// Sort by published_at
usort($publishedPosts, function($a, $b) {
    return strtotime($b['published_at']) - strtotime($a['published_at']);
});
```

## Troubleshooting

### Common Issues:

1. **Arrays not saving properly in MySQL**
   - Solution: Ensure categories/tags are JSON encoded before insert/update

2. **Update not working**
   - Solution: Check that update() uses correct syntax: `update($table, $data, $where, $params)`

3. **Delete not working**
   - Solution: Check that delete() uses correct syntax: `delete($table, $where, $params)`

4. **Posts not showing**
   - Check database connection
   - Verify data exists in table/file
   - Check for JSON decode errors

## Security Considerations

1. **SQL Injection**: Prevented by using prepared statements (PDO)
2. **XSS**: All output uses `htmlspecialchars()`
3. **Authentication**: All admin pages check `$_SESSION['admin_logged_in']`
4. **Input Validation**: Validate all user inputs before saving

