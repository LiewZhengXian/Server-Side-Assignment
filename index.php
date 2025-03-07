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
                    <a class="nav-link dropdown-toggle" href="#" id="mealPlanningDropdown" role="button" data-bs-toggle="dropdown">
                        Meal Planning
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Plan a Meal</a></li>
                        <li><a class="dropdown-item" href="#">View Schedule</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./community_module/Community.php">Community</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Competitions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./user_module/logout.php">Logout</a>

                </li>

            </ul>
        </div>
    </div>
</nav>

<div class="form">
        <p>User Dashboard</p>
        <p>Access Granted - This page is protected.</p>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
