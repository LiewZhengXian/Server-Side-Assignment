<?php
include '../user_module/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch recipes for dropdown - corrected query to reset result pointer
$recipe_result = $con->query("SELECT recipe_id, title FROM recipe");

// Days and meal times
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$meal_times = ["Breakfast", "Lunch", "Dinner"];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $template_name = $_POST['template_name'];
    $description = $_POST['description'] ?? null;

    // Start transaction
    $con->begin_transaction();

    try {
        // Insert template
        $template_stmt = $con->prepare("INSERT INTO meal_template (user_id, template_name, description) VALUES (?, ?, ?)");
        $template_stmt->bind_param("iss", $user_id, $template_name, $description);
        $template_stmt->execute();
        $template_id = $con->insert_id;

        // Insert template details
        $detail_stmt = $con->prepare("
        INSERT INTO meal_template_details 
        (template_id, day_of_week, meal_time, meal_name, meal_type, recipe_id, custom_meal) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $detail_stmt->bind_param(
            "issssss",  // 's' for the `meal_type` as a string
            $template_id,
            $day,
            $meal_time,
            $meal_name,
            $meal_type,  // Added meal_type here
            $recipe_id,
            $custom_meal
        );
    

        foreach ($days as $day) {
            foreach ($meal_times as $meal_time) {
                $meal_name = $_POST[$day . '_' . $meal_time . '_name'];
                $meal_type = $_POST[$day . '_' . $meal_time . '_meal_type'] ?? null;

                if ($meal_type === 'recipe') { // Change 'existing_recipe' to 'recipe'
                    $recipe_id = $_POST[$day . '_' . $meal_time . '_recipe_id'] != '' 
                        ? $_POST[$day . '_' . $meal_time . '_recipe_id'] 
                        : null;
                    $custom_meal = null;
                }
                 elseif ($meal_type === 'custom_meal') {
                    $recipe_id = null;
                    $custom_meal = $_POST[$day . '_' . $meal_time . '_custom_meal'] ?? null;
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
        $error = "Error creating template: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Meal Plan Template - Recipe Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .day-container {
        display: none;
    }
    .day-container.active {
        display: block;
    }
</style>
<style>
    .is-invalid {
        border-color: #dc3545;
    }
</style>



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
        <h1 class="text-center">Create Meal Plan Template</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" id="mealPlanForm">
            <div class="mb-3">
                <label for="template_name" class="form-label">Template Name</label>
                <input type="text" class="form-control" id="template_name" name="template_name" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <div class="navigation-buttons mb-3">
                <button type="button" class="btn btn-secondary" id="prevDay">Previous Day</button>
                <button type="button" class="btn btn-secondary" id="nextDay">Next Day</button>
            </div>

            <?php foreach ($days as $index => $day): ?>
                <div class="day-container <?= $index === 0 ? 'active' : '' ?>" id="<?= strtolower($day) ?>">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4><?= $day ?></h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($meal_times as $meal_time): ?>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label"><?= $meal_time ?> Meal Name</label>
                                        <input type="text" class="form-control" name="<?= $day ?>_<?= $meal_time ?>_name" placeholder="Enter meal name" required>
                                    </div>
                                    <!-- Meal Type Selection -->
                                    <div class="col-md-3">
                                        <label class="form-label">Meal Type</label>
                                        <div class="form-check">
                                            <input class="form-check-input meal-type-radio" type="radio" name="<?= $day ?>_<?= $meal_time ?>_meal_type" id="<?= $day ?>_<?= $meal_time ?>_existing_recipe" value="recipe" checked>
                                            <label class="form-check-label" for="<?= $day ?>_<?= $meal_time ?>_existing_recipe">
                                                Existing Recipe
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input meal-type-radio" type="radio" name="<?= $day ?>_<?= $meal_time ?>_meal_type" id="<?= $day ?>_<?= $meal_time ?>_custom_meal" value="custom">
                                            <label class="form-check-label" for="<?= $day ?>_<?= $meal_time ?>_custom_meal">
                                                Custom Meal
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Recipe Selection Section -->
                                    <div class="col-md-3 recipe-select-section">
                                        <label class="form-label">Select Recipe</label>
                                        <select class="form-control" name="<?= $day ?>_<?= $meal_time ?>_recipe_id">
                                            <option value="">-- Select a Recipe --</option>
                                            <?php 
                                            $recipe_result = $con->query("SELECT recipe_id, title FROM recipe"); // Re-fetch inside the loop
                                            while ($recipe = $recipe_result->fetch_assoc()): ?>
                                                <option value="<?= $recipe['recipe_id'] ?>">
                                                    <?= htmlspecialchars($recipe['title']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <!-- Custom Meal Section -->
                                    <div class="col-md-3 custom-meal-section" style="display: none;">
                                        <label class="form-label">Custom Meal Details</label>
                                        <textarea class="form-control" name="<?= $day ?>_<?= $meal_time ?>_custom_meal" rows="3" placeholder="Enter your custom meal details"></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>


            <button type="submit" class="btn btn-primary">Create Template</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle between recipe selection and custom meal input for each meal
        document.querySelectorAll('.meal-type-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const row = this.closest('.row');
                const recipeSelectSection = row.querySelector('.recipe-select-section');
                const customMealSection = row.querySelector('.custom-meal-section');
                const recipeSelect = recipeSelectSection.querySelector('select');
                const customMealTextarea = customMealSection.querySelector('textarea');
                
                if (this.value === 'recipe') { // Change 'existing_recipe' to 'recipe'
                    recipeSelectSection.style.display = 'block';
                    customMealSection.style.display = 'none';
                    recipeSelect.required = true;
                    customMealTextarea.required = false;
                } else {
                    recipeSelectSection.style.display = 'none';
                    customMealSection.style.display = 'block';
                    recipeSelect.required = false;
                    customMealTextarea.required = true;
                }

            });
        });
    </script>
    <script>
        function toggleMealInput() {
            const recipeSelect = document.getElementById('recipe_select');
            const customMealInput = document.getElementById('custom_meal_input');

            if (recipeSelect.value) {
                customMealInput.disabled = true;
            } else {
                customMealInput.disabled = false;
            }
        }
    </script>
    <script>
        const days = <?= json_encode($days) ?>;
        let currentDayIndex = 0;

        document.getElementById('prevDay').addEventListener('click', () => {
            if (currentDayIndex > 0) {
                document.getElementById(days[currentDayIndex].toLowerCase()).classList.remove('active');
                currentDayIndex--;
                document.getElementById(days[currentDayIndex].toLowerCase()).classList.add('active');
            }
        });

        document.getElementById('nextDay').addEventListener('click', () => {
            if (currentDayIndex < days.length - 1) {
                document.getElementById(days[currentDayIndex].toLowerCase()).classList.remove('active');
                currentDayIndex++;
                document.getElementById(days[currentDayIndex].toLowerCase()).classList.add('active');
            }
        });
    </script>
<script>
    document.getElementById('mealPlanForm').addEventListener('submit', function(event) {
        let isValid = true;
        const requiredFields = document.querySelectorAll('#mealPlanForm [required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
</script>



</body>
</html>