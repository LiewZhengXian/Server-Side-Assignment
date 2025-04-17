<?php
// Start session and include necessary files
include("../user_module/auth.php");
require("../user_module/database.php");
require_once(__DIR__ . "/function/function.php");

// Fetch competition details based on the competition ID passed in the URL
$competition_id = intval($_GET['id']) ?? '';
$query = "SELECT * FROM competition WHERE competition_id = '$competition_id'";
$result = mysqli_query($con, $query);
$competition = mysqli_fetch_assoc($result);

if (!$competition) {
    echo "<p>Competition not found.</p>";
    exit;
}

$competition_start_date = strtotime($competition['start_date']);
$competition_end_date = strtotime($competition['end_date']);

// Check for success or error messages in the URL
$message = $_GET['message'] ?? null;
$status =  $_GET['status'] ?? null;

// Display floating box if there's a message and error
if (isset($status) && isset($message)) {
    $box_class = ($status === 'success') ? 'success' : 'error';
    echo "<div class='floating-box $box_class'>$message</div>";
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
        <link rel="stylesheet" href="style/floating_box.css">
        <style>
            body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f8f9fa;
                /* Light gray background */
            }

            p {
                text-align: center;
                color: #6c757d;
                /* Muted gray for descriptions */
                font-size: 1.1rem;
            }

            .hero {
                background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(' <?php echo $competition['image_path']?>') no-repeat center center/cover;
                background-size: cover;
                color: white;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
                padding: 120px 0;
                width: 100vw;
                height: auto;
                margin: 0;
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
                color: white;
                animation: fadeInUp 1s ease-in-out;
            }

            .table-container {
                margin: 30px auto;
                padding: 20px;
                background-color: #ffffff;
                /* White background for the table container */
                border-radius: 10px;
                /* Rounded corners */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                /* Subtle shadow for depth */
                max-width: 90%;
                /* Limit the width of the container */
            }

            table {
                width: 100%;
                /* Full width inside the container */
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #dee2e6;
                /* Light gray borders */
                padding: 12px;
                text-align: center;
                /* Center-align text */
            }

            th {
                background-color: #007bff;
                /* Blue background for headers */
                color: white;
                /* White text for headers */
                font-weight: bold;
            }

            td {
                background-color: #f8f9fa;
                /* Light gray for table cells */
            }

            .btn-primary {
                background-color: #007bff;
                /* Bootstrap blue */
                border-color: #007bff;
                padding: 8px 16px;
                font-size: 1rem;
                border-radius: 5px;
            }

            .btn-primary:hover {
                background-color: #0056b3;
                /* Darker blue on hover */
                border-color: #0056b3;
            }

            .submit-recipe-btn {
                display: inline-block;
                margin: 20px auto;
                padding: 12px 24px;
                font-size: 1.2rem;
                color: white;
                background-color: #28a745;
                /* Green for the button */
                border: none;
                border-radius: 5px;
                text-decoration: none;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: background-color 0.3s ease, transform 0.2s ease;
            }

            .submit-recipe-btn:hover {
                background-color: #218838;
                /* Darker green on hover */
                transform: translateY(-2px);
                /* Slight lift on hover */
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
        <?php include("../navbar.php"); ?>

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
        <div class="content-wrapper">
            <header>
                <div class="hero text-center">
                    <div class="container">
                        <h1><?php echo $competition['competition_name']; ?></h1>
                        <p><?php echo $competition['description']; ?></p>
                        <p>Start Date: <?php echo $competition['start_date']; ?></p>
                        <p>End Date: <?php echo $competition['end_date']; ?></p>
                    </div>
                </div>
            </header>
            <div id="ongoing-competition-info" class="text-center my-5" style="display: none;">
                <div id="prize-info" class="text-center my-5">
                    <h2 class="text-primary" style="font-size: 2.8rem; font-weight: bold; text-transform: uppercase; margin-bottom: 30px; letter-spacing: 2px;">Prizes</h2>
                    <div class="d-flex flex-column align-items-center">
                        <?php 
                            $query = "SELECT rank, prize FROM competition_prize WHERE competition_id = '$competition_id'";
                            $result = mysqli_query($con, $query);
                            while($row = mysqli_fetch_assoc($result)) {
                                echo '<p class="text-secondary" style="font-size: 1.3rem; margin: 10px 0; background-color: #fff3cd; padding: 15px 25px; border-radius: 10px; box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15); border-left: 5px solid #ffc107;"><strong>' . htmlspecialchars($row['rank']) . '.</strong> ' . htmlspecialchars($row['prize']) . '</p>';
                            }
                        ?>
                    </div>
                </div>
                <div id="recipe-submission" class="text-center my-5">
                    <p class="lead" style="font-size: 1.6rem; color: #495057; margin-bottom: 25px; font-weight: 500;">What are you waiting for? Click the button below to join the competition and submit your recipe!</p>
                    <p style="font-size: 1.1rem; color: #6c757d; margin-bottom: 20px;"><em>Note: You can only submit one recipe for this competition.</em></p>
                    <div class="d-flex justify-content-center">
                        <a href="submit_recipe.php?competition_id=<?php echo $competition_id; ?>" class="btn btn-success btn-lg" style="font-size: 1.3rem; padding: 15px 40px; border-radius: 10px; box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15); transition: transform 0.3s ease, background-color 0.3s ease;">
                            Submit Your Recipe!
                        </a>
                    </div>
                </div>
                <div id="participant-info" class="mt-5">
                    <h2 class="text-primary" style="font-size: 2.8rem; font-weight: bold; text-transform: uppercase; margin-bottom: 30px; letter-spacing: 2px;">Participants</h2>
                    <p class="text-secondary" style="font-size: 1.3rem; margin-bottom: 15px;">These are the participants who have submitted their recipes for this competition.</p>
                    <p class="text-secondary" style="font-size: 1.3rem; margin-bottom: 15px;">Click the <b>VOTE</b> button to vote for your favorite recipe!</p>
                    <p class="text-muted" style="font-size: 1.1rem; margin-bottom: 20px;"><em>Note: You can only vote once for each recipe.</em></p>
                </div>
                </div>
            </div>
            <div id="upcoming-competition-info" class="text-center my-5" style="display: none;">
                <h2 class="text-primary" style="font-size: 2.8rem; font-weight: bold; text-transform: uppercase; margin-bottom: 30px; letter-spacing: 2px;">Stay tuned for the Upcoming Competition!</h2>
                <p>While waiting for this competiton, why not join the <strong><a href="competition_main.php" style="color: black;">Ongoing Competition</a></strong> now to bring the back attractive prizes home!</p>
            </div>

            <div id="past-competition-info" class="text-center my-5" style="display: none;">
                <h2 class="text-primary" style="font-size: 2.8rem; font-weight: bold; text-transform: uppercase; margin-bottom: 30px; letter-spacing: 2px;">Past Competition</h2>
                <p>These are the list of participants who have won the competition with amazing prize with their recipes!</p>
                <p>Join the <strong><a href="competition_main.php" style="color: black;">Ongoing Competition</a></strong> now to bring the back attractive prizes home!</p>
            </div>
            
            <!-- Ongoing Table -->
            <div id="ongoing-competition" class="container table-container">
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

            <!-- Upcoming Table -->
            <div id="upcoming-competition" class="table-container" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th colspan="3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan='3' colspan='3' class="text-center">
                                <strong>Coming Soon! Stay tuned!</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Past Table -->
            <div id="past-competition" class="table-container" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Participant Name</th>
                            <th>Recipe</th>
                            <th>Prize</th>
                            <th>Total Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Fetch the ranking of participants, participants name, recipe title and total number of votes
                            $query = "SELECT 
                                cr.rank,
                                u.username,
                                r.title,
                                r.recipe_id,
                                cr.prize,
                                COUNT(cv.vote_id) AS total_votes
                            FROM 
                                competition_result cr
                                INNER JOIN competition_submission cs ON cr.submission_id = cs.submission_id
                                INNER JOIN Recipe r ON cs.recipe_id = r.recipe_id
                                INNER JOIN User u ON r.user_id = u.user_id
                                LEFT JOIN competition_vote cv ON cs.submission_id = cv.submission_id
                            WHERE 
                                cr.competition_id = ?
                            GROUP BY 
                                cr.rank, u.username, r.title, r.recipe_id
                            ORDER BY 
                                cr.rank ASC";
                            
                            $stmt = mysqli_prepare($con, $query);
                            mysqli_stmt_bind_param($stmt, "i", $competition_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);

                            // Display the ranking of participants
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['rank'] . "</td>";
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
                                    echo "<td>" . (!empty($row['prize']) ? $row['prize'] : 'Appreciation of Participation') . "</td>";
                                    echo "<td>" . $row['total_votes'] . "</td>";
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
                // Define the elements for each competition section and participant info section
                const ongoingCompetition = document.getElementById("ongoing-competition");
                const upcomingCompetition = document.getElementById("upcoming-competition");
                const pastCompetition = document.getElementById("past-competition");
                const ongoingCompetitionInfo = document.getElementById("ongoing-competition-info");
                const upcomingCompetitionInfo = document.getElementById("upcoming-competition-info");
                const pastCompetitionInfo = document.getElementById("past-competition-info");

                // Get the current date and competition start and end dates
                const currentDate = new Date();
                const competitionStartDate = new Date(<?php echo json_encode(date('Y-m-d H:i:s', $competition_start_date)); ?>);
                const competitionEndDate = new Date(<?php echo json_encode(date('Y-m-d H:i:s', $competition_end_date)); ?>);

                // Load competition details based on the current date
                function loadCompetitionDetails() {
                    if (currentDate < competitionStartDate) {
                        // Show upcoming competition and hide others
                        upcomingCompetition.style.display = "block";
                        ongoingCompetition.style.display = "none";
                        pastCompetition.style.display = "none";
                        upcomingCompetitionInfo.style.display = "block";
                    } else if (currentDate > competitionEndDate) {
                        // Show past competition and hide others
                        pastCompetition.style.display = "block";
                        ongoingCompetition.style.display = "none";
                        upcomingCompetition.style.display = "none";
                        pastCompetitionInfo.style.display = "block";
                    } else {
                        // Show ongoing competition and hide others
                        ongoingCompetition.style.display = "block";
                        upcomingCompetition.style.display = "none";
                        pastCompetition.style.display = "none";
                        ongoingCompetitionInfo.style.display = "block";
                    }
                }

                document.addEventListener("DOMContentLoaded", loadCompetitionDetails);

                // Function to load recipe details into the modal
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
        <?php include '../footer.php'; ?>
    </body>
</html>