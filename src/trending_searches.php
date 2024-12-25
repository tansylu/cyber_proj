<?php
session_start();
include 'database.php';

// Fetch the top 10 most recent search queries
$query = "SELECT query, timestamp FROM search_queries ORDER BY timestamp DESC LIMIT 10";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching search queries: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending Searches</title>
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

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .trending-list {
            list-style-type: none;
            padding: 0;
        }

        .trending-list li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .trending-list li:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Trending Searches</h1>
    </div>

    <!-- Navigation Menu -->
    <div class="nav-container">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="search.php">Search Travel News</a></li>
            <li><a href="trending_searches.php">Trending</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</li>
                <li><a href="admin.php">Admin Panel</a></li>
                <li><a href="add_admin.php">Add Admin</a></li>
                <li><a href="profile.php">Profile</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
            <li><a href="logout.php" style="color: red;">Logout</a></li>
        </ul>
    </div>


    <div class="container">
        <h2>Top 10 Trending Search Queries</h2>
        <ul class="trending-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['query']); ?></strong>
                    <br>
                    <small>Timestamp: <?php echo date("d-m-Y H:i", strtotime($row['timestamp'])); ?></small>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

</body>

</html>
