<?php
include("../user_module/auth.php");
require '../user_module/database.php';

$user_id = $_SESSION['user_id'];
$isAdmin = $_SESSION['isAdmin'];

if (isset($_GET['recipe_id'])) {
    $recipe_id = intval($_GET['recipe_id']);

    // Check if the user is the owner or an admin
    $stmt = $con->prepare("SELECT user_id FROM Recipe WHERE recipe_id = ?");
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $stmt->bind_result($owner_id);
    $stmt->fetch();
    $stmt->close();

    if ($owner_id == $user_id || $isAdmin == 1) {
        // Delete related data and the recipe
        $stmt = $con->prepare("DELETE FROM Recipe WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Recipe deleted successfully.";
        header("Location: recipe.php");
        exit();
    } else {
        die("You do not have permission to delete this recipe.");
    }
} else {
    die("Invalid recipe ID.");
}
