<?php
require('../user_module/database.php');
require('../user_module/auth.php');
if (isset($_POST["commentText"]) && isset($_POST["post_id"]) && isset($_SESSION["user_id"])) {
    session_start();
    $user_id = $_POST['user_id'] ?? null;
    $post_id = $_POST['post_id'] ?? null;
    $content = $_POST['commentText'] ?? null;
    // Prevent SQL injection
    $user_id = mysqli_real_escape_string($con, $user_id);
    $post_id = mysqli_real_escape_string($con, $post_id);
    $content = mysqli_real_escape_string($con, $content);

    // Check if rating exists

    $insert_query = "INSERT INTO comment (user_id, post_id , content) 
                VALUES ( '$user_id', '$post_id', '$content')";
    // Execute the query
    if (mysqli_query($con, $insert_query)) {

        // Redirect to success page or show success message
        // header("Location: dashboard.php?success=1");
        header("Location: Community.php");
        exit();
    } else {
        // Handle error
        echo "Error: " . mysqli_error($con);
    }

    // Close connection
    mysqli_close($con);
}
?>