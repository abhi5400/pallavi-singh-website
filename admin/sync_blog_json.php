<?php
/**
 * Sync Blog Posts from Database to JSON File
 * This ensures the frontend can read published blog posts
 */

require_once '../config/database.php';

function syncBlogPostsToJson() {
    try {
        $db = Database::getInstance();
        $allPosts = $db->getData('blog_posts');
        
        // Decode JSON fields if using MySQL
        if (!$db->isUsingJson()) {
            foreach ($allPosts as &$post) {
                if (isset($post['categories']) && is_string($post['categories'])) {
                    $post['categories'] = json_decode($post['categories'], true) ?: [];
                }
                if (isset($post['tags']) && is_string($post['tags'])) {
                    $post['tags'] = json_decode($post['tags'], true) ?: [];
                }
            }
            unset($post); // Break reference
        }
        
        // Ensure data directory exists
        $dataDir = __DIR__ . '/../data/';
        if (!file_exists($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
        
        // Save to JSON file
        $jsonFile = $dataDir . 'blog_posts.json';
        $result = file_put_contents($jsonFile, json_encode($allPosts, JSON_PRETTY_PRINT));
        
        return $result !== false;
    } catch (Exception $e) {
        error_log("Error syncing blog posts to JSON: " . $e->getMessage());
        return false;
    }
}

// If called directly, sync and return result
if (php_sapi_name() === 'cli' || basename($_SERVER['PHP_SELF']) === 'sync_blog_json.php') {
    $success = syncBlogPostsToJson();
    echo $success ? "Blog posts synced successfully!" : "Error syncing blog posts.";
    exit($success ? 0 : 1);
}

