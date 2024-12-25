<?php
session_start();
include 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $username = $_POST['username'];

    /*
    PATCHED:
    Password is hashed to prvent any cleartext storage of sensitive information.
    */
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
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
        // Fetch the inserted password
        $stmt->close();
        $select_stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        if ($select_stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $select_stmt->bind_param("s", $username);
        $select_stmt->execute();
        $select_stmt->bind_result($stored_password);
        $select_stmt->fetch();
        $select_stmt->close();

        // On success, set session variables and display popup
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $username;
        $success_message = "Your account has been created successfully. Your password is: $stored_password";
        echo "<script>
            alert('$success_message');
            window.location.href = 'profile.php';
        </script>";
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
        /* Styles omitted for brevity */
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