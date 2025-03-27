<?php
include '../user_module/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Filter options
$display_table = $_GET['display_table'] ?? 'meal_plans'; // Default to meal plans
$template_id = $_GET['template_id'] ?? null; // For filtering templates

// Fetch template data if needed
$templates = [];
if ($display_table === 'meal_template_details') {
    $template_sql = "SELECT DISTINCT template_id FROM meal_template_details";
    $template_result = $con->query($template_sql);
    while ($row = $template_result->fetch_assoc()) {
        $templates[] = $row['template_id'];
    }
}

// Fetch meal plan or template details based on the selected table
if ($display_table === 'meal_plans') {
    $sql = "SELECT * FROM meal_plans WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else {
    $sql = "SELECT * FROM meal_template_details";
    if ($template_id) {
        $sql .= " WHERE template_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $template_id);
    } else {
        $stmt = $con->prepare($sql);
    }
}

$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

// Define weekdays and meal times
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$meal_times = ["Breakfast", "Lunch", "Dinner"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan Display</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Recipe Hub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Recipes</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" id="mealPlanningDropdown" role="button" data-bs-toggle="dropdown">
                        Meal Planning
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="meal_plan_add.php">Plan a Meal</a></li>
                        <li><a class="dropdown-item" href="meal_plan_list.php">View Schedule</a></li>
                        <li><a class="dropdown-item active" href="../meal_template_module/list_templates.php">Manage Templates</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="../community_module/Community.php">Community</a></li>
                <li class="nav-item"><a class="nav-link" href="../user_module/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1 class="text-center">Meal Plan Display</h1>

    <!-- Filter Options -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <label for="display_table" class="form-label">Choose Table:</label>
                <select name="display_table" id="display_table" class="form-select" onchange="this.form.submit()">
                    <option value="meal_plans" <?= $display_table === 'meal_plans' ? 'selected' : '' ?>>Meal Plans</option>
                    <option value="meal_template_details" <?= $display_table === 'meal_template_details' ? 'selected' : '' ?>>Meal Template Details</option>
                </select>
            </div>

            <?php if ($display_table === 'meal_template_details'): ?>
            <div class="col-md-6">
                <label for="template_id" class="form-label">Choose Template:</label>
                <select name="template_id" id="template_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Templates</option>
                    <?php foreach ($templates as $template): ?>
                        <option value="<?= $template ?>" <?= $template == $template_id ? 'selected' : '' ?>>
                            Template ID <?= $template ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>
    </form>

    <!-- Display Table -->
    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <?php if ($display_table === 'meal_plans'): ?>
                    <th>Meal Date</th>
                    <th>Meal Time</th>
                    <th>Meal Name</th>
                    <th>Meal Type</th>
                    <th>Duration</th>
                <?php else: ?>
                    <th>Template ID</th>
                    <th>Day of Week</th>
                    <th>Meal Time</th>
                    <th>Meal Name</th>
                    <th>Meal Type</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php if ($display_table === 'meal_plans'): ?>
                        <td><?= $row['meal_date'] ?></td>
                        <td><?= $row['meal_time'] ?></td>
                        <td><?= $row['meal_name'] ?></td>
                        <td><?= ucfirst($row['meal_type']) ?></td>
                        <td><?= $row['duration'] ?> days</td>
                    <?php else: ?>
                        <td><?= $row['template_id'] ?></td>
                        <td><?= $row['day_of_week'] ?></td>
                        <td><?= $row['meal_time'] ?></td>
                        <td><?= $row['meal_name'] ?></td>
                        <td><?= $row['meal_type'] ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="meal_plan_list.php" class="btn btn-primary">Back to Meal Plans</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
