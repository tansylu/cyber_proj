<?php
// Include database connection
include 'database.php';

// Initialize variables for error/success messages
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Input validation
    if (empty($username) || empty($password)) {
        $error = "Username and password cannot be empty.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $success = "Account created successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
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
            /* Add space below the form for the links */
        }

        .create-account-form input {
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
            background-color: darkblue;
        }
    </style>
</head>

<body>

    <div class="create-account-container">
        <!-- Header -->
        <h1>Create an account</h1>

        <!-- Display success or error messages -->
        <?php if (!empty($error)): ?>
            <p class="message error"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="message success"><?php echo $success; ?></p>
        <?php endif; ?>

        <!-- Create Account Form -->
        <form method="POST" class="create-account-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create</button>
        </form>

    </div>
</body>

</html>