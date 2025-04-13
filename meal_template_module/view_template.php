<?php
include '../user_module/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$template_id = $_GET['template_id'] ?? null;

if (!$template_id) {
    header("Location: list_templates.php?error=Invalid template ID.");
    exit();
}

// Fetch template and details with correct recipe name
$template_stmt = $con->prepare("
    SELECT 
        mt.template_name, 
        mt.description, 
        mtd.day_of_week, 
        mtd.meal_time, 
        mtd.meal_name, 
        mtd.meal_type, 
        r.title AS recipe_title, 
        mtd.custom_meal
    FROM meal_template mt
    LEFT JOIN meal_template_details mtd ON mt.template_id = mtd.template_id
    LEFT JOIN recipe r ON mtd.recipe_id = r.recipe_id
    WHERE mt.template_id = ? AND mt.user_id = ?
");
$template_stmt->bind_param("ii", $template_id, $user_id);
$template_stmt->execute();
$template_result = $template_stmt->get_result();

$template_data = [];
while ($row = $template_result->fetch_assoc()) {
    $template_data[$row['day_of_week']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meal Plan Template - Recipe Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Navbar (Similar to previous pages) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Recipe Hub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Recipes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="mealPlanningDropdown" role="button" data-bs-toggle="dropdown">
                            Meal Planning
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../meal_planning_module/meal_plan_add.php">Plan a Meal</a></li>
                            <li><a class="dropdown-item" href="../meal_planning_module/meal_plan_list.php">View Schedule</a></li>
                            <li><a class="dropdown-item" href="list_templates.php">Manage Templates</a></li>
                            <li><a class="dropdown-item" href="../meal_planning_module/meal_plan_display.php">Display Schedule Table</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../community_module/Community.php">Community</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Competitions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user_module/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center"><?= htmlspecialchars($template_data[array_key_first($template_data)][0]['template_name']) ?></h1>
        <p class="text-muted"><?= htmlspecialchars($template_data[array_key_first($template_data)][0]['description']) ?></p>

        <?php foreach ($template_data as $day => $meals): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <h4><?= htmlspecialchars($day) ?></h4>
                </div>
                <div class="card-body">
                    <?php foreach ($meals as $meal): ?>
                        <div class="mb-2">
                            <strong><?= htmlspecialchars($meal['meal_time']) ?>:</strong> 
                            <?= htmlspecialchars($meal['meal_name']) ?> 
                            (<?= $meal['meal_type'] === 'recipe' 
                                ? htmlspecialchars($meal['recipe_title'] ?? 'Unknown Recipe') 
                                : 'Custom Meal: ' . htmlspecialchars($meal['custom_meal']) ?>
                            )
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <a href="list_templates.php" class="btn btn-secondary">Back to Templates</a>
    </div>
</body>
</html>
