<?php
    include("../user_module/auth.php");
    require("../user_module/database.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cooking Competition Results</title>
        <style>
            body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            }
            h1 {
            text-align: center;
            color: #333;
            }
        </style>
    </head>
    <body>
        <h1>Cooking Competition Results</h1>
        <div id="results">
            <?php
            // Include the database connection file
            include 'db_connection.php';

            // Fetch results from the database
            $query = "SELECT * FROM competition_results ORDER BY score DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                echo "<table border='1' style='width:100%; text-align:left;'>";
                echo "<tr><th>Rank</th><th>Participant Name</th><th>Score</th></tr>";
                $rank = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $rank . "</td>";
                    echo "<td>" . htmlspecialchars($row['participant_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['score']) . "</td>";
                    echo "</tr>";
                    $rank++;
                }
                echo "</table>";
            } else {
                echo "<p>No results found.</p>";
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </div>
</html>