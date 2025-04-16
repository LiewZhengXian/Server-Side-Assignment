<?php
include("../user_module/auth.php");
require("../user_module/database.php");

$competition_id = $_GET['competition_id'] ?? null; // Get the competition ID from the URL
$user_id = $_SESSION['user_id'] ?? null; // Get the user ID from the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected recipe ID from the form
    $recipe_id = $_POST['existing_recipe'] ?? null;

    // Check if a recipe was selected
    if ($recipe_id) {
        // Prepare the SQL query to insert the submission
        $query = "INSERT INTO competition_submission (competition_id, user_id, recipe_id) 
                VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            // Bind the parameters
            mysqli_stmt_bind_param($stmt, "iii", $competition_id, $user_id, $recipe_id);

            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                // Redirect with a success message
                $status = "success";
                $message = "Recipe submitted successfully!";
            } else {
                $status = "error";
                $message = "Failed to submit recipe: " . mysqli_error($con);
            }
        } else {
            $status = "error";
            $message = "Failed to prepare the statement: " . mysqli_error($con);
        }
    } else {
        $status = "error";
        $message = "Please select a recipe to submit.";
    }

    // Redirect to the competition page with a status message
    header("Location: competition_details.php?id=$competition_id&status=$status&message=$message");
    exit();
}
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
            <form action="" method="POST">
                <!-- Existing Recipe Form Section -->
                <div id="existing_recipe_section">
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
                    <p>No existing recipe or wish to submit a new recipe? <strong><a href="../recipe_management_module/add_recipe.php" style="color:black">Click Here</a></strong> to create a new recipe!</p>
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms" class="form-label">I agree to the <a href="../cooking_competition_module/terms_and_conditions.php">terms and conditions</a></label>
                </div>
                <button type="submit" class="btn btn-primary mb-3">Submit Recipe</button>
            </form>
        </div>
        <?php include '../footer.php'; ?>
    </body>
</html>