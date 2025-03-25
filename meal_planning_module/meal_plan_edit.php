<?php
include '../user_module/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

// Check if meal_id is provided in the URL
if (!isset($_GET['meal_id']) || empty($_GET['meal_id'])) {
    // Redirect to the meal plan list if no meal_id is provided
    header("Location: meal_plan_list.php");
    exit();
}

// Convert to integer to ensure it's a valid ID
$id = intval($_GET['meal_id']);
$user_id = $_SESSION['user_id'];

// Use prepared statement for security
$stmt = $con->prepare("SELECT * FROM meal_plans WHERE meal_id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No matching meal plan found for this user
    header("Location: meal_plan_list.php");
    exit();
}

$meal = $result->fetch_assoc();
$recipe_result = $con->query("SELECT recipe_id, title FROM recipe");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meal_name = $_POST['meal_name'];
    $meal_date = $_POST['meal_date'];
    $meal_time = $_POST['meal_time'];
    $duration = intval($_POST['duration']);
    
    // Determine if using existing recipe or custom meal
    $meal_type = $_POST['meal_type'];
    
    if ($meal_type == 'existing_recipe') {
        $recipe_id = $_POST['recipe_id'] != '' ? $_POST['recipe_id'] : NULL;
        $custom_meal = NULL;
    } else {
        $recipe_id = NULL;
        $custom_meal = $_POST['custom_meal'];
    }
    
    // Update the SQL query and bind parameters
    $stmt = $con->prepare("UPDATE meal_plans SET meal_name=?, meal_date=?, meal_time=?, meal_type=?, recipe_id=?, duration=?, custom_meal=? WHERE meal_id=? AND user_id=?");
    $meal_type = ($meal_type == 'existing_recipe') ? 'recipe' : 'custom';
    $stmt->bind_param("ssssissii", $meal_name, $meal_date, $meal_time, $meal_type, $recipe_id, $duration, $custom_meal, $id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: meal_plan_list.php");
        exit();
    } else {
        echo "Error: " . $con->error;
    }
}

// Determine if this is an existing recipe or custom meal
$meal_type = ($meal['recipe_id'] !== NULL) ? 'existing_recipe' : 'custom_meal';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meal - Recipe Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar (Same as meal_plan_list.php) -->
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
                            <li><a class="dropdown-item" href="meal_plan_add.php">Plan a Meal</a></li>
                            <li><a class="dropdown-item" href="meal_plan_list.php">View Schedule</a></li>
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
        <h1 class="text-center">Meal Planning 📅</h1>
        <p class="text-center">Modify your scheduled meals.</p>

        <div class="card shadow-sm p-4">
            <h2 class="mb-4">Edit Meal</h2>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Meal Name</label>
                    <input type="text" class="form-control" name="meal_name" value="<?= htmlspecialchars($meal['meal_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Meal Date</label>
                    <input type="date" class="form-control" name="meal_date" value="<?= $meal['meal_date'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Meal Time</label>
                    <select class="form-control" name="meal_time">
                        <option value="Breakfast" <?= ($meal['meal_time'] == 'Breakfast') ? 'selected' : '' ?>>Breakfast</option>
                        <option value="Lunch" <?= ($meal['meal_time'] == 'Lunch') ? 'selected' : '' ?>>Lunch</option>
                        <option value="Dinner" <?= ($meal['meal_time'] == 'Dinner') ? 'selected' : '' ?>>Dinner</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Duration (days)</label>
                    <input type="number" class="form-control" name="duration" min="1" value="<?= $meal['duration'] ?>" required>
                </div>
                
                <!-- Meal Type Selection -->
                <div class="mb-3">
                    <label class="form-label">Meal Type</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="meal_type" id="existingRecipe" value="existing_recipe" <?= ($meal_type == 'existing_recipe') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="existingRecipe">
                            Use Existing Recipe
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="meal_type" id="customMeal" value="custom_meal" <?= ($meal_type == 'custom_meal') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="customMeal">
                            Create Custom Meal
                        </label>
                    </div>
                </div>
                
                <!-- Recipe Selection (shows when "Use Existing Recipe" is selected) -->
                <div class="mb-3" id="recipeSelectSection" style="<?= ($meal_type == 'custom_meal') ? 'display: none;' : '' ?>">
                    <label class="form-label">Select Recipe</label>
                    <select class="form-control" name="recipe_id">
                        <option value="">-- Select a Recipe --</option>
                        <?php while ($row = $recipe_result->fetch_assoc()): ?>
                            <option value="<?= $row['recipe_id'] ?>" <?= ($row['recipe_id'] == $meal['recipe_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['title']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <!-- Custom Meal Details (shows when "Create Custom Meal" is selected) -->
                <div class="mb-3" id="customMealSection" style="<?= ($meal_type == 'existing_recipe') ? 'display: none;' : '' ?>">
                    <label class="form-label">Custom Meal Details</label>
                    <textarea class="form-control" name="custom_meal" rows="3" placeholder="Enter your custom meal details, ingredients, or notes here"><?= htmlspecialchars($meal['custom_meal'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Meal</button>
                <a href="meal_plan_list.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle between recipe selection and custom meal input
        document.querySelectorAll('input[name="meal_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'existing_recipe') {
                    document.getElementById('recipeSelectSection').style.display = 'block';
                    document.getElementById('customMealSection').style.display = 'none';
                } else {
                    document.getElementById('recipeSelectSection').style.display = 'none';
                    document.getElementById('customMealSection').style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>