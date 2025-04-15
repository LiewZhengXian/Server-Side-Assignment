<?php
include("../user_module/auth.php");
include '../user_module/database.php';

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
    // New code fetching both template_id and template_name from meal_template:
    $template_sql = "SELECT template_id, template_name FROM meal_template WHERE user_id = ?";
    $stmt_templates = $con->prepare($template_sql);
    $stmt_templates->bind_param("i", $user_id);
    $stmt_templates->execute();
    $result_templates = $stmt_templates->get_result();
    while ($row = $result_templates->fetch_assoc()) {
        $templates[] = $row;
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
    <?php include("../navbar.php"); ?>

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
                                <option value="<?= $template['template_id'] ?>" <?= $template['template_id'] == $template_id ? 'selected' : '' ?>>
                                    Template <?= $template['template_id'] ?> - <?= htmlspecialchars($template['template_name']) ?>
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
    <br>
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>