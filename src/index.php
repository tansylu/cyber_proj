<?php
// RSS feed URL'sini tanımla
$rss_url = "https://www.ntv.com.tr/seyahat.rss";

// RSS feed'i yükle
$rss = simplexml_load_file($rss_url);

// Hata kontrolü
if ($rss === false) {
    echo "A valid RSS XML response could not be retrieved.";
    exit;
}

// Handle the POST request when the "Read more" button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['news_link'])) {
    $news_link = $_POST['news_link'];
    $response = file_get_contents($news_link);
    echo $response;
}

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

        .news-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .news-item {
            border: 1px solid #ddd;
            padding: 15px;
            width: 300px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .news-item h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #007BFF;
        }

        .news-item p {
            font-size: 14px;
            color: #555;
            line-height: 1.4;
        }

        .news-item a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
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
            <li><a href="search.php">Search Travel News</a></li>
            <li><a href="comment.php">Post Comments</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </div>

    <!-- Dashboard Content Section -->
    <div class="dashboard-content">
        <h2>Latest Travel News</h2>
        <div class="news-container">
            <?php
            // Atom formatındaki 'entry' etiketlerini işle
            foreach ($rss->entry as $item) {
                $title = $item->title; // // Haber başlığı
                $link = $item->link['href']; // Haber bağlantısı
                $content = strip_tags($item->content); // İçerik (HTML temizlenmiş)
                $published = date("d-m-Y H:i", strtotime($item->published)); // Yayınlanma tarihi
            
                // Haber kutusunu ekrana yazdır
                echo '<div class="news-item">';
                echo '<h3>' . htmlspecialchars($title) . '</h3>';
                echo '<p><strong>Published:</strong> ' . $published . '</p>';
                echo '<p>' . htmlspecialchars(mb_substr($content, 0, 150)) . '...</p>';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="news_link" value="' . htmlspecialchars($link) . '">';
                echo '<button type="submit" style="background: none; border: none; color: #007BFF; cursor: pointer; padding: 0; font-size: 1em;">Read more</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>

</html>