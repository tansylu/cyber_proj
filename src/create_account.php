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
    // Sanitize user input
    $username = $_POST['username'];

    /*
    VULNERABILITY:
    Password is stored as cleartext in the database. This is not secure as any attacks on the database will reveal sensitive information.
    */
    $password = $_POST['password'];
    $age = $_POST['age'] ?? NULL;  // Default to NULL if no age provided
    $gender = $_POST['gender'] ?? NULL;  // Default to NULL if no gender provided
    $profile_pic = $_POST['profile_pic'] ?? NULL;  // Default to NULL if no profile pic provided

    // Prepared statement with correct binding
    $stmt = $conn->prepare("INSERT INTO users (username, password, age, gender, profile_pic, created_at) VALUES (?, ?, ?, ?, ?, NOW())");

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Bind parameters: s = string, i = integer
    $stmt->bind_param("ssiss", $username, $password, $age, $gender, $profile_pic);

    // Execute statement
    if ($stmt->execute()) {
        // On success, set session variables and redirect
        $user_id = $stmt->insert_id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header("Location: profile.php");
        exit();
    } else {
        // On failure, show error
        $error = "Error creating account. Please try again.";
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

        .create-account-container {
            max-width: 400px;
            margin: 50px auto;
            text-align: center;
        }

        .create-account-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .create-account-form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .create-account-form select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .create-account-form button {
            width: 100%;
            padding: 10px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .create-account-form button:hover {
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

    <div class="create-account-container">
        <h1>Create a new account</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="create-account-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="number" name="age" placeholder="Age" min="1" max="120">
            <select name="gender">
                <option value="" disabled selected>Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <input type="text" name="profile_pic" placeholder="Profile Picture URL (optional)">
            <button type="submit">Create Account</button>
        </form>
        <div class="link-container">
            <a href="login.php">Login</a>
        </div>
    </div>

</body>

</html>