<?php
include("../user_module/auth.php");
require '../user_module/database.php';

if ($_SESSION['isAdmin'] != 1) {
    header("Location: Community.php?error=Unauthorized+action");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);

    // Delete the ratings associated with the post
    $stmt = $con->prepare("DELETE FROM Rating WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    // Delete the comments associated with the post
    $stmt = $con->prepare("DELETE FROM Comment WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    // Delete the post itself
    $stmt = $con->prepare("DELETE FROM Post WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    header("Location: Community.php?message=Post+deleted+successfully");
    exit();
} else {
    header("Location: Community.php?error=Invalid+request");
    exit();
}
