<?php
// Database configuration for InfinityFree
$host = 'sql302.epizy.com'; // Your database host
$db_name = 'epiz_12345678_portosantos'; // Your database name
$username = 'epiz_12345678'; // Your database username
$password = 'your_password_here'; // Your database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}