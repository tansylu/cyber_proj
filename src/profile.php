<?php
echo "<h1>Your Profile</h1>";
echo "<p>Upload your profile picture:</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $uploadDir = '../uploads/';
    $uploadFile = $uploadDir . basename($_FILES['profile_pic']['name']);

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadFile)) {
        echo "<p>File uploaded successfully!</p>";
        echo "<img src='/uploads/" . basename($_FILES['profile_pic']['name']) . "' alt='Profile Picture'>";
    } else {
        echo "<p>File upload failed.</p>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="profile_pic" required>
    <button type="submit">Upload</button>
</form>