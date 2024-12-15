<?php
// File upload handling
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
        echo "<p style='color:green;'>File uploaded successfully!</p>";
        echo "<img src='/uploads/" . htmlspecialchars(basename($_FILES['profile_pic']['name'])) . "' alt='Profile Picture' style='max-width:200px; margin-top:10px;'>";
    } else {
        echo "<p style='color:red;'>Error: File upload failed. Please try again.</p>";
    }
}
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
    </style>
</head>

<body>
    <div class="header">
        <h1>Profile</h1>
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

    <!-- Profile Picture Upload Form -->
    <div class="upload-form">
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="profile_pic" accept="image/*" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>

</html>