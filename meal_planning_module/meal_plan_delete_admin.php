<?php
include '../user_module/database.php';
include("../user_module/auth.php");

// Validate and get meal ID
if (!isset($_GET['meal_id']) || empty($_GET['meal_id'])) {
    header("Location: meal_plan_admin.php");
    exit();
}
$id = intval($_GET['meal_id']);

// Delete meal plan
$stmt = $con->prepare("DELETE FROM meal_plans WHERE meal_id=?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: meal_plan_admin.php");
    exit();
} else {
    echo "Error: " . $con->error;
}
?>
