<?php
include("../user_module/auth.php");
require '../user_module/database.php';

if ($_SESSION['isAdmin'] != 1) {
    header("Location: Community.php?error=Unauthorized+action");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_id'])) {
    $comment_id = intval($_POST['comment_id']);

    // Delete the comment
    $stmt = $con->prepare("DELETE FROM Comment WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->close();

    header("Location: Community.php?message=Comment+deleted+successfully");
    exit();
} else {
    header("Location: Community.php?error=Invalid+request");
    exit();
}
