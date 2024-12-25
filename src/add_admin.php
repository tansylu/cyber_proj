<?php
session_start();
include 'database.php'; // Ensure this file connects to your database

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') || !isset($_SESSION['role'])) {
    echo '<script>alert("Access denied. You must be an admin to view this page."); window.location.href = "index.php";</script>';
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';

    // Validate input
    if (empty($username)) {
        $error = "Please enter a username.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update user's role to admin
            $stmt->close();
            $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE username = ?");
            $stmt->bind_param("s", $username);

            if ($stmt->execute()) {
                $success = "User role updated to admin successfully.";
            } else {
                $error = "Error updating user role: " . $stmt->error;
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Role to Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #007BFF;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update User Role to Admin</h2>
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <button type="submit">Update to Admin</button>
    </form>
</div>

</body>
</html>
