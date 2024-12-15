<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];


}
?>

<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 20px;
        }

        .login-container {
            max-width: 400px;
            margin: 50px auto;
            text-align: center;
        }

        .login-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            /* Add space below the form for the links */
        }

        .login-form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-form button:hover {
            background-color: darkblue;
        }

        .link-container {
            margin-top: 10px;
            /* Add some space above the links */
        }

        .link-container a {
            display: inline-box;
            text-decoration: none;
            color: blue;
            font-weight: bold;
            margin-right: 10px;
            margin-left: 10px;
            margin-bottom: 5px;
        }

        .link-container a:hover {
            color: darkblue;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <!-- Header -->
        <h2>Sign in as an administrator</h2>
        <!-- Login Form -->
        <form method="POST" class="login-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- Links below the form -->
        <div class="link-container">
            <a href="login.php">User Login</a>
            <a href="create_account.php">Create Account</a>
        </div>
    </div>

</body>

</html>