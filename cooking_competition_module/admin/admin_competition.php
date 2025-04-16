<?php
require("../../user_module/database.php");

// Fetch competitions from the database
$query = "SELECT * FROM competition ORDER BY competition_id ASC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Competitions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .add-button {
            float: right;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard - Competitions</h1>
    <a href="add_competition.php" class="add-button">Add New Competition</a>
    <table>
        <thead>
            <tr>
                <th><a href="?sort=competition_id">ID</a></th>
                <th><a href="?sort=competition_name">Name</a></th>
                <th><a href="?sort=image_path">Header Image Path</a></th>
                <th><a href="?sort=description">Description</a></th>
                <th><a href="?sort=start_date">Start Date</a></th>
                <th><a href="?sort=end_date">End Date</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['competition_id']; ?></td>
                        <td><?php echo $row['competition_name']; ?></td>
                        <td><?php echo $row['image_path']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['start_date']; ?></td>
                        <td><?php echo $row['end_date']; ?></td>
                        <td>
                            <a href="modify_competition.php?id=<?php echo $row['competition_id']; ?>">Modify</a> |
                            <a href="delete_competition.php?id=<?php echo $row['competition_id']; ?>" onclick="return confirm('Are you sure you want to delete this competition?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No competitions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php include '../../footer.php'; ?>
</body>
</html>

<?php
// Close the database connection
mysqli_close($con);
?>