<?php
session_start();
include 'database.php';

// RSS feed URL
$rss_url = "https://www.ntv.com.tr/seyahat.rss";

// Load RSS feed
$rss = simplexml_load_file($rss_url);
if ($rss === false) {
    die("Could not fetch RSS feed.");
}

// Get the article's `news_link` from the URL
if (!isset($_GET['news_link']) || empty($_GET['news_link'])) {
    die("Article not specified.");
}
$news_link = htmlspecialchars($_GET['news_link']);

// Find the article in the RSS feed
$article = null;
foreach ($rss->entry as $item) {
    $link = (string) $item->link['href'] ?: (string) $item->id;
    if ($link === $news_link) {
        $article = $item;
        break;
    }
}

if ($article === null) {
    die("Article not found.");
}

// Handle Comment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['comment'])) {
    $user_id = $_SESSION['user_id'];
    $news_link = $_POST['news_link'];
    $comment = trim($_POST['comment']);


    if (!empty($comment)) {

        /*
        PATCHED:
        This removes all HTML tags and escape special characters. This prevents users from entering HTML tags that can execute stored XSS
        */

        $safe_comment = htmlspecialchars(strip_tags($comment), ENT_QUOTES, 'UTF-8');

        // Prepare and execute the insert query
        $insert_stmt = $conn->prepare("INSERT INTO article_comments (user_id, news_link, comment, created_at) VALUES (?, ?, ?, NOW())");
        if ($insert_stmt === false) {
            die("SQL Error: " . $conn->error);
        }

        $insert_stmt->bind_param("iss", $user_id, $news_link, $safe_comment);
        if ($insert_stmt->execute()) {
            $insert_stmt->close();
            header("Location: viewarticle.php?news_link=" . urlencode($news_link));
            exit();
        } else {
            $insert_stmt->close();
            die("Failed to post comment: " . $insert_stmt->error);
        }
    }
}



// Fetch comments for the article
$comments_stmt = $conn->prepare("SELECT users.username, users.profile_pic, article_comments.comment, article_comments.created_at 
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

        /* Article Section */
        .article {
            margin-bottom: 20px;
        }

        .article h2 {
            color: #333;
        }

        /* Article Images */
        .article img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
            display: block;
            margin: 10px auto;
        }

        /* Comments Section */
        .comment {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: #fafafa;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .comment img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid #ddd;
        }

        /* Comment Text */
        .comment p {
            margin: 0;
            word-wrap: break-word;
        }

        /* Comment Form */
        .comment-form {
            margin-top: 20px;
        }

        .comment-form textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            resize: vertical;
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
        <!-- Display Article -->
        <div class="article">
            <h2><?php echo htmlspecialchars($article->title); ?></h2>
            <p><strong>Published:</strong> <?php echo date("d-m-Y H:i", strtotime($article->published)); ?></p>
            <!-- Render the article content safely -->
            <div>
                <?php echo html_entity_decode(strip_tags($article->content, '<p><img><a><strong><em><ul><ol><li>')); ?>
            </div>
        </div>

        <!-- Display Comments -->
        <div class="comments">
            <h3>Comments</h3>
            <?php while ($comment = $comments_result->fetch_assoc()): ?>

                <div class="comment">
                    <img src="<?php echo htmlspecialchars(!empty($comment['profile_pic']) ? $comment['profile_pic'] : '../uploads/profile_676be065b40b56.99402766.png'); ?>"
                        alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                    <p>


                        <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                        <?php echo htmlspecialchars($comment['comment']); ?>
                    </p>
                </div>
            <?php endwhile; ?>
        </div>


        <!-- Comment Form (Logged-in Users Only) -->
        <div class="comment-form">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="">
                    <h3>Add a Comment</h3>
                    <input type="hidden" name="news_link" value="<?php echo htmlspecialchars($news_link); ?>">
                    <textarea name="comment" placeholder="Write your comment..." required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <!-- Display log in prompt if not logged in -->
                <p><strong>You have to <a href="login.php">log in</a> before you can comment.</strong></p>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>