<?php
session_start();
include 'database.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') || !isset($_SESSION['role'])) {
    echo '<script>alert("Access denied. You must be an admin to view this page."); window.location.href = "index.php";</script>';
    exit();
}

// Handle User Deletion
if (isset($_POST['delete_user_id'])) {
    $delete_user_id = intval($_POST['delete_user_id']);
    
    // Store current admin user ID
    $current_user_id = $_SESSION['user_id'];

    $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($delete_stmt === false) {
        die("SQL Error: " . $conn->error);
    }

    $delete_stmt->bind_param("i", $delete_user_id);
    if ($delete_stmt->execute()) {
        $delete_stmt->close();

        // Check if the deleted user is the current admin
        if ($delete_user_id === $current_user_id) {
            // Reset the session and redirect to logout
            session_unset();
            session_destroy();
            header("Location: logout.php"); // Redirect to logout
            exit();
        } else {
            header("Location: admin.php");
            exit();
        }
    } else {
        $delete_stmt->close();
        die("Failed to delete user: " . $delete_stmt->error);
    }
}

// Fetch all users
$users_stmt = $conn->prepare("SELECT id, username, profile_pic, age, gender, role, created_at FROM users ORDER BY created_at DESC");
if ($users_stmt === false) {
    die("SQL Error: " . $conn->error);
}
$users_stmt->execute();
$users_result = $users_stmt->get_result();

// Handle Comment Deletion
if (isset($_POST['delete_comment_id'])) {
    $delete_comment_id = intval($_POST['delete_comment_id']);
    $delete_stmt = $conn->prepare("DELETE FROM article_comments WHERE id = ?");
    if ($delete_stmt === false) {
        die("SQL Error: " . $conn->error);
    }

    $delete_stmt->bind_param("i", $delete_comment_id);
    if ($delete_stmt->execute()) {
        $delete_stmt->close();
        header("Location: admin.php");
        exit();
    } else {
        $delete_stmt->close();
        die("Failed to delete comment: " . $delete_stmt->error);
    }
}

// Fetch all comments
$comments_stmt = $conn->prepare("SELECT article_comments.id, article_comments.comment, article_comments.created_at, users.username, article_comments.news_link 
                                 FROM article_comments 
                                 JOIN users ON article_comments.user_id = users.id 
                                 ORDER BY article_comments.created_at DESC");
if ($comments_stmt === false) {
    die("SQL Error: " . $conn->error);
}
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Users and Comments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-table th,
        .user-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .user-table th {
            background-color: #1e3d58;
            color: white;
        }
        .user-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .user-table tr:hover {
            background-color: #f1f1f1;
        }
        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #1e3d58;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background-color: #16334a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Panel - Manage Users</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profile Picture</th>
                    <th>Username</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($user['profile_pic'] ?? 'uploads/default_profile.png'); ?>"
                                alt="Profile Picture" class="profile-pic">
                        </td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['age'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($user['gender'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo date("d-m-Y H:i", strtotime($user['created_at'])); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Admin Panel - Manage Comments</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Comment</th>
                    <th>News Article</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($comment = $comments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comment['id']); ?></td>
                        <td><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($comment['news_link']); ?>" target="_blank">View Article</a></td>
                        <td><?php echo htmlspecialchars($comment['created_at']); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                <input type="hidden" name="delete_comment_id" value="<?php echo $comment['id']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="index.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>
