<?php
session_start();
require($_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'] . '/../');
$dotenv->load();

$servername = $_SERVER['DB_SERVERNAME'] ?? 'default_servername';
$username = $_SERVER['DB_USERNAME'] ?? 'default_username';
$password = $_SERVER['DB_PASSWORD'] ?? 'default_password';
$dbname = $_SERVER['DB_NAME'] ?? 'default_dbname';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_username, $db_password, $db_role);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $db_role; // Correctly set the session role
            header("Location: profile.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
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
            background-color: #007BFF;
        }
        .link-container {
            margin-top: 10px;
        }
        .link-container a {
            display: inline-block;
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
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Sign in to your account</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="link-container">
            <a href="create_account.php">Create Account</a>
        </div>
    </div>
</body>
</html>
