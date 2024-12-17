<?php
session_start();
require($_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'] . '/../');
$dotenv->load();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection (use environment variables)
$servername = $_SERVER['DB_SERVERNAME'] ?? 'default_servername';
$username = $_SERVER['DB_USERNAME'] ?? 'default_username';
$password = $_SERVER['DB_PASSWORD'] ?? 'default_password';
$dbname = $_SERVER['DB_NAME'] ?? 'default_dbname';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information from the database using session data (user_id)
$stmt = $conn->prepare("SELECT username, age, gender, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($db_username, $db_age, $db_gender, $db_profile_pic);
$stmt->fetch();
$stmt->close();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $uploadDir = 'uploads/'; // Directory for uploaded files
    $uploadFile = $uploadDir . basename($_FILES['profile_pic']['name']);
    $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION)); // Get file extension
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']; // Allowed file types

    // Check if upload directory exists, if not, create it
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory with permissions
    }

    // File validation
    if (!in_array($fileType, $allowedTypes)) {
        echo "<p style='color:red;'>Error: Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
    } elseif ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) { // Limit file size to 2MB
        echo "<p style='color:red;'>Error: File size exceeds 2MB limit.</p>";
    } elseif (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadFile)) {
        // Save the uploaded file path in the session and update the database
        $_SESSION['profile_pic'] = $uploadFile;
        $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $uploadFile, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        echo "<p style='color:green;'>File uploaded successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: File upload failed. Please try again.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .header {
            background-color: #262673;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .nav-container {
            background-color: #f8f9fa;
            padding: 10px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .nav-container ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            text-align: center;
        }

        .nav-container ul li {
            display: inline;
            margin: 0 15px;
        }

        .nav-container ul li a {
            text-decoration: none;
            color: #262673;
            font-weight: bold;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .nav-container ul li a:hover {
            background-color: #007BFF;
            color: white;
        }

        .profile-container {
            max-width: 500px;
            margin: 50px auto;
            text-align: center;
        }

        .profile-info {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .upload-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 20px auto;
            text-align: center;
        }

        .upload-form input[type="file"] {
            margin-bottom: 15px;
        }

        .upload-form button {
            background-color: blue;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .upload-form button:hover {
            background-color: darkblue;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Your Profile</h1>
    </div>

    <div class="nav-container">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="search.php">Search Travel Advisories</a></li>
            <li><a href="comment.php">Post Comments</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </div>

    <div class="profile-container">
        <div class="profile-info">
            <h2>Welcome, <?php echo htmlspecialchars($db_username); ?></h2>
            <p>Age: <?php echo htmlspecialchars($db_age); ?></p>
            <p>Gender: <?php echo htmlspecialchars($db_gender); ?></p>
            <?php
            if (isset($_SESSION['profile_pic'])) {
                echo "<h3>Your Profile Picture:</h3>";
                echo "<img src='" . $_SESSION['profile_pic'] . "' alt='Profile Picture' style='max-width:200px; margin-top:10px;'>";
            } else {
                echo "<h3>No profile picture uploaded.</h3>";
            }
            ?>
        </div>

        <!-- Profile Picture Upload Form -->
        <div class="upload-form">
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_pic" accept="image/*" required>
                <button type="submit">Upload New Picture</button>
            </form>
        </div>
    </div>

</body>

</html>
