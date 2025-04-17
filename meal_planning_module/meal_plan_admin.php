<?php
include("../user_module/auth.php");
include '../user_module/database.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information to check if the user is an admin
$sql_user = "SELECT isAdmin FROM user WHERE user_id = ?";
$stmt_user = $con->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

if (!$user || $user['isAdmin'] != 1) {
    // Show an alert, then redirect back to login
    echo "<script>
            alert('You do not have permission to access the admin page.');
            window.location.href = '../meal_planning_module/meal_plan_list.php';
          </script>";
    exit();
}

// Fetch all meal plans
$sql = "SELECT mp.*, r.title AS recipe_title FROM meal_plans mp LEFT JOIN recipe r ON mp.recipe_id = r.recipe_id ORDER BY mp.meal_date ASC";
$result = $con->query($sql);

// Fetch all meal templates
$sql_templates = "SELECT * FROM meal_template ORDER BY created_at ASC";
$result_templates = $con->query($sql_templates);

// Fetch all meal template details
$sql_template_details = "SELECT * FROM meal_template_details ORDER BY template_id ASC, day_of_week ASC, meal_time ASC";
$result_template_details = $con->query($sql_template_details);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Meal Plans - Recipe Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include("../navbar.php"); ?>
    <div class="container mt-4">
        <h1 class="text-center">Admin Meal Plans</h1>

        <!-- Meal Plans Table -->
        <h2>Meal Plans</h2>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="meal_plan_add.php" class="btn btn-success">+ Plan a Meal</a>
        </div>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Meal Name</th>
                    <th>Recipe</th>
                    <th>Duration</th>
                    <th>Actions</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['meal_date'] ?></td>
                    <td><?= $row['meal_time'] ?></td>
                    <td><?= $row['meal_name'] ?></td>
                    <td>
                        <?php if ($row['meal_type'] == 'recipe'): ?>
                        <?= $row['recipe_title'] ?>
                        <?php else: ?>
                        Custom Meal
                        <?php endif; ?>
                    </td>
                    <td><?= $row['duration'] ? $row['duration'] . ' days' : '1 day' ?></td>
                    <td>
                        <a href="meal_plan_edit_admin.php?meal_id=<?= $row['meal_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="meal_plan_delete_admin.php?meal_id=<?= $row['meal_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this meal?');">Delete</a>
                    </td>
                    <td><?= $row['updated_at'] ? date('Y-m-d H:i', strtotime($row['updated_at'])) : 'N/A' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Meal Templates Table -->
        <h2>Meal Templates</h2>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="../meal_template_module/add_template.php" class="btn btn-success">+ Create Template</a>
        </div>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Template Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_templates->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['template_name'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td>
                        <a href="edit_template_admin.php?template_id=<?= $row['template_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_template_admin.php?template_id=<?= $row['template_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this template?');">Delete</a>
                    </td>
                    <td><?= $row['updated_at'] ? date('Y-m-d H:i', strtotime($row['updated_at'])) : 'N/A' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Meal Template Details Table -->
        <h2>Meal Template Details</h2>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Template ID</th>
                    <th>Day of Week</th>
                    <th>Time</th>
                    <th>Meal Name</th>
                    <th>Meal Type</th>
                    <th>Recipe</th>
                    <th>Custom Meal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_template_details->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['template_id'] ?></td>
                    <td><?= $row['day_of_week'] ?></td>
                    <td><?= $row['meal_time'] ?></td>
                    <td><?= $row['meal_name'] ?></td>
                    <td><?= $row['meal_type'] ?></td>
                    <td><?= $row['recipe_id'] ? $row['recipe_id'] : 'N/A' ?></td>
                    <td><?= $row['custom_meal'] ? $row['custom_meal'] : 'N/A' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
