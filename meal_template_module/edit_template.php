<?php
include '../user_module/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate and get template ID
if (!isset($_GET['template_id']) || !is_numeric($_GET['template_id'])) {
    header("Location: list_templates.php");
    exit();
}

$template_id = intval($_GET['template_id']);

// Fetch template details
$template_stmt = $con->prepare("
    SELECT * FROM meal_template 
    WHERE template_id = ? AND user_id = ?
");
$template_stmt->bind_param("ii", $template_id, $user_id);
$template_stmt->execute();
$template = $template_stmt->get_result()->fetch_assoc();

if (!$template) {
    header("Location: list_templates.php");
    exit();
}

// Fetch template details
$details_stmt = $con->prepare("
    SELECT * FROM meal_template_details 
    WHERE template_id = ? 
    ORDER BY 
        CASE day_of_week 
            WHEN 'Monday' THEN 1 
            WHEN 'Tuesday' THEN 2 
            WHEN 'Wednesday' THEN 3 
            WHEN 'Thursday' THEN 4 
            WHEN 'Friday' THEN 5 
            WHEN 'Saturday' THEN 6 
            WHEN 'Sunday' THEN 7 
        END,
        CASE meal_time 
            WHEN 'Breakfast' THEN 1 
            WHEN 'Lunch' THEN 2 
            WHEN 'Dinner' THEN 3 
        END
");
$details_stmt->bind_param("i", $template_id);
$details_stmt->execute();
$details_result = $details_stmt->get_result();

// Fetch recipes for dropdown
$recipes_stmt = $con->prepare("SELECT recipe_id, title FROM recipe WHERE user_id = ?");
$recipes_stmt->bind_param("i", $user_id);
$recipes_stmt->execute();
$recipes_result = $recipes_stmt->get_result();

// Days and meal times
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$meal_times = ["Breakfast", "Lunch", "Dinner"];

// Organize details by day and meal time
$template_details = [];
while ($detail = $details_result->fetch_assoc()) {
    $template_details[$detail['day_of_week']][$detail['meal_time']] = $detail;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $template_name = $_POST['template_name'];
    $description = $_POST['description'] ?? null;

    // Start transaction
    $con->begin_transaction();

    try {
        // Update template
        $update_stmt = $con->prepare("UPDATE meal_template SET template_name = ?, description = ? WHERE template_id = ?");
        $update_stmt->bind_param("ssi", $template_name, $description, $template_id);
        $update_stmt->execute();

        // Delete existing details
        $delete_stmt = $con->prepare("DELETE FROM meal_template_details WHERE template_id = ?");
        $delete_stmt->bind_param("i", $template_id);
        $delete_stmt->execute();

        // Insert new details
        $detail_stmt = $con->prepare("
            INSERT INTO meal_template_details 
            (template_id, day_of_week, meal_time, meal_name, meal_type, recipe_id, custom_meal) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($days as $day) {
            foreach ($meal_times as $meal_time) {
                $meal_name = $_POST[$day . '_' . $meal_time . '_name'];
                $meal_type = $_POST[$day . '_' . $meal_time . '_type'] ?? null;

                if ($meal_type === 'recipe') {
                    $recipe_id = $_POST[$day . '_' . $meal_time . '_recipe_id'] ?? null;
                    $custom_meal = null;
                } elseif ($meal_type === 'custom') {
                    $recipe_id = null;
                    $custom_meal = $_POST[$day . '_' . $meal_time . '_custom'] ?? null;
                } else {
                    $recipe_id = null;
                    $custom_meal = null;
                }
                
        
                $detail_stmt->bind_param(
                    "issssss",
                    $template_id,
                    $day,
                    $meal_time,
                    $meal_name,
                    $meal_type,
                    $recipe_id,
                    $custom_meal
                );
        
                $detail_stmt->execute();
            }
        }
        

        $con->commit();
        header("Location: list_templates.php?success=1");
        exit();
    } catch (Exception $e) {
        $con->rollback();
        $error = "Error updating template: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meal Plan Template - Recipe Hub</title>
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
        <h1 class="text-center">Edit Meal Plan Template</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="template_name" class="form-label">Template Name</label>
                <input type="text" class="form-control" id="template_name" name="template_name" 
                       value="<?= htmlspecialchars($template['template_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= 
                    htmlspecialchars($template['description'] ?? '') 
                ?></textarea>
            </div>

            <?php foreach ($days as $day): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h4><?= $day ?></h4>
                    </div>
                    <div class="card-body">
                        <?php foreach ($meal_times as $meal_time): 
                            $detail = $template_details[$day][$meal_time] ?? null;
                        ?>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label"><?= $meal_time ?> Meal Name</label>
                                    <input type="text" class="form-control" 
                                           name="<?= $day ?>_<?= $meal_time ?>_name" 
                                           value="<?= $detail ? htmlspecialchars($detail['meal_name']) : '' ?>" 
                                           placeholder="Enter meal name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Meal Type</label>
                                    <select class="form-select meal-type-select"
                                            name="<?= $day ?>_<?= $meal_time ?>_type">
                                        <option value="recipe"
                                            <?= $detail && $detail['meal_type'] == 'recipe' ? 'selected' : '' ?>>
                                            Recipe
                                        </option>
                                        <option value="custom"
                                            <?= $detail && $detail['meal_type'] == 'custom' ? 'selected' : '' ?>>
                                            Custom Meal
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 recipe-select-section" <?= ($detail && $detail['meal_type'] == 'custom') ? 'style="display:none;"' : '' ?>>
                                    <label class="form-label">Select Recipe</label>
                                    <select class="form-control" name="<?= $day ?>_<?= $meal_time ?>_recipe_id">
                                        <option value="">-- Select a Recipe --</option>
                                        <?php 
                                            $recipes_result = $con->query("SELECT recipe_id, title FROM recipe"); // Re-fetch inside the loop
                                            while ($recipe = $recipes_result->fetch_assoc()):
                                        ?>
                                            <option value="<?= $recipe['recipe_id'] ?>" 
                                                <?= isset($template_details[$day][$meal_time]['recipe_id']) 
                                                    && $template_details[$day][$meal_time]['recipe_id'] == $recipe['recipe_id'] 
                                                    ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($recipe['title']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 custom-meal-input"
                                    <?= $detail && $detail['meal_type'] == 'recipe' ? 'style="display:none;"' : '' ?>>
                                    <label class="form-label">Custom Meal</label>
                                    <input type="text" class="form-control"
                                        name="<?= $day ?>_<?= $meal_time ?>_custom"
                                        value="<?= $detail && $detail['meal_type'] == 'custom' 
                                                    ? htmlspecialchars($detail['custom_meal']) 
                                                    : '' ?>"
                                        placeholder="Enter custom meal">
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Update Template</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.meal-type-select').forEach(select => {
        select.addEventListener('change', function () {
            const row = this.closest('.row');
            const recipeSelectSection = row.querySelector('.recipe-select-section');
            const customMealSection = row.querySelector('.custom-meal-input');
            const recipeSelect = recipeSelectSection.querySelector('select');
            const customMealInput = customMealSection.querySelector('input');

            if (this.value === 'recipe') {
                recipeSelectSection.style.display = 'block';
                customMealSection.style.display = 'none';
                recipeSelect.required = true;
                customMealInput.required = false;
            } else {
                recipeSelectSection.style.display = 'none';
                customMealSection.style.display = 'block';
                recipeSelect.required = false;
                customMealInput.required = true;
            }
        });
    });
</script>


</body>
</html>