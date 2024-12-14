<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Placeholder logic for authentication
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['user'] = 'admin';
        echo "<p>Login successful! Welcome, $username.</p>";
    } else {
        echo "<p>Invalid username or password.</p>";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>