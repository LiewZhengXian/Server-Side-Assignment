<?php
// Include the authentication and database connection files
include("../user_module/auth.php");
require("../user_module/database.php");

// task todo
// 1. error handling (done)
// 2. mvc structure (in future after assignment)
// 3. admin competition management (done)

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cooking Competition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            /*font-family: 'Arial', sans-serif;*/
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            /* Light gray background */
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../assets/competition_main_bg.jpg') no-repeat center center/cover;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            padding: 120px 0;
        }

        .hero h1 {
            text-align: center;
            font-size: 3.5rem;  
            font-weight: bold;
            animation: fadeInDown 1s ease-in-out;
        }

        .hero p {
            text-align: center;
            font-size: 1.3rem;
            margin-top: 20px;
        }

        .competition-container {
            margin: 20px auto;
            padding: 20px;
            max-width: 1200px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);

        }

        /* Initial state for elements to animate */
        .competition-container, .card {
            opacity: 0;
            transform: translateY(20px); /* Matches your fadeInUp animation */
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        /* Visible state when the element is in the viewport */
        .competition-container.visible, .card.visible {
            opacity: 1;
            transform: translateY(0); /* Reset transform */
        }

        .description {
            animation: fadeInUp 1.7s ease-in-out;
        }

        h2 {
            margin-left: 1%;
            text-align: center;
            color: #555;
            font-size: 2rem;
            margin-top: 30px;
            font-weight: bold;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */

            margin: 10px;
        }


        .card:hover {
            transform: scale(1.05);
            /* Slight zoom effect */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            /* Stronger shadow on hover */
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            /* Ensure the image fits nicely */
        }

        .card-body {
            padding: 15px;
            background-color: #fff;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 1rem;
            color: #555;
            margin-bottom: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            /* Bootstrap blue */
            border-color: #007bff;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
            transform: translateY(-2px);
            /* Slight lift on hover */
        }

        .btn-primary:active {
            transform: translateY(0);
            /* Reset lift on click */
        }

        .no-competitions {
            text-align: center;
            font-size: 1.2rem;
            color: #6c757d;
            margin-top: 20px;
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
    <?php include("../navbar.php"); ?>

    <!-- Page Content -->
    <div class="content-wrapper">
        <header>
            <div class="hero text-center">
                <div class="container">
                    <h1>Cooking Competition</h1>
                    <p class="description">Where food becomes your own dream</p>
                </div>
            </div>
        </header>

        <!-- Ongoing Competitions -->
        <h2>Ongoing Competitions</h2>
        <?php
        $query = "SELECT * FROM competition WHERE end_date > NOW() AND start_date <= NOW() ORDER BY start_date DESC";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="competition-container"><div class="row">';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="col-md-4">';
                echo '<div class="card mb-4">';
                echo '<img src="' . htmlspecialchars($row['image_path']) . '" class="card-img-top" alt="Competition Image">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['competition_name']) . '</h5>';
                echo '<p class="card-text">Start Date: ' . htmlspecialchars($row['start_date']) . '</p>';
                echo '<p class="card-text">End Date: ' . htmlspecialchars($row['end_date']) . '</p>';
                echo '<a href="competition_details.php?id=' . $row['competition_id'] . '" class="btn btn-primary">View Details</a>';
                echo '</div></div></div>';
            }
            echo '</div></div>';
        } else {
            echo '<p class="no-competitions">There are no ongoing competitions currently.</p>';
        }
        ?>

        <!-- Upcoming Competitions -->
        <h2>Upcoming Competitions</h2>
        <?php
        $query = "SELECT * FROM competition WHERE start_date > NOW() ORDER BY start_date ASC";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="competition-container"><div class="row">';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="col-md-4">';
                echo '<div class="card mb-4">';
                echo '<img src="' . htmlspecialchars($row['image_path']) . '" class="card-img-top" alt="Competition Image">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['competition_name']) . '</h5>';
                echo '<p class="card-text">Start Date: ' . htmlspecialchars($row['start_date']) . '</p>';
                echo '<p class="card-text">End Date: ' . htmlspecialchars($row['end_date']) . '</p>';
                echo '<a href="competition_details.php?id=' . $row['competition_id'] . '" class="btn btn-primary">View Details</a>';
                echo '</div></div></div>';
            }
            echo '</div></div>';
        } else {
            echo '<p class="no-competitions">There are no upcoming competitions currently.</p>';
        }
        ?>

        <!-- Past Competitions -->
        <h2>Past Competitions</h2>
        <?php
        $query = "SELECT * FROM competition WHERE end_date < NOW() ORDER BY start_date DESC";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="competition-container"><div class="row">';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="col-md-4">';
                echo '<div class="card mb-4">';
                echo '<img src="' . htmlspecialchars($row['image_path']) . '" class="card-img-top" alt="Competition Image">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['competition_name']) . '</h5>';
                echo '<p class="card-text">Start Date: ' . htmlspecialchars($row['start_date']) . '</p>';
                echo '<p class="card-text">End Date: ' . htmlspecialchars($row['end_date']) . '</p>';
                echo '<a href="competition_details.php?id=' . $row['competition_id'] . '" class="btn btn-primary">View Details</a>';
                echo '</div></div></div>';
            }
            echo '</div></div>';
        } else {
            echo '<p class="no-competitions">There are no past competitions currently.</p>';
        }

        // Close the database connection
        mysqli_close($con);
        ?>
    </div>
    <?php include '../footer.php'; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const elements = document.querySelectorAll(".competition-container, .card");

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                        observer.unobserve(entry.target); // Stop observing once the animation is triggered
                    }
                });
            });

            elements.forEach((el) => observer.observe(el));
        });
    </script>
</body>

</html>