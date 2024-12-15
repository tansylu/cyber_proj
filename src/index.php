<?php
// Include any server-side logic here
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turkish Travel Advisory Dashboard</title>
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

        .dashboard-content {
            padding: 20px;
            text-align: center;
        }

        .dashboard-content h2 {
            color: #333;
        }

        .dashboard-content p {
            color: #555;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <h1>Travel Advisory Dashboard</h1>
    </div>

    <!-- Navigation Menu -->
    <div class="nav-container">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="search.php">Search Travel Advisories</a></li>
            <li><a href="comment.php">Post Comments</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </div>

    <!-- Dashboard Content Section -->
    <div class="dashboard-content">
        <h2>Latest Travel News</h2>
        <p>Stay informed about the latest travel advisories and updates.</p>
    </div>
</body>

</html>