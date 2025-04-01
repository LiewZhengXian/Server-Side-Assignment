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
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>
    <body>
        <h1>Cooking Compeition</h1>
        <p>Where food becomes your own dream</p>
        <h2>Ongoing Competitions</h2>
        <?php
            // Fetch ongoing competitions from the database
            $query = "SELECT * FROM competition WHERE end_date > NOW() ORDER BY start_date DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                echo "<table>";
                echo "<tr><th>Competition ID</th><th>Competition Name</th><th>Date</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['competition_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['competition_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['competition_date']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>There's no ongoing competitions currently.</p>";
            }
        ?>
        <h2>Upcoming Competitions</h2>
        <p>Where exciting cooking competitions are waiting for you</p>
        <?php
            // Fetch upcoming competitions from the database
            $query = "SELECT * FROM competition WHERE start_date > NOW() ORDER BY start_date ASC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                echo "<table>";
                echo "<tr><th>Competition ID</th><th>Competition Name</th><th>Date</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['competition_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['competition_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['competition_date']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>There's no upcoming competitions currently.</p>";
            }
        ?>
        <button onclick="window.location.href='submit_recipes.php'">Submit Your Recipe</button>
        <button onclick="window.location.href='vote_favourites.php'">Vote for Your Favourites</button>
        <button onclick="window.location.href='view_results.php'">View Results</button>
        <h2>Past Competitions</h2>
        <?php
            // Fetch past competitions from the database
            $query = "SELECT * FROM competition ORDER BY start_date DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                echo "<table>";
                echo "<tr><th>Competition ID</th><th>Competition Name</th><th>Date</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['competition_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['competition_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['competition_date']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>There's no past competitions.</p>";
            }

            // Close the database connection
            mysqli_close($conn);
        ?>
    </body>
</html>