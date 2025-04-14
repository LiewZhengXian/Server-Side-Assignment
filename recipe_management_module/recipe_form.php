<?php
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
        .ingredient-row,
        .step-row {
            margin-bottom: 10px;
        }

        .form-label {
            font-weight: bold;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include("../navbar.php"); ?>

    <div class="container mt-4 mb-4">
        <div class="card p-4">
            <h2 class="mb-3 text-center"><?php echo $formTitle; ?></h2>
            <form id="recipeForm" method="POST" action="<?php echo $formAction; ?>" enctype="multipart/form-data">
                <!-- Recipe Title and Cuisine -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="title" value="<?php echo htmlspecialchars($recipe['title'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cuisine</label>
                        <select class="form-select" name="cuisine_id" id="cuisine_id" required>
                            <option value="">Select Cuisine</option>
                            <?php while ($row = $cuisines->fetch_assoc()) { ?>
                                <option value="<?php echo $row['cuisine_id']; ?>" <?php echo (isset($recipe['cuisine_id']) && $recipe['cuisine_id'] == $row['cuisine_id']) ? 'selected' : ''; ?>>
                                    <?php echo $row['cuisine_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Category and Image -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" id="category_id" required>
                            <option value="">Select Category</option>
                            <?php while ($row = $categories->fetch_assoc()) { ?>
                                <option value="<?php echo $row['category_id']; ?>" <?php echo (isset($recipe['category_id']) && $recipe['category_id'] == $row['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo $row['category_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Image File</label>
                        <input type="file" class="form-control" name="image_file" id="image_file" accept="image/*">
                        <?php if (!empty($recipe['image_path'])) { ?>
                            <small class="text-muted">Current File: <?php echo basename($recipe['image_path']); ?></small>
                        <?php } ?>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="description" rows="3" required><?php echo htmlspecialchars($recipe['description'] ?? ''); ?></textarea>
                </div>

                <!-- Prep Time, Cook Time, Servings, and Spicy -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Prep Time (minutes)</label>
                        <input type="number" class="form-control" name="prep_time" id="prep_time" min="0" value="<?php echo htmlspecialchars($recipe['prep_time'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cook Time (minutes)</label>
                        <input type="number" class="form-control" name="cook_time" id="cook_time" min="0" value="<?php echo htmlspecialchars($recipe['cook_time'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Servings</label>
                        <input type="number" class="form-control" name="servings" id="servings" min="0" value="<?php echo htmlspecialchars($recipe['servings'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Spicy</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="spicy" id="spicy" <?php echo (isset($recipe['spicy']) && $recipe['spicy']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="spicy">Yes</label>
                        </div>
                    </div>
                </div>

                <!-- Ingredients -->
                <div class="mb-3">
                    <label class="form-label">Ingredients</label>
                    <div id="ingredientsContainer">
                        <?php if (isset($recipe_ingredients) && !empty($recipe_ingredients)) { ?>
                            <?php foreach ($recipe_ingredients as $ingredient) { ?>
                                <div class="ingredient-row row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control ingredient-input" name="ingredient_names[]" value="<?php echo htmlspecialchars($ingredient['ingredient_name']); ?>" placeholder="Search or add new ingredient" list="ingredientList">
                                        <datalist id="ingredientList">
                                            <?php while ($row = $ingredients->fetch_assoc()) { ?>
                                                <option value="<?php echo $row['ingredient_name']; ?>"></option>
                                            <?php } ?>
                                        </datalist>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="any" class="form-control" name="quantities[]" value="<?php echo htmlspecialchars($ingredient['quantity']); ?>" placeholder="Quantity" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="units[]" value="<?php echo htmlspecialchars($ingredient['units']); ?>" placeholder="Units" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger" onclick="removeIngredientRow(this)">Remove</button>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="ingredient-row row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control ingredient-input" name="ingredient_names[]" placeholder="Search or add new ingredient" list="ingredientList">
                                    <datalist id="ingredientList">
                                        <?php while ($row = $ingredients->fetch_assoc()) { ?>
                                            <option value="<?php echo $row['ingredient_name']; ?>"></option>
                                        <?php } ?>
                                    </datalist>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="any" class="form-control" name="quantities[]" placeholder="Quantity" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="units[]" placeholder="Units" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger" onclick="removeIngredientRow(this)">Remove</button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" onclick="addIngredientRow()">Add Ingredient</button>
                </div>

                <!-- Steps -->
                <div class="mb-3">
                    <label class="form-label">Steps</label>
                    <table class="table table-bordered" id="stepsTable">
                        <thead>
                            <tr>
                                <th style="width: 10%;">No.</th>
                                <th style="width: 80%;">Instruction</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="stepsContainer">
                            <?php if (isset($recipe_steps) && !empty($recipe_steps)) { ?>
                                <?php foreach ($recipe_steps as $index => $step) { ?>
                                    <tr class="step-row">
                                        <td><?php echo $index + 1; ?></td>
                                        <td><input type="text" class="form-control" name="steps[]" value="<?php echo htmlspecialchars($step['instruction']); ?>" placeholder="Step description" required></td>
                                        <td><button type="button" class="btn btn-danger" onclick="removeStepRow(this)">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr class="step-row">
                                    <td>1</td>
                                    <td><input type="text" class="form-control" name="steps[]" placeholder="Step description" required></td>
                                    <td><button type="button" class="btn btn-danger" onclick="removeStepRow(this)">Remove</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-secondary mt-2" onclick="addStepRow()">Add Step</button>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg me-2"><?php echo $submitButtonText; ?></button>
                    <a href="recipe.php" class="btn btn-danger btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <?php include '../footer.php'; ?>
</body>

<script>
    let stepCount = <?php echo isset($recipe_steps) ? count($recipe_steps) : 1; ?>;

    function addIngredientRow() {
        const container = document.getElementById('ingredientsContainer');
        const row = document.createElement('div');
        row.className = 'ingredient-row row';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control ingredient-input" name="ingredient_names[]" placeholder="Search or add new ingredient" list="ingredientList">
                <datalist id="ingredientList">
                    <?php
                    $ingredients->data_seek(0); // Reset the result pointer to the beginning
                    while ($row = $ingredients->fetch_assoc()) { ?>
                        <option value="<?php echo $row['ingredient_name']; ?>"></option>
                    <?php } ?>
                </datalist>
            </div>
            <div class="col-md-2">
                <input type="number" step="any" class="form-control" name="quantities[]" placeholder="Quantity" min="0" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="units[]" placeholder="Units" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger" onclick="removeIngredientRow(this)">Remove</button>
            </div>
        `;
        container.appendChild(row);
    }

    function removeIngredientRow(button) {
        button.closest('.ingredient-row').remove();
    }

    function addStepRow() {
        stepCount++;
        const container = document.getElementById('stepsContainer');
        const row = document.createElement('tr');
        row.className = 'step-row';
        row.innerHTML = `
            <td>${stepCount}</td>
            <td><input type="text" class="form-control" name="steps[]" placeholder="Step description" required></td>
            <td><button type="button" class="btn btn-danger" onclick="removeStepRow(this)">Remove</button></td>
        `;
        container.appendChild(row);
    }

    function removeStepRow(button) {
        button.closest('.step-row').remove();
        updateStepNumbers();
    }

    function updateStepNumbers() {
        const rows = document.querySelectorAll('#stepsContainer .step-row');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
        stepCount = rows.length;
    }
</script>

</html>