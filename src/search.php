<?php
session_start();
include 'database.php'; // Include database connection

$searchResults = [];
$searchQuery = '';

// Handle Search Query
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $searchQuery = trim($_GET['query']); // Get the search query and remove extra spaces

    if (!empty($searchQuery)) {
        // SQL Query to search for the title containing the search word
        $stmt = $conn->prepare("SELECT title, link, description, pubDate FROM rss_items WHERE title LIKE ?");
        $searchTerm = '%' . $searchQuery . '%';
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Travel Advisories</title>
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

        .search-container {
            max-width: 500px;
            margin: 50px auto;
            text-align: center;
        }

        .search-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .search-form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-form button {
            width: 100%;
            padding: 10px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: darkblue;
        }

        .search-results-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .search-results-container h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #262673;
        }

        .results-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .result-item {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .result-item h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #007BFF;
        }

        .result-item p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }

        .result-item a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        .result-item a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Search Travel News</h1>
    </div>

    <!-- Navigation Menu -->
    <div class="nav-container">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="search.php">Search Travel News</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li class="greeting">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! </li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET" class="search-form">
            <input type="text" name="query" placeholder="Search advisories..." required>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Search Results -->
    <div class="search-results-container">
        <?php if (!empty($searchQuery)): ?>
            <h2>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
            <?php if (!empty($searchResults)): ?>
                <div class="results-list">
                    <?php foreach ($searchResults as $result): ?>
                        <div class="result-item">
                            <h3>
                                <a href="viewarticle.php?news_link=<?php echo urlencode($result['link']); ?>">
                                    <?php echo htmlspecialchars($result['title']); ?>
                                </a>
                            </h3>
                            <p><strong>Published:</strong> <?php echo date("d-m-Y H:i", strtotime($result['pubDate'])); ?></p>
                            <p><?php echo htmlspecialchars(mb_substr($result['description'], 0, 150)); ?>...</p>
                            <a href="<?php echo htmlspecialchars($result['link']); ?>" target="_blank">Read More</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No results found for "<?php echo htmlspecialchars($searchQuery); ?>".</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Enter a keyword to search travel news.</p>
        <?php endif; ?>
    </div>
</body>

</html>