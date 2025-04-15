<?php
include '../user_module/database.php';
include("../user_module/auth.php");


if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

if (!isset($_GET['meal_id']) || empty($_GET['meal_id'])) {
    header("Location: meal_plan_list.php");
    exit();
}

$id = intval($_GET['meal_id']);
$user_id = $_SESSION['user_id'];

$stmt = $con->prepare("DELETE FROM meal_plans WHERE meal_id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    header("Location: meal_plan_list.php");
    exit();
} else {
    echo "Error: " . $con->error;
}
?>
