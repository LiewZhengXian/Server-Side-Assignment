<?php
// Include the authentication and database connection files
include("../../user_module/auth.php");
require("../../user_module/database.php");


// Check if the competition ID is provided in the URL
if (isset($_GET['id'])) {
    $competition_id = intval($_GET['id']);

    // Prepare the SQL statement to delete the competition
    $query = "DELETE FROM competition WHERE competition_id = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        // Bind the competition ID to the statement
        mysqli_stmt_bind_param($stmt,"i", $competition_id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the admin competition page with a success message
            header("Location: admin_competition.php?status=success&message=Competition deleted successfully");
            exit();
        } else {
            // Redirect with an error message if execution fails
            header("Location: admin_competition.php?error=Failed to delete competition");
            exit();
        }
    } else {
        // Redirect with an error message if statement preparation fails
        header("Location: admin_competition.php?error=Failed to prepare statement");
        exit();
    }
} else {
    // Redirect with an error message if no ID is provided
    header("Location: admin_competition.php?error=No competition ID provided");
    exit();
}
?>