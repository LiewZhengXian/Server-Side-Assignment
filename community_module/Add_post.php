<?php
include("../user_module/auth.php");
include '../user_module/database.php'; ?>
<?php
if (isset($_POST['post_title']) && isset($_POST['post_description']) ){
$post_title = mysqli_real_escape_string($con, $_POST['post_title']);
$post_description = mysqli_real_escape_string($con, $_POST['post_description']);
$user_id = mysqli_real_escape_string($con, $_SESSION["user_id"]);
$recipe_id = mysqli_real_escape_string($con, $_POST["selected_recipe"]) ?? null;
$selected_recipe = !empty($_POST['selected_recipe']) ? mysqli_real_escape_string($con, $_POST['selected_recipe']) : NULL;



// Insert query for Post table
$insert_query = "INSERT INTO Post (user_id, content, title, recipe_id) 
                VALUES ( '$user_id', '$post_description', '$post_title', '$selected_recipe')";

// Execute the query
if (mysqli_query($con, $insert_query)) {
    
    // Redirect to success page or show success message
    // header("Location: dashboard.php?success=1");
    header("Location: Community.php");
    exit();
} else {
    // Handle error
    echo "Error: " . mysqli_error($con);
}

// Close connection
mysqli_close($con);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Your Recipe</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link active" href="#">Home</a>
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
        <h2 class="mb-4 text-center">Share Your Recipe</h2>

        <!-- Form Start -->
        <form id="quickPostForm" action="Add_post.php" method="post" enctype="multipart/form-data">
            <div class="row mb-3">
                <label for="post_title" class="col-sm-2 col-form-label">Post Title</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Post Title" name="post_title" id="post_title"
                        required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="post_description" class="col-sm-2 col-form-label">Post Description</label>
                <div class="col-sm-10">
                    <textarea class="form-control" rows="3" placeholder="Post Description" name="post_description"
                        id="post_description" required></textarea>
                </div>
            </div>

            <!-- Dropdown Section for Existing Recipes -->
            <div class="row mb-3">
                <label for="selected_recipe" class="col-sm-2 col-form-label">Select an Existing Recipe</label>
                <div class="col-sm-10">
                    <select class="form-control" name="selected_recipe" id="selected_recipe">
                        <option value="">Select a Recipe or Leave Empty</option>
                        <?php
                        // Your SQL query to fetch recipes
                        $sel_query = "SELECT r.title ,r.recipe_id
                                  FROM user u, recipe r 
                                  WHERE u.user_id = r.user_id 
                                  AND u.user_id = '" . mysqli_real_escape_string($con, $_SESSION["user_id"]) . "' 
                                  ORDER BY r.title ASC";
                        $result = mysqli_query($con, $sel_query);

                        // Loop through the results and create an option for each recipe
                        while ($user_recipe = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . htmlspecialchars($user_recipe['recipe_id']) . "'>" . htmlspecialchars($user_recipe['title']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-50">Post Recipe</button>
            </div>
        </form>
        <!-- Form End -->
    </div>

    <!-- Bootstrap JS (optional for features like modals, dropdowns, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>