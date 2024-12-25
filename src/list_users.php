<?php
// Include database connection
include 'database.php';

// Fetch users from the database
$result = $conn->query("SELECT id, username, password, created_at FROM users");

// Check for errors
if ($conn->error) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 20px;
        }

        .user-list-container {
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <div class="user-list-container">
        <h1>List of Users</h1>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Created At</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>