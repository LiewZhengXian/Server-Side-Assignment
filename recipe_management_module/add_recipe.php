<?php
session_start();
require '../user_module/database.php';

$formTitle = "Add New Recipe";
$formAction = "save_recipe.php";
$submitButtonText = "Save Recipe";

include 'recipe_form.php';
?>