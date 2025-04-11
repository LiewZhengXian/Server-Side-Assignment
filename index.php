<?php
include("./user_module/auth.php");
require('./user_module/database.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <?php include("./navbar.php");
    ?>

    <header class="text-center my-4">
        <h1 class="mt-3">Welcome to Recipe Hub!</h1>
        <p>Your one-stop destination for delicious recipes, meal planning, and competitions.</p>
    </header>

    <!-- Feature Cards -->
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Recipes 🍽️</h5>
                        <p class="card-text">Discover and share amazing recipes.</p>
                        <a href="./recipe_management_module/recipe.php" class="btn btn-primary">Explore</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Meal Planning 📅</h5>
                        <p class="card-text">Plan your meals and stay organized.</p>
                        <a href="#" class="btn btn-primary">Start Planning</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Community 💬</h5>
                        <p class="card-text">Join discussions and connect with fellow food lovers.</p>
                        <a href="./community_module/Community.php" class="btn btn-primary">Join Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Competitions 🏆</h5>
                        <p class="card-text">Participate and win exciting prizes.</p>
                        <a href="#" class="btn btn-primary">View Competitions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>