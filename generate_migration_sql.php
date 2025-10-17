<?php
/**
 * Generate SQL INSERT statements from JSON data
 * This creates SQL statements that can be run directly in phpMyAdmin
 */

$json_files = [
    'admin_users' => 'admin_users',
    'contact_submissions' => 'contact_submissions', 
    'join_submissions' => 'join_submissions',
    'newsletter_subscriptions' => 'newsletter_subscriptions',
    'waitlist_subscriptions' => 'waitlist_subscriptions',
    'blog_posts' => 'blog_posts',
    'clients' => 'clients',
    'services' => 'services',
    'events_workshops' => 'events_workshops',
    'testimonials' => 'testimonials',
    'sessions' => 'sessions',
    'payments' => 'payments',
    'media_library' => 'media_library',
    'analytics' => 'analytics',
    'email_log' => 'email_log',
    'booking_submissions' => 'booking_submissions',
    'journey_submissions' => 'journey_submissions'
];

$sql_output = "-- Data Migration SQL for pallavi_singh database\n";
$sql_output .= "-- Run this in phpMyAdmin SQL tab\n\n";

foreach ($json_files as $table_name => $json_file) {
    $json_path = __DIR__ . "/data/$json_file.json";
    
    if (file_exists($json_path)) {
        $json_data = json_decode(file_get_contents($json_path), true);
        
        if (!empty($json_data)) {
            $sql_output .= "-- Migrating data to $table_name table\n";
            $sql_output .= "DELETE FROM `$table_name`;\n";
            
            foreach ($json_data as $record) {
                $columns = array_keys($record);
                $values = array_values($record);
                
                // Escape values for SQL
                $escaped_values = array_map(function($value) {
                    if ($value === null) {
                        return 'NULL';
                    } elseif (is_bool($value)) {
                        return $value ? 'TRUE' : 'FALSE';
                    } elseif (is_string($value)) {
                        return "'" . addslashes($value) . "'";
                    } else {
                        return $value;
                    }
                }, $values);
                
                $sql = "INSERT INTO `$table_name` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $escaped_values) . ");\n";
                $sql_output .= $sql;
            }
            
            $sql_output .= "\n";
            echo "✅ Generated SQL for $table_name table (" . count($json_data) . " records)\n";
        } else {
            echo "ℹ️  No data found in $json_file.json\n";
        }
    } else {
        echo "ℹ️  File $json_file.json not found\n";
    }
}

// Save to file
file_put_contents('migration_data.sql', $sql_output);

echo "\n🎉 SQL migration file created: migration_data.sql\n";
echo "📋 Instructions:\n";
echo "1. Open phpMyAdmin: http://localhost/phpmyadmin/\n";
echo "2. Select database: pallavi_singh\n";
echo "3. Click 'SQL' tab\n";
echo "4. Copy and paste the contents of migration_data.sql\n";
echo "5. Click 'Go' to execute\n";
?>
