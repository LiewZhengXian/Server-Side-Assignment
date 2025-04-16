<?php
include("../user_module/auth.php");
require '../user_module/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to edit a recipe.";
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$isAdmin = $_SESSION['isAdmin']; // Check if the user is an admin

// Validate recipe ID
if (!isset($_GET['recipe_id']) || !is_numeric($_GET['recipe_id'])) {
    $_SESSION['error'] = "Invalid recipe ID.";
    header("Location: recipe.php");
    exit();
}

$recipe_id = intval($_GET['recipe_id']);

// Fetch recipe details and verify ownership or admin privileges
$sql = "SELECT *, TIME_TO_SEC(prep_time) / 60 AS prep_time_minutes, TIME_TO_SEC(cook_time) / 60 AS cook_time_minutes 
        FROM Recipe 
        WHERE recipe_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();
$stmt->close();

if (!$recipe) {
    $_SESSION['error'] = "Recipe not found.";
    header("Location: recipe.php");
    exit();
}

// Check if the user is the owner of the recipe or an admin
if ($recipe['user_id'] != $user_id && $isAdmin != 1) {
    $_SESSION['error'] = "You do not have permission to edit this recipe.";
    header("Location: recipe.php");
    exit();
}

// Convert prep_time and cook_time to minutes and format as non-decimal if whole number
$recipe['prep_time'] = (floor($recipe['prep_time_minutes']) == $recipe['prep_time_minutes']) ? intval($recipe['prep_time_minutes']) : $recipe['prep_time_minutes'];
$recipe['cook_time'] = (floor($recipe['cook_time_minutes']) == $recipe['cook_time_minutes']) ? intval($recipe['cook_time_minutes']) : $recipe['cook_time_minutes'];

// Fetch recipe ingredients
$sql = "SELECT ri.quantity, ri.units, i.ingredient_name 
        FROM Recipe_Ingredient ri
        JOIN Ingredient i ON ri.ingredient_id = i.ingredient_id
        WHERE ri.recipe_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe_ingredients = [];
while ($row = $result->fetch_assoc()) {
    $row['quantity'] = (floor($row['quantity']) == $row['quantity']) ? intval($row['quantity']) : $row['quantity'];
    $recipe_ingredients[] = $row;
}
$stmt->close();

// Fetch recipe steps
$sql = "SELECT step_num, instruction 
        FROM Step 
        WHERE recipe_id = ? 
        ORDER BY step_num";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe_steps = [];
while ($row = $result->fetch_assoc()) {
    $recipe_steps[] = $row;
}
$stmt->close();

// Prepare form variables
$formTitle = "Update Recipe";
$formAction = "update_recipe.php?recipe_id=" . $recipe_id;
$submitButtonText = "Update Recipe";

// Include the recipe form
include 'recipe_form.php';
