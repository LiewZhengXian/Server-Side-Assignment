<?php
require('../user_module/database.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();
    $user_id = $_POST['user_id'] ?? null;
    $post_id = $_POST['post_id'] ?? null;
    $rating_value = $_POST['rating'] ?? null;



    // Check if rating exists
    $query = "SELECT * FROM Rating WHERE user_id = ? AND post_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing rating
        $query = "UPDATE Rating SET rating_value = ? WHERE user_id = ? AND post_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("iii", $rating_value, $user_id, $post_id);
    } else {
        // Insert new rating
        $query = "INSERT INTO Rating (user_id, post_id, rating_value) VALUES (?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("iii", $user_id, $post_id, $rating_value);
    }

    if ($stmt->execute()) {
        header("Location: Community.php");
        exit();

    } else {
        echo json_encode(["success" => false, "message" => "Database error."]);
    }
}
?>