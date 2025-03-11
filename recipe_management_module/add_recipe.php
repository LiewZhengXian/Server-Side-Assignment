<?php
session_start();
require '../user_module/database.php';

// Fetch cuisines and categories for dropdowns
$cuisines = $con->query("SELECT * FROM Cuisine");
$categories = $con->query("SELECT * FROM Category");
$ingredients = $con->query("SELECT * FROM Ingredient");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .ingredient-row, .step-row {
            margin-bottom: 10px;
        }
    </style>
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
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'recipe.php' ? 'active' : ''; ?>" href="recipe.php">Recipes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="mealPlanningDropdown" role="button" data-bs-toggle="dropdown">
                            Meal Planning
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Plan a Meal</a></li>
                            <li><a class="dropdown-item" href="#">View Schedule</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./Community.php">Community</a>
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

    <div class="container mt-4 mb-4">
        <h2 class="mb-3">Add New Recipe</h2>
        <form id="addRecipeForm" method="POST" action="save_recipe.php">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cuisine</label>
                    <select class="form-control" name="cuisine_id" required>
                        <option value="">Select Cuisine</option>
                        <?php while ($row = $cuisines->fetch_assoc()) { ?>
                            <option value="<?php echo $row['cuisine_id']; ?>"><?php echo $row['cuisine_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select class="form-control" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php while ($row = $categories->fetch_assoc()) { ?>
                            <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Image URL</label>
                    <input type="url" class="form-control" name="image_url">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3" required></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label">Prep Time (minutes)</label>
                    <input type="number" class="form-control" name="prep_time" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cook Time (minutes)</label>
                    <input type="number" class="form-control" name="cook_time" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Servings</label>
                    <input type="number" class="form-control" name="servings" required>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Spicy</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="spicy" id="spicy">
                        <label class="form-check-label" for="spicy">Yes</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Ingredients</label>
                <div id="ingredientsContainer">
                    <div class="ingredient-row row">
                        <div class="col-md-4">
                            <select class="form-control" name="ingredients[]">
                                <option value="">Select Ingredient</option>
                                <?php while ($row = $ingredients->fetch_assoc()) { ?>
                                    <option value="<?php echo $row['ingredient_id']; ?>"><?php echo $row['ingredient_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="quantities[]" placeholder="Quantity" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="units[]" placeholder="Units" required>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2" onclick="addIngredientRow()">Add Ingredient</button>
            </div>
            <div class="mb-3">
                <label class="form-label">Steps</label>
                <table class="table table-bordered" id="stepsTable">
                    <thead>
                        <tr>
                            <th style="width: 10%;">No.</th>
                            <th style="width: 90%;">Instruction</th>
                        </tr>
                    </thead>
                    <tbody id="stepsContainer">
                        <tr class="step-row">
                            <td>1</td>
                            <td><input type="text" class="form-control" name="steps[]" placeholder="Step description" required></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-secondary mt-2" onclick="addStepRow()">Add Step</button>
            </div>
            <div class="d-flex">
                <button type="submit" class="btn btn-primary btn-lg me-2">Save Recipe</button>
                <a href="recipe.php" class="btn btn-danger btn-lg">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        let stepCount = 1;

        function addIngredientRow() {
            const container = document.getElementById('ingredientsContainer');
            const row = document.createElement('div');
            row.className = 'ingredient-row row';
            row.innerHTML = `
                <div class="col-md-4">
                    <select class="form-control" name="ingredients[]">
                        <option value="">Select Ingredient</option>
                        <?php
                        $ingredients->data_seek(0); // Reset the result pointer to the beginning
                        while ($row = $ingredients->fetch_assoc()) { ?>
                            <option value="<?php echo $row['ingredient_id']; ?>"><?php echo $row['ingredient_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="quantities[]" placeholder="Quantity" required>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="units[]" placeholder="Units" required>
                </div>
            `;
            container.appendChild(row);
        }

        function addStepRow() {
            stepCount++;
            const container = document.getElementById('stepsContainer');
            const row = document.createElement('tr');
            row.className = 'step-row';
            row.innerHTML = `
                <td>${stepCount}</td>
                <td><input type="text" class="form-control" name="steps[]" placeholder="Step description" required></td>
            `;
            container.appendChild(row);
        }
    </script>
</body>

</html>

<?php $con->close(); ?>