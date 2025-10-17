<?php
/**
 * Migration script to transfer data from JSON files to MySQL database
 * Run this script after setting up the MySQL database
 */

require_once 'config/database.php';
require_once 'config/database_json.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Migration: JSON to MySQL</h1>";
echo "<p>Starting migration process...</p>";

try {
    // Get both database instances
    $mysqlDb = Database::getInstance();
    $jsonDb = \Database::getInstance(); // Use namespace to avoid conflict
    
    echo "<h2>Step 1: Testing MySQL Connection</h2>";
    $connection = $mysqlDb->getConnection();
    echo "✓ MySQL connection successful<br>";
    
    echo "<h2>Step 2: Migrating Data</h2>";
    
    // Migrate join submissions
    echo "<h3>Migrating Join Submissions...</h3>";
    $joinSubmissions = $jsonDb->getData('join_submissions');
    $migratedCount = 0;
    
    foreach ($joinSubmissions as $submission) {
        try {
            // Remove the 'id' field as MySQL will auto-generate it
            unset($submission['id']);
            unset($submission['created_at']);
            unset($submission['updated_at']);
            
            $mysqlDb->insert('join_submissions', $submission);
            $migratedCount++;
            echo "✓ Migrated submission: {$submission['form_id']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate submission {$submission['form_id']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} join submissions<br><br>";
    
    // Migrate newsletter subscriptions
    echo "<h3>Migrating Newsletter Subscriptions...</h3>";
    $newsletterSubs = $jsonDb->getData('newsletter_subscriptions');
    $migratedCount = 0;
    
    foreach ($newsletterSubs as $subscription) {
        try {
            unset($subscription['id']);
            unset($subscription['created_at']);
            unset($subscription['updated_at']);
            
            $mysqlDb->insert('newsletter_subscriptions', $subscription);
            $migratedCount++;
            echo "✓ Migrated subscription: {$subscription['email']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate subscription {$subscription['email']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} newsletter subscriptions<br><br>";
    
    // Migrate contact submissions
    echo "<h3>Migrating Contact Submissions...</h3>";
    $contactSubs = $jsonDb->getData('contact_submissions');
    $migratedCount = 0;
    
    foreach ($contactSubs as $submission) {
        try {
            unset($submission['id']);
            unset($submission['created_at']);
            unset($submission['updated_at']);
            
            $mysqlDb->insert('contact_submissions', $submission);
            $migratedCount++;
            echo "✓ Migrated contact submission: {$submission['name']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate contact submission {$submission['name']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} contact submissions<br><br>";
    
    // Migrate blog posts
    echo "<h3>Migrating Blog Posts...</h3>";
    $blogPosts = $jsonDb->getData('blog_posts');
    $migratedCount = 0;
    
    foreach ($blogPosts as $post) {
        try {
            unset($post['id']);
            unset($post['created_at']);
            unset($post['updated_at']);
            
            // Set author_id to 1 (default admin user)
            $post['author_id'] = 1;
            
            $mysqlDb->insert('blog_posts', $post);
            $migratedCount++;
            echo "✓ Migrated blog post: {$post['title']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate blog post {$post['title']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} blog posts<br><br>";
    
    // Migrate testimonials
    echo "<h3>Migrating Testimonials...</h3>";
    $testimonials = $jsonDb->getData('testimonials');
    $migratedCount = 0;
    
    foreach ($testimonials as $testimonial) {
        try {
            unset($testimonial['id']);
            unset($testimonial['created_at']);
            unset($testimonial['updated_at']);
            
            $mysqlDb->insert('testimonials', $testimonial);
            $migratedCount++;
            echo "✓ Migrated testimonial: {$testimonial['client_name']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate testimonial {$testimonial['client_name']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} testimonials<br><br>";
    
    // Migrate services
    echo "<h3>Migrating Services...</h3>";
    $services = $jsonDb->getData('services');
    $migratedCount = 0;
    
    foreach ($services as $service) {
        try {
            unset($service['id']);
            unset($service['created_at']);
            unset($service['updated_at']);
            
            $mysqlDb->insert('services', $service);
            $migratedCount++;
            echo "✓ Migrated service: {$service['title']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate service {$service['title']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} services<br><br>";
    
    // Migrate events/workshops
    echo "<h3>Migrating Events/Workshops...</h3>";
    $events = $jsonDb->getData('events_workshops');
    $migratedCount = 0;
    
    foreach ($events as $event) {
        try {
            unset($event['id']);
            unset($event['created_at']);
            unset($event['updated_at']);
            
            $mysqlDb->insert('events_workshops', $event);
            $migratedCount++;
            echo "✓ Migrated event: {$event['title']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate event {$event['title']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} events/workshops<br><br>";
    
    // Migrate clients
    echo "<h3>Migrating Clients...</h3>";
    $clients = $jsonDb->getData('clients');
    $migratedCount = 0;
    
    foreach ($clients as $client) {
        try {
            unset($client['id']);
            unset($client['created_at']);
            unset($client['updated_at']);
            
            $mysqlDb->insert('clients', $client);
            $migratedCount++;
            echo "✓ Migrated client: {$client['full_name']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate client {$client['full_name']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} clients<br><br>";
    
    // Migrate sessions
    echo "<h3>Migrating Sessions...</h3>";
    $sessions = $jsonDb->getData('sessions');
    $migratedCount = 0;
    
    foreach ($sessions as $session) {
        try {
            unset($session['id']);
            unset($session['created_at']);
            unset($session['updated_at']);
            
            $mysqlDb->insert('sessions', $session);
            $migratedCount++;
            echo "✓ Migrated session: {$session['session_type']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate session {$session['session_type']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} sessions<br><br>";
    
    // Migrate payments
    echo "<h3>Migrating Payments...</h3>";
    $payments = $jsonDb->getData('payments');
    $migratedCount = 0;
    
    foreach ($payments as $payment) {
        try {
            unset($payment['id']);
            unset($payment['created_at']);
            unset($payment['updated_at']);
            
            $mysqlDb->insert('payments', $payment);
            $migratedCount++;
            echo "✓ Migrated payment: {$payment['amount']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate payment {$payment['amount']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} payments<br><br>";
    
    // Migrate media library
    echo "<h3>Migrating Media Library...</h3>";
    $media = $jsonDb->getData('media_library');
    $migratedCount = 0;
    
    foreach ($media as $item) {
        try {
            unset($item['id']);
            unset($item['created_at']);
            unset($item['updated_at']);
            
            // Set uploaded_by to 1 (default admin user)
            $item['uploaded_by'] = 1;
            
            $mysqlDb->insert('media_library', $item);
            $migratedCount++;
            echo "✓ Migrated media: {$item['filename']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate media {$item['filename']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} media items<br><br>";
    
    // Migrate analytics
    echo "<h3>Migrating Analytics...</h3>";
    $analytics = $jsonDb->getData('analytics');
    $migratedCount = 0;
    
    foreach ($analytics as $analytic) {
        try {
            unset($analytic['id']);
            unset($analytic['created_at']);
            unset($analytic['updated_at']);
            
            $mysqlDb->insert('analytics', $analytic);
            $migratedCount++;
            echo "✓ Migrated analytic: {$analytic['event_type']}<br>";
        } catch (Exception $e) {
            echo "✗ Failed to migrate analytic {$analytic['event_type']}: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migrated {$migratedCount} analytics records<br><br>";
    
    echo "<h2>Migration Complete!</h2>";
    echo "<p>All data has been successfully migrated from JSON files to MySQL database.</p>";
    echo "<p>You can now update your application to use the MySQL database.</p>";
    
} catch (Exception $e) {
    echo "<h2>Migration Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your MySQL configuration and try again.</p>";
}
?>
