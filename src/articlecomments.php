<?php
session_start();
include 'database.php'; // Veritabanı bağlantısı

// Yorum ekleme işlemini kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['news_link'], $_POST['comment'])) {
    $news_link = $_POST['news_link']; // Haber linki
    $user_id = $_SESSION['user_id'] ?? 1; // Kullanıcı oturumundan ID (test için varsayılan değer 1)
    $comment = $_POST['comment']; // Yorum içeriği

    // Yorumu veritabanına kaydet
    $stmt = $conn->prepare("INSERT INTO article_comments (news_link, user_id, comment) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("SQL error: " . $conn->error);
    }
    $stmt->bind_param("sis", $news_link, $user_id, $comment);

    if ($stmt->execute()) {
        header("Location: index.php"); // Yorumu ekledikten sonra ana sayfaya yönlendirme
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
