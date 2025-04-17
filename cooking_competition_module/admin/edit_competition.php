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

// Get initial information for the competition
if(isset($_GET["id"])) {
    $competition_id = intval($_GET["id"]);

    // Prepare the SQL query to fetch competition details
    $query = "SELECT * FROM competition c INNER JOIN competition_prize cp ON c.competition_id = cp.competition_id
              WHERE c.competition_id = ?"; // Assuming rank 1 is the main competition details
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
            $rank = $row['rank'];
            $prize = $row['prize'];
        } else {
            header("Location: admin_competition.php?status=error&message=Competition not found");
            exit();
        }
    } else {
        header("Location: admin_competition.php?status=error&message=Failed to fetch competition details");
        exit();
    }
}

// Handle form submission to update competition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['competition_name'])) {
    $competition_name = $_POST['competition_name'];
    $image_path = $_POST['image_path'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $rank = $_POST['rank'];
    $prize = $_POST['prize'];

    // Prepare the SQL query to update competition details
    $update_query = "UPDATE competition SET competition_name = ?, image_path = ?, description = ?, start_date = ?, end_date = ? WHERE competition_id = ?";
    $stmt = mysqli_prepare($con, $update_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssi", $competition_name, $image_path, $description, $start_date, $end_date, $competition_id);

        if (mysqli_stmt_execute($stmt)) {
            // Update the competition prize details
            $update_prize_query = "UPDATE competition_prize SET rank = ?, prize = ? WHERE competition_id = ?";
            $stmt_prize = mysqli_prepare($con, $update_prize_query);
            if ($stmt_prize) {
                mysqli_stmt_bind_param($stmt_prize, "isi", $rank, $prize, $competition_id);
                if(mysqli_stmt_execute($stmt_prize)) {
                    // Successfully updated the competition and prize
                    $status = "success";
                    $message = "Competition updated successfully!";
                } else {
                    $status = "error";
                    $message = "Failed to update prize: " . mysqli_error($con);
                }
                mysqli_stmt_close($stmt_prize);
            } else {
                $status = "error";
                $message = "Failed to prepare update prize statement: " . mysqli_error($con);
            }
        } else {
            $status = "error";
            $message = "Failed to update competition: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
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

            <div class="row mb-3">
                <div class="col-3">
                    <label for="rank" class="form-label">Rank:</label>
                    <input type="number" id="rank" name="rank" class="form-control" min="1" max="10" value="<?php echo htmlspecialchars($rank) ?>" required>
                </div>
                <div class="col-9">
                    <label for="prize" class="form-label">Prize:</label>
                    <input type="text" id="prize" name="prize" class="form-control" maxlength="255" value="<?php echo htmlspecialchars($prize) ?>" required>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Competition</button>
            </div>
        </form>
    </div>
    <?php include '../../footer.php'; ?>
</body>
</html>