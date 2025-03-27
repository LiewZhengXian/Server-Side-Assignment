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

                if ($meal_type === 'existing_recipe') {
                    $recipe_id = $_POST[$day . '_' . $meal_time . '_recipe_id'] != '' 
                                 ? $_POST[$day . '_' . $meal_time . '_recipe_id'] 
                                 : null;
                    $custom_meal = null;
                } elseif ($meal_type === 'custom_meal') {
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
</head>
<body>
    <!-- Navbar (Similar to previous pages) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <!-- ... (same as previous navbar) ... -->
    </nav>

    <div class="container mt-4">
        <h1 class="text-center">Create Meal Plan Template</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="template_name" class="form-label">Template Name</label>
                <input type="text" class="form-control" id="template_name" name="template_name" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description (Optional)</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <?php foreach ($days as $day): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h4><?= $day ?></h4>
                    </div>
                    <div class="card-body">
                        <?php foreach ($meal_times as $meal_time): 
                            // Re-execute the query to reset the result pointer
                        ?>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label"><?= $meal_time ?> Meal Name</label>
                                    <input type="text" class="form-control" 
                                           name="<?= $day ?>_<?= $meal_time ?>_name" 
                                           placeholder="Enter meal name" required>
                                </div>
                                
                                <!-- Meal Type Selection -->
                                <div class="col-md-3">
                                    <label class="form-label">Meal Type</label>
                                    <div class="form-check">
                                        <input class="form-check-input meal-type-radio" 
                                               type="radio" 
                                               name="<?= $day ?>_<?= $meal_time ?>_meal_type" 
                                               id="<?= $day ?>_<?= $meal_time ?>_existing_recipe" 
                                               value="existing_recipe" 
                                               checked>
                                        <label class="form-check-label" for="<?= $day ?>_<?= $meal_time ?>_existing_recipe">
                                            Existing Recipe
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input meal-type-radio" 
                                               type="radio" 
                                               name="<?= $day ?>_<?= $meal_time ?>_meal_type" 
                                               id="<?= $day ?>_<?= $meal_time ?>_custom_meal" 
                                               value="custom_meal">
                                        <label class="form-check-label" for="<?= $day ?>_<?= $meal_time ?>_custom_meal">
                                            Custom Meal
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Recipe Selection Section -->
                                <div class="col-md-3 recipe-select-section">
                                    <label class="form-label">Select Recipe</label>
                                    <select class="form-control" 
                                            name="<?= $day ?>_<?= $meal_time ?>_recipe_id">
                                        <option value="">-- Select a Recipe --</option>
                                        <?php 
                                            $recipe_result = $con->query("SELECT recipe_id, title FROM recipe"); // Re-fetch inside the loop
                                            while ($recipe = $recipe_result->fetch_assoc()):
                                        ?>
                                            <option value="<?= $recipe['recipe_id'] ?>">
                                                <?= htmlspecialchars($recipe['title']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <!-- Custom Meal Section -->
                                <div class="col-md-3 custom-meal-section" style="display: none;">
                                    <label class="form-label">Custom Meal Details</label>
                                    <textarea class="form-control" 
                                              name="<?= $day ?>_<?= $meal_time ?>_custom_meal" 
                                              rows="3" 
                                              placeholder="Enter your custom meal details"></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
                
                if (this.value === 'existing_recipe') {
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

</body>
</html>