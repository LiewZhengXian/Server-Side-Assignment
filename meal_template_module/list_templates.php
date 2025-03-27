<?php
include '../user_module/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch list of meal templates
$stmt = $con->prepare("
    SELECT mt.*, 
    (SELECT COUNT(*) FROM meal_template_details mtd WHERE mtd.template_id = mt.template_id) AS meal_count
    FROM meal_template mt 
    WHERE mt.user_id = ? 
    ORDER BY mt.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$templates_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan Templates - Recipe Hub</title>
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
                            <li><a class="dropdown-item" href="../meal_plan_module/meal_plan_add.php">Plan a Meal</a></li>
                            <li><a class="dropdown-item" href="../meal_plan_module/meal_plan_list.php">View Schedule</a></li>
                            <li><a class="dropdown-item active" href="list_templates.php">Manage Templates</a></li>
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
        <h1 class="text-center">Meal Plan Templates</h1>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="add_template.php" class="btn btn-success">+ Create New Template</a>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Template Name</th>
                    <th>Description</th>
                    <th>Meals Included</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($template = $templates_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($template['template_name']) ?></td>
                    <td><?= htmlspecialchars($template['description'] ?? 'No description') ?></td>
                    <td><?= $template['meal_count'] ?> meals</td>
                    <td><?= date('Y-m-d H:i', strtotime($template['created_at'])) ?></td>
                    <td>
                        <a href="view_template.php?template_id=<?= $template['template_id'] ?>" class="btn btn-info btn-sm">View</a>
                        <a href="edit_template.php?template_id=<?= $template['template_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_template.php?template_id=<?= $template['template_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this template?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>