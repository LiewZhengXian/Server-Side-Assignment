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
    <style>
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./assets/banner1.jpg') no-repeat center center/cover;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            padding: 120px 0;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
            animation: fadeInDown 1s ease-in-out;
        }

        .hero p {
            font-size: 1.3rem;
            margin-top: 20px;
            animation: fadeInUp 1s ease-in-out;
        }

        .hero .btn {
            margin-top: 30px;
            padding: 15px 30px;
            font-size: 1.2rem;
            animation: fadeInUp 1.5s ease-in-out;
        }

        /* Feature Section */

        /* Features Container */
        .features-container {
            background-color: #D3D3D3;
            /* Light grey background */
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .feature-row {
            margin-bottom: 50px;
        }

        .feature-row img {
            width: 100%;
            /* Ensures responsiveness */
            max-width: 750px;
            /* Fixed width for uniformity */
            height: 300px;
            /* Fixed height for uniformity */
            object-fit: cover;
            /* Ensures the image scales properly without distortion */
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            display: block;
            margin: 0 auto;
            /* Centers the image horizontally */
        }

        .feature-row .feature-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .feature-row h5 {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .feature-row p {
            font-size: 1rem;
            color: #6c757d;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Feature Section Enhancements */
        .feature-row .feature-text {
            background-color: #343a40;
            /* Dark gray background */
            color: #ffffff;
            /* Light text color */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .feature-row h5 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ffffff;
            /* White text for headings */
        }

        .feature-row p {
            font-size: 1rem;
            color: #dcdcdc;
            /* Slightly lighter gray for paragraph text */
        }

        .feature-row .btn-primary {
            margin-top: 15px;
            background-color: #007bff;
            /* Keep button color consistent */
            border: none;
            color: #ffffff;
            /* White text for buttons */
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .feature-row .btn-primary:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
            transform: scale(1.05);
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <?php include("./navbar.php"); ?>

    <!-- Hero Section -->
    <header class="hero text-center">
        <div class="container">
            <h1>Welcome to Recipe Hub!</h1>
            <p>Your one-stop destination for delicious recipes, meal planning, and competitions.</p>
            <a href="./recipe_management_module/recipe.php" class="btn btn-primary btn-lg">Explore Recipes</a>
        </div>
    </header>

    <!-- Feature Section -->
    <div class="container my-5">
        <h2 class="text-center mb-5" style="font-weight: bold; color: #343a40;">What We Offer</h2>

        <!-- Features Container -->
        <div class="features-container p-4">
            <!-- Feature 1 -->
            <div class="row feature-row align-items-center">
                <div class="col-md-6">
                    <img src="./assets/recipes.jpg" alt="Recipes" class="img-fluid">
                </div>
                <div class="col-md-6 feature-text">
                    <h5>Recipes 🍽️</h5>
                    <p>Discover and share amazing recipes from around the world. Whether you're a beginner or a pro, there's something for everyone.</p>
                    <a href="./recipe_management_module/recipe.php" class="btn btn-primary">Explore Recipes</a>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="row feature-row align-items-center flex-md-row-reverse">
                <div class="col-md-6">
                    <img src="./assets/meal_plan.jpeg" alt="Meal Planning" class="img-fluid">
                </div>
                <div class="col-md-6 feature-text">
                    <h5>Meal Planning 📅</h5>
                    <p>Plan your meals and stay organized with our easy-to-use meal planning tools. Save time and eat healthier.</p>
                    <a href="./meal_planning_module/meal_plan_add.php" class="btn btn-primary">Start Planning</a>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="row feature-row align-items-center">
                <div class="col-md-6">
                    <img src="./assets/community.jpg" alt="Community" class="img-fluid">
                </div>
                <div class="col-md-6 feature-text">
                    <h5>Community 💬</h5>
                    <p>Join discussions and connect with fellow food lovers. Share your experiences, tips, and favorite recipes.</p>
                    <a href="./community_module/Community.php" class="btn btn-primary">Join Now</a>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="row feature-row align-items-center flex-md-row-reverse">
                <div class="col-md-6">
                    <img src="./assets/competition.jpg" alt="Competitions" class="img-fluid">
                </div>
                <div class="col-md-6 feature-text">
                    <h5>Competitions 🏆</h5>
                    <p>Participate in exciting cooking competitions and win amazing prizes. Show off your culinary skills!</p>
                    <a href="./cooking_competition_module/competition_main.php" class="btn btn-primary">View Competitions</a>
                </div>
            </div>
        </div>
    </div>
    <?php include './footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>