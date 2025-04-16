<?php
include("../user_module/auth.php");
require("../user_module/database.php");

$competition_id = $_GET['competition_id'] ?? null; // Get the competition ID from the URL
$user_id = $_SESSION['user_id'] ?? null; // Get the user ID from the session

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cooking Competition - Submit Recipes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Navbar -->
    <?php include("../navbar.php"); ?>

    <!-- Page Title and Description -->
    <div class="container mt-5">
        <h2 class="text-center">Submit Your Recipe</h2>
        <p class="text-center">
            Join the competition and showcase your culinary skills! <br>
            Submit your best recipe and stand a chance to win exciting prizes! <br>
            Got join got chance, no join no chance, so join lah! <br>
        </p>
        <form action="submit_recipes.php" method="POST" enctype="multipart/form-data">
            <!-- Radio Button Selection -->
            <div class="mb-3">
                <label for="choose_recipe" class="form-label">Choose Recipe From:</label><br>
                <input type="radio" id="existing_recipe_option" name="recipe_type" value="existing" checked>
                <label for="existing_recipe_option" class="form-label">Existing Recipe</label>
                <input type="radio" id="new_recipe" name="recipe_type" value="new">
                <label for="new_recipe" class="form-label">Upload New Recipe</label>
            </div>

            <!-- New Recipe Form Section -->
            <!-- Still need to edit the details for the new recipe -->
            <div id="new_recipe_section">
                <div class="mb-3">
                    <label for="recipe_title" class="form-label">Recipe Title</label>
                    <input type="text" class="form-control" id="recipe_title" name="recipe_title">
                </div>
                <div class="mb-3">
                    <label for="recipe_description" class="form-label">Description</label>
                    <textarea class="form-control" id="recipe_description" name="recipe_description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="recipe_image" class="form-label">Upload Image</label>
                    <input type="file" class="form-control" id="recipe_image" name="recipe_image" accept=".jpg, .jpeg, .png, .gif">
                </div>
            </div>

            <!-- Existing Recipe Form Section -->
            <div id="existing_recipe_section" style="display: none;">
                <div class="mb-3">
                    <label for="existing_recipe" class="form-label">Choose Existing Recipe</label>
                    <select class="form-select" id="existing_recipe" name="existing_recipe">
                        <option value="">--</option>
                        <?php
                        $query = "SELECT recipe_id, title FROM Recipe WHERE user_id = ?";
                        $stmt = mysqli_prepare($con, $query);
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value=" . $row['recipe_id'] . ">" . $row['title'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms" class="form-label">I agree to the <a href="../cooking_competition_module/terms_and_conditions.php">terms and conditions</a></label>
            </div>
            <button type="submit" class="btn btn-primary mb-3">Submit Recipe</button>
        </form>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $recipe_title = $_POST['recipe_title'];
        $recipe_description = $_POST['recipe_description'];
        $recipe_image = $_FILES['recipe_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($recipe_image);
        move_uploaded_file($_FILES['recipe_image']['tmp_name'], $target_file);

        // Insert the recipe into the database
        $query = "INSERT INTO competition_submissions (competition_id, user_id, title, description, image) 
                          VALUES ('$competition_id', '$user_id', '$recipe_title', '$recipe_description', '$target_file')";
        if (mysqli_query($con, $insert_query)) {
            echo "<div class='alert alert-success mt-3'>Recipe submitted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Error submitting recipe: " . mysqli_error($con) . "</div>";
        }
    }
    ?>
    <script>
        // JavaScript to toggle between new and existing recipe sections
        // Wait for the DOM to load before executing the script
        document.addEventListener('DOMContentLoaded', function() {
            const newRecipeRadio = document.getElementById('new_recipe');
            const existingRecipeRadio = document.getElementById('existing_recipe_option');
            const newRecipeSection = document.getElementById('new_recipe_section');
            const existingRecipeSection = document.getElementById('existing_recipe_section');

            // Function to toggle form sections
            function toggleSections() {
                if (newRecipeRadio.checked) {
                    newRecipeSection.style.display = 'block';
                    existingRecipeSection.style.display = 'none';
                    // Make new recipe fields required
                    document.getElementById('recipe_title').required = true;
                    document.getElementById('recipe_description').required = true;
                    // Make existing recipe field not required
                    document.getElementById('existing_recipe').required = false;
                } else {
                    newRecipeSection.style.display = 'none';
                    existingRecipeSection.style.display = 'block';
                    // Make new recipe fields not required
                    document.getElementById('recipe_title').required = false;
                    document.getElementById('recipe_description').required = false;
                    // Make existing recipe field required
                    document.getElementById('existing_recipe').required = true;
                }
            }

            // Add event listeners to radio buttons
            newRecipeRadio.addEventListener('change', toggleSections);
            existingRecipeRadio.addEventListener('change', toggleSections);

            // Initial toggle based on default selection
            toggleSections();
        });
    </script>

<?php include '../footer.php'; ?>

</body>

</html>