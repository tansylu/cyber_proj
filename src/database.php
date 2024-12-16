<?php
require($_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php');
$dotenv=Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'].'/../');
$dotenv->load();

// Access environment variables
$servername = $_SERVER['DB_SERVERNAME'] ?? 'default_servername';
$username = $_SERVER['DB_USERNAME'] ?? 'default_username';
$password = $_SERVER['DB_PASSWORD'] ?? 'default_password';
$dbname = $_SERVER['DB_NAME'] ?? 'default_dbname';

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>