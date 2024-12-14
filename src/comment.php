<?php
echo "<h1>Post a Comment</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = htmlspecialchars($_POST['comment']);
    echo "<p>Your comment: $comment</p>";
}
?>

<form method="POST">
    <textarea name="comment" placeholder="Write your comment" required></textarea>
    <button type="submit">Post Comment</button>
</form>