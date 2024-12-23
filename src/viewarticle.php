<?php
session_start();
include 'database.php';


// Get the article's news_link from the URL
if (!isset($_GET['news_link'])) {
    die("Article not specified.");
}

$news_link = $_GET['news_link'];

// Fetch the article details
$article_stmt = $conn->prepare("SELECT title, link, description, pubDate FROM rss_items WHERE link = ?");
if ($article_stmt === false) {
    die("SQL Error: " . $conn->error);
}
$article_stmt->bind_param("s", $news_link);
$article_stmt->execute();
$article_result = $article_stmt->get_result();
$article = $article_result->fetch_assoc();

// Fetch comments for the article
$comments_stmt = $conn->prepare("SELECT users.username, article_comments.comment, article_comments.created_at 
                                 FROM article_comments 
                                 JOIN users ON article_comments.user_id = users.id 
                                 WHERE article_comments.news_link = ?");
if ($comments_stmt === false) {
    die("SQL Error: " . $conn->error);
}
$comments_stmt->bind_param("s", $news_link);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Comments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .article {
            margin-bottom: 20px;
        }

        .article h2 {
            color: #333;
        }

        .comments {
            margin-top: 20px;
        }

        .comment-form {
            margin-top: 20px;
        }

        .comment-form textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .comment-form button {
            padding: 10px 15px;
            background-color: #1e3d58;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .comment-form button:hover {
            background-color: #16334a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="article">
            <h2><?php echo htmlspecialchars($article['title']); ?></h2>
            <p><strong>Published:</strong> <?php echo date("d-m-Y H:i", strtotime($article['pubDate'])); ?></p>
            <p><?php echo htmlspecialchars($article['description']); ?></p>
        </div>

        <div class="comments">
            <h3>Comments</h3>
            <?php while ($comment = $comments_result->fetch_assoc()): ?>
                <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p>
            <?php endwhile; ?>
        </div>

        <div class="comment-form">
            <h3>Add a Comment</h3>
            <form method="POST" action="articlecomments.php">
                <input type="hidden" name="news_link" value="<?php echo htmlspecialchars($news_link); ?>">
                <textarea name="comment" placeholder="Write your comment..." required></textarea>
                <button type="submit">Post Comment</button>
            </form>
        </div>
    </div>
</body>
</html>
