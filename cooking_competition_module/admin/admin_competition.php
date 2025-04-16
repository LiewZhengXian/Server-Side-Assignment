<?php
// Include the authentication and database connection files
include("../../user_module/auth.php");
require("../../user_module/database.php");

// Fetch competitions from the database
// Determine sorting column and order
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'competition_id';
$allowed_columns = ['competition_id', 'competition_name', 'image_path', 'description', 'start_date', 'end_date'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'competition_id';
}

// Determine sorting order (ASC or DESC)
$sort_order = isset($_GET['order']) && $_GET['order'] === 'DESC' ? 'DESC' : 'ASC';

// Toggle sorting order for the next click
$next_order = $sort_order === 'ASC' ? 'DESC' : 'ASC';

// Build the query
$query = "SELECT * FROM competition ORDER BY $sort_column $sort_order";
$result = mysqli_query($con, $query);

// Check for success or error messages in the URL
$message = isset($_GET['message']) ? $_GET['message'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

// Display floating box if there's a message and error
if (isset($status) && isset($message)) {
    $box_class = ($status === 'success') ? 'success' : 'error';
    echo "<div class='floating-box $box_class'>$message</div>";
}

// Display floating box if there's a status and message 
/*
if (isset($status) && isset($message)) {
    echo "<div class='floating-box " . ($status === 'success' ? 'success' : 'error') . "'>$message</div>";
 } */

 // Search functionality
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = mysqli_real_escape_string($con, $search_query);
$search_sql = "SELECT * FROM competition WHERE competition_name LIKE '%$search_query%' ORDER BY $sort_column $sort_order";
$result = mysqli_query($con, $search_sql);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Competitions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../style/floating_box.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
            color: #495057;
            font-size: 2.5rem;
        }

        .search-box {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-box form {
            display: flex;
            flex-grow: 1; /* Ensures the form takes up available space */
            margin-right: 10px; /* Adds spacing between the form and the button */
        }

        .search-box .form-control {
            flex-grow: 1; /* Ensures the input field stretches to fill available space */
        }

        .add-button {
            float: right;
            margin-bottom: 10px;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .add-button:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;            
            max-width: 100%; /* Ensure it doesn't exceed the container width */
            margin: 0 auto; /* Center the table-container */
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #343a40;
            color: white;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        td {
            text-align: left;
            vertical-align: middle;
            max-width: 350px;
            overflow-wrap: break-word;
        }
        th, td {
            padding: 10px 20px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
            transition: background-color 0.3s ease;
        }
        .actions a {
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .actions a:hover {
            text-decoration: underline;
        img {
            max-width: 150px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 10px 0;
        }
        }
        .table-container {
            display: flex;
            flex-wrap: wrap;
            padding: 40px;
            margin: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include("../../navbar.php"); ?>

    <div class="container mt-4">
        <h1>Admin Dashboard - Competitions</h1>

        <div class="search-box d-flex justify-content-between align-items-center mb-3">
            <form method="GET" class="d-flex" action="">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by Competition Name" aria-label="Search">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            
            <a href="add_competition.php" class="btn btn-success ms-3">Add New Competition</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th onclick="window.location.href='?sort=competition_id&order=<?php echo $next_order; ?>'" style="cursor: pointer; background-color: #343a40; color: white;">ID</th>
                    <th onclick="window.location.href='?sort=competition_name&order=<?php echo $next_order; ?>'" style="cursor: pointer; background-color: #343a40; color: white;">Name</th>
                    <th onclick="window.location.href='?sort=image_path&order=<?php echo $next_order; ?>'" style="cursor: pointer; background-color: #343a40; color: white;">Header Image</th>
                    <th onclick="window.location.href='?sort=description&order=<?php echo $next_order; ?>'" style="cursor: pointer; background-color: #343a40; color: white;">Description</th>
                    <th onclick="window.location.href='?sort=start_date&order=<?php echo $next_order; ?>'" style="cursor: pointer; background-color: #343a40; color: white;">Start Date</th>
                    <th onclick="window.location.href='?sort=end_date&order=<?php echo $next_order; ?>'" style="cursor: pointer; background-color: #343a40; color: white;">End Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['competition_id']; ?></td>
                                <td><?php echo $row['competition_name']; ?></td>
                                <td>
                                    <?php 
                                    if (!empty($row['image_path'])) {
                                        echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="Competition Image" style="max-width: 300px; max-height: 300px;"><br>';
                                        echo $row['image_path'];
                                    } else {
                                        echo 'No Image';
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['start_date']; ?></td>
                                <td><?php echo $row['end_date']; ?></td>
                                <td class="actions">
                                    <div class="d-flex justify-content-center row m-2">
                                        <a href="edit_competition.php?id=<?php echo $row['competition_id']; ?>" class="btn btn-primary btn-sm text-white m-2" style="width: 100px;">Edit</a>
                                        <a href="delete_competition.php?id=<?php echo $row['competition_id']; ?>" class="btn btn-danger btn-sm text-white m-2" style="width: 100px;" onclick="return confirm('Are you sure you want to delete this competition?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No competitions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../../footer.php'; ?>
</body>
</html>

<?php
// Close the database connection
mysqli_close($con);
?>