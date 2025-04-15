<?php
include '../user_module/database.php';
include("../user_module/auth.php");


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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Navbar -->
    <?php include("../navbar.php"); ?>


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
    <br>

    <?php include '../footer.php'; ?>

</body>

</html>