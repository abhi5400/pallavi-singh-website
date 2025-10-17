<?php
/**
 * Test MySQL Connection
 * This will test if your application can connect to MySQL
 */

// Test MySQL connection
try {
    $host = 'localhost';
    $dbname = 'pallavi_singh';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ MySQL Connection: SUCCESS!<br>";
    echo "✅ Database: $dbname<br>";
    echo "✅ Host: $host<br>";
    
    // Test inserting a record
    $sql = "INSERT INTO contact_submissions (name, email, message, submission_date, ip_address, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'Test User',
        'test@example.com', 
        'This is a test from PHP to MySQL',
        date('Y-m-d H:i:s'),
        '127.0.0.1',
        'new'
    ]);
    
    if ($result) {
        echo "✅ Test INSERT: SUCCESS!<br>";
        echo "✅ New form submissions will save to MySQL!<br>";
    }
    
} catch (Exception $e) {
    echo "❌ MySQL Connection: FAILED<br>";
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "❌ Forms will continue using JSON files<br>";
}
?>
