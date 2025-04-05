<?php
    // Include the authentication and database connection files
    include("../user_module/auth.php");
    require("../user_module/database.php");
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
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f8f9fa; /* Light gray background */
            }

            h1 {
                text-align: center;
                color: #343a40; /* Dark gray for the title */
                font-size: 3rem;
                margin-top: 20px;
                font-weight: bold;
            }

            p {
                text-align: center;
                color: #6c757d; /* Muted gray for descriptions */
                font-size: 1.2rem;
                margin-bottom: 20px;
            }

            h2 {
                margin-left: 1%;
                text-align: left;
                color: #555;
                font-size: 2rem;
                margin-top: 30px;
                font-weight: bold;
            }

            .container {
                margin-top: 20px;
            }

            .card {
                border: none;
                border-radius: 10px;
                overflow: hidden;
                transition: transform 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            }

            .card:hover {
                transform: scale(1.05); /* Slight zoom effect */
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
            }

            .card img {
                width: 100%;
                height: 200px;
                object-fit: cover; /* Ensure the image fits nicely */
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
                background-color: #007bff; /* Bootstrap blue */
                border-color: #007bff;
                padding: 10px 20px;
                font-size: 1rem;
                border-radius: 5px;
                transition: background-color 0.3s, transform 0.2s;
            }

            .btn-primary:hover {
                background-color: #0056b3; /* Darker blue on hover */
                transform: translateY(-2px); /* Slight lift on hover */
            }

            .btn-primary:active {
                transform: translateY(0); /* Reset lift on click */
            }

            .no-competitions {
                text-align: center;
                font-size: 1.2rem;
                color: #6c757d;
                margin-top: 20px;
            }
        </style>
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
        <!-- Page Content -->
        <h1>Cooking Competition</h1>
        <p>Where food becomes your own dream</p>

        <!-- Ongoing Competitions -->
        <h2>Ongoing Competitions</h2>
        <?php
            $query = "SELECT * FROM competition WHERE end_date > NOW() AND start_date <= NOW() ORDER BY start_date DESC";
            $result = mysqli_query($con, $query);

            if (mysqli_num_rows($result) > 0) {
                echo '<div class="container"><div class="row">';
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
                echo '<div class="container"><div class="row">';
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
                echo '<div class="container"><div class="row">';
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
    </body>
</html>