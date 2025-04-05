<?php
    include("../user_module/auth.php");
    require("../user_module/database.php");

    $competition_id = $_GET['competition_id'] ?? null; // Get the competition ID from the URL
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
        <!-- Simple Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Recipe Hub</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Recipes</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="mealPlanningDropdown" role="button"
                                data-bs-toggle="dropdown">
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
        <div class="container mt-5">
            <h2 class="text-center">Submit Your Recipe</h2>
            <p class="text-center">
                Join the competition and showcase your culinary skills! <br>
                Submit your best recipe and stand a chance to win exciting prizes! <br>
                Got join got chance, no join no chance, so join lah! <br>
            </p>
            <form action="submit_recipes.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="recipe_title" class="form-label">Recipe Title</label>
                    <input type="text" class="form-control" id="recipe_title" name="recipe_title" required>
                </div>
                <div class="mb-3">
                    <label for="recipe_description" class="form-label">Description</label>
                    <textarea class="form-control" id="recipe_description" name="recipe_description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="recipe_image" class="form-label">Upload Image</label>
                    <input type="file" class="form-control" id="recipe_image" name="recipe_image" accept=".jpg, .jpeg, .png, .gif">
                </div>
                <div class="mb-3">
                    <label for="existing_recipe" class="form-label">Choose Existing Recipe</label>
                    <select class="form-select" id="existing_recipe" name="existing_recipe">
                        <option value="">Select an existing recipe</option>
                        <?php
                            $query = "SELECT * FROM recipes WHERE user_id = '$user_id'";
                            $result = mysqli_query($con, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['id']}'>{$row['title']}</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms" class="form-label">I agree to the <a href="#">terms and conditions</a></label>
                </div>
                <button type="submit" class="btn btn-primary">Submit Recipe</button>
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
    </body>
</html>