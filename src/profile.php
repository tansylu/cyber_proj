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



include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
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

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        die("Failed to create directory: " . error_get_last()['message']);
    }
    
    
    

    // File validation
    if (!in_array($fileType, $allowedTypes)) {
        echo "<p style='color:red;'>Error: Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
    } elseif ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) { // Limit file size to 2MB
        echo "<p style='color:red;'>Error: File size exceeds 2MB limit.</p>";
    } else {
        // Rename the file to avoid overwrites
        $uniqueName = $uploadDir . uniqid('profile_', true) . '.' . $fileType;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uniqueName)) {
            // Update the database with the new file path
            $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $uniqueName, $user_id);
            if ($stmt->execute()) {
                echo "<p style='color:green;'>Profile picture uploaded successfully!</p>";
                $db_profile_pic = $uniqueName; // Update for immediate display
            } else {
                echo "<p style='color:red;'>Error updating profile picture in the database.</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color:red;'>Error: File upload failed. Please try again.</p>";
        }
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

    <!-- Navigation Menu -->
    <div class="nav-container">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="search.php">Search Travel News</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php" style="color: red;">Logout</a></li>
        </ul>
    </div>

    <div class="profile-container">
        <div class="profile-info">
            <h2>Welcome, <?php echo htmlspecialchars($db_username); ?></h2>
            <p>Age: <?php echo htmlspecialchars($db_age); ?></p>
            <p>Gender: <?php echo htmlspecialchars($db_gender); ?></p>           
            <?php if (!empty($db_profile_pic)): ?>
                <h3>Your Profile Picture:</h3>
                <img src="<?php echo htmlspecialchars($db_profile_pic); ?>" alt="Profile Picture" style="max-width:200px; margin-top:10px;">
            <?php else: ?>
                <h3>No profile picture uploaded.</h3>
            <?php endif; ?>
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