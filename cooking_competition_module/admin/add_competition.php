<?php
// Include the authentication and database connection files
include("../../user_module/auth.php");
require("../../user_module/database.php");
require_once (__DIR__ . "/../function/function.php");

// Check if the user is logged in and is an admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    // Redirect non-admin users to the user competition page or login page
    header("Location: ../competition_main.php");
    exit();
}

// Handle adding competition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['competition_name'])) {
    $competition_name = $_POST['competition_name'];
    $image_path = $_POST['image_path'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $add_competition_result = addCompetition($competition_name, $image_path, $description, $start_date, $end_date, $con);
    $status = $add_competition_result['status'];
    $message = $add_competition_result['message'];
}

// Display floating box if there's a status and message
if (isset($status) && isset($message)) {
   header("Location: admin_competition.php?status=$status&message=$message");
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../style/floating_box.css">
    <title>Add Competition</title>
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include("../../navbar.php"); ?>

    <div class="container mt-5 mb-5">
        <h1 class="text-center mb-4">Add Competition</h1>
        <form method="POST" action="" class="p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label for="competition_name" class="form-label">Competition Name:</label>
                <input type="text" id="competition_name" name="competition_name" class="form-control" maxlength="255" placeholder="Enter Competition Name" required>
            </div>

            <div class="mb-3">
                <label for="image_path" class="form-label">Header Image Path:</label>
                <input type="text" id="image_path" name="image_path" class="form-control" maxlength="255" placeholder="Enter Image Path for Competition" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter Description for Competition" required></textarea>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date:</label>
                <input type="datetime-local" id="start_date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">End Date:</label>
                <input type="datetime-local" id="end_date" name="end_date" class="form-control" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Add Competition</button>
            </div>
        </form>
    </div>
    <?php include '../../footer.php'; ?>
</body>
</html>