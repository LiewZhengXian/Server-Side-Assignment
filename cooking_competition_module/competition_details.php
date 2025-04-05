<?php
    include("../user_module/auth.php");
    require("../user_module/database.php");
    require_once(__DIR__ . "/model/function.php");

    // Fetch competition details based on the competition ID passed in the URL
    $competition_id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $query = "SELECT * FROM competition WHERE competition_id = '$competition_id'";
    $result = mysqli_query($con, $query);
    $competition = mysqli_fetch_assoc($result);
    if (!$competition) {
        echo "<p>Competition not found.</p>";
        exit;
    }
?>
<!DOCTYPE html>
<html>
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
                font-size: 2.5rem;
                margin-top: 20px;
            }

            p {
                text-align: center;
                color: #6c757d; /* Muted gray for descriptions */
                font-size: 1.1rem;
            }

            .table-container {
                margin: 30px auto;
                padding: 20px;
                background-color: #ffffff; /* White background for the table container */
                border-radius: 10px; /* Rounded corners */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
                max-width: 90%; /* Limit the width of the container */
            }

            table {
                width: 100%; /* Full width inside the container */
                border-collapse: collapse;
            }

            th, td {
                border: 1px solid #dee2e6; /* Light gray borders */
                padding: 12px;
                text-align: center; /* Center-align text */
            }

            th {
                background-color: #007bff; /* Blue background for headers */
                color: white; /* White text for headers */
                font-weight: bold;
            }

            td {
                background-color: #f8f9fa; /* Light gray for table cells */
            }

            .btn-primary {
                background-color: #007bff; /* Bootstrap blue */
                border-color: #007bff;
                padding: 8px 16px;
                font-size: 1rem;
                border-radius: 5px;
            }

            .btn-primary:hover {
                background-color: #0056b3; /* Darker blue on hover */
                border-color: #0056b3;
            }

            .submit-recipe-btn {
                display: inline-block;
                margin: 20px auto;
                padding: 12px 24px;
                font-size: 1.2rem;
                color: white;
                background-color: #28a745; /* Green for the button */
                border: none;
                border-radius: 5px;
                text-decoration: none;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: background-color 0.3s ease, transform 0.2s ease;
            }

            .submit-recipe-btn:hover {
                background-color: #218838; /* Darker green on hover */
                transform: translateY(-2px); /* Slight lift on hover */
            }

            .floating-box {
                position: fixed;
                top: 40%;
                left: 50%;
                transform: translateX(-50%);
                padding: 15px;
                border-radius: 5px;
                color: white;
                z-index: 1000;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                animation: fadeOut 3s forwards;
            }

            .floating-box.success {
                background-color: #28a745; /* Green for success */
            }

            .floating-box.error {
                background-color: #dc3545; /* Red for error */
            }

            @keyframes fadeOut {
                0% { opacity: 1; }
                80% { opacity: 1; }
                100% { opacity: 0; display: none; }
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
        <?php
            // Handle vote submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'])) {
                $submission_id = intval($_POST['submission_id']);
                $user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session

                $vote_result = handleVote($submission_id, $user_id, $con);
                $status = $vote_result['status'];
                $message = $vote_result['message'];
            }

            // Display floating box if there's a status and message
            if (isset($status) && isset($message)) {
                echo "<div class='floating-box " . ($status === 'success' ? 'success' : 'error') . "'>$message</div>";
            }       
        ?>
        <div class="container">
            <h1><?php echo $competition['competition_name']; ?></h1>
            <p><?php echo $competition['description']; ?></p>
            <p>Start Date: <?php echo $competition['start_date']; ?></p>
            <p>End Date: <?php echo $competition['end_date']; ?></p>
            <p>What you waiting for? Click the button below to join the competition and submit your recipe!</p>
            <p><em>Note: You can only submit one recipe for this competition.</em></p>
            <div style="text-align: center; margin: 20px auto;">
                <a href="submit_recipe.php?competition_id=<?php echo $competition_id; ?>" class="submit-recipe-btn">
                    Submit Your Recipe
                </a>
            </div>
            <h2 style="text-align: center; margin-top: 30px;">Participants</h2>
            <p>These are the participants who have submitted their recipes for this competition.</p>
            <p>Click the <b>VOTE</b> to vote for your favorite recipe!</p>
            <p><em>Note: You can only vote once for each recipe.</em></p>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Participant Name</th>
                            <th>Recipe</th>
                            <th>Vote</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Fetch participants for the competition
                            $query = "SELECT u.username, r.title, c.submission_id, r.recipe_id    
                                    FROM Recipe r INNER JOIN User u ON r.user_id = u.user_id 
                                    INNER JOIN competition_submission c ON r.recipe_id = c.recipe_id 
                                    WHERE competition_id = '$competition_id'";
                            $result = mysqli_query($con, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['username'] . "</td>";
                                    echo '<td>
                                            <a href="#" 
                                               onclick="loadRecipeDetails(' . $row['recipe_id'] . ');" 
                                               class="text-info"
                                               data-bs-toggle="modal"
                                               data-bs-target="#viewModal">
                                                ' . htmlspecialchars($row['title']) . '
                                            </a>
                                        </td>';
                                    echo "<td>
                                            <form action='' method='POST' style='display:inline;'>
                                                <input type='hidden' name='submission_id' value='" . $row['submission_id'] . "'>
                                                <button type='submit' class='btn btn-primary'>VOTE</button>
                                            </form>
                                        </td>";                    
                                    echo "</tr>";
                                }
                            } else {
                                echo "<p>No participants found for this competition.</p>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- View Recipe Modal -->
            <div class="modal fade" id="viewModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title">Recipe Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="recipeDetails">
                            <!-- Recipe details will be loaded here via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function loadRecipeDetails(recipeId) {
                    console.log("Loading recipe: ", recipeId);
                    const xhr = new XMLHttpRequest();
                    xhr.open("GET", "../recipe_management_module/view_recipe.php?id=" + recipeId, true);
                    xhr.onload = function() {
                        if (this.status === 200) {
                            document.getElementById("recipeDetails").innerHTML = this.responseText;
                        }
                    };
                    xhr.send();
                }
            </script>
        </div>
    </body>
</html>