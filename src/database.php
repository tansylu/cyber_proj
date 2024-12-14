<?php
$host = 'db';
$dbname = 'travel_advisory';
$user = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
} catch (PDOException $e) {
    die("<p>Database connection failed: " . $e->getMessage() . "</p>");
}
?>