<?php
// Include the authentication and database connection files
include("../../user_module/auth.php");
require("../../user_module/database.php");
require_once (__DIR__ . "/../function/function.php");

// Get initial information for the competition
if(isset($_GET["id"])) {
    $competition_id = intval($_GET["id"]);

    // Prepare the SQL query to fetch competition details
    $query = "SELECT * FROM competition WHERE competition_id = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $competition_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $competition_name = $row['competition_name'];
            $image_path = $row['image_path'];
            $description = $row['description'];
            $start_date = $row['start_date'];
            $end_date = $row['end_date'];
        } else {
            $status = "error";
            $message = "Competition not found!: " . mysqli_error($con);
        }
    } else {
        $status = "error";
        $message = "Failed to fetch competition details: " . mysqli_error($con);
    }

    header("Location: admin_competition.php?status=$status&message=$message");
    exit();
}

// Handle form submission to update competition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['competition_name'])) {
    $competition_name = $_POST['competition_name'];
    $image_path = $_POST['image_path'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Prepare the SQL query to update competition details
    $update_query = "UPDATE competition SET competition_name = ?, image_path = ?, description = ?, start_date = ?, end_date = ? WHERE competition_id = ?";
    $stmt = mysqli_prepare($con, $update_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssi", $competition_name, $image_path, $description, $start_date, $end_date, $competition_id);

        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the admin competition page with a success message
            $status = "success";
            $message = "Competition updated successfully!";
        } else {
            $status = "error";
            $message = "Failed to update competition: " . mysqli_error($con);
        }
    } else {
        $status = "error";
        // Prepare the statement failed
        $message = "Failed to prepare update statement: " . mysqli_error($con);
    }

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
</head>
<body>
    <!-- Navbar -->
    <?php include("../../navbar.php"); ?>

    <div class="container mt-5 mb-5">
        <h1 class="text-center mb-4">Edit Competition</h1>
        <form method="POST" action="" class="p-4 border rounded shadow-sm bg-light">
            <div class="mb-3">
                <label for="competition_name" class="form-label">Competition Name:</label>
                <input type="text" id="competition_name" name="competition_name" class="form-control" maxlength="255" value="<?php echo htmlspecialchars($competition_name); ?>" required>
            </div>

            <div class="mb-3">
                <label for="image_path" class="form-label">Header Image Path:</label>
                <input type="text" id="image_path" name="image_path" class="form-control" maxlength="255" value="<?php echo htmlspecialchars($image_path); ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date:</label>
                <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($start_date)); ?>" required>
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">End Date:</label>
                <input type="datetime-local" id="end_date" name="end_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($end_date)); ?>" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Competition</button>
            </div>
        </form>
    </div>
    <?php include '../../footer.php'; ?>
</body>
</html>