<?php
session_start();
require '../user_module/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid recipe ID.";
    header("Location: recipe.php");
    exit();
}

$recipe_id = intval($_GET['id']);

// Fetch recipe details
$sql = "SELECT *, TIME_TO_SEC(prep_time) / 60 AS prep_time_minutes, TIME_TO_SEC(cook_time) / 60 AS cook_time_minutes FROM Recipe WHERE recipe_id = ?";
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

// Convert prep_time and cook_time to minutes and format as non-decimal if whole number
$recipe['prep_time'] = (floor($recipe['prep_time_minutes']) == $recipe['prep_time_minutes']) ? intval($recipe['prep_time_minutes']) : $recipe['prep_time_minutes'];
$recipe['cook_time'] = (floor($recipe['cook_time_minutes']) == $recipe['cook_time_minutes']) ? intval($recipe['cook_time_minutes']) : $recipe['cook_time_minutes'];

// Fetch recipe ingredients
$sql = "SELECT ri.quantity, ri.units, i.ingredient_name FROM Recipe_Ingredient ri
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
$sql = "SELECT step_num, instruction FROM Step WHERE recipe_id = ? ORDER BY step_num";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe_steps = [];
while ($row = $result->fetch_assoc()) {
    $recipe_steps[] = $row;
}
$stmt->close();

$formTitle = "Update Recipe";
$formAction = "update_recipe.php?id=" . $recipe_id;
$submitButtonText = "Update Recipe";

include 'recipe_form.php';
?>