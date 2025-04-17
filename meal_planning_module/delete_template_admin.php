<?php
include '../user_module/database.php';
include("../user_module/auth.php");

// Validate and get template ID
if (!isset($_GET['template_id']) || !is_numeric($_GET['template_id'])) {
    header("Location: meal_plan_admin.php?error=1");
    exit();
}
$template_id = intval($_GET['template_id']);

// Begin transaction
$con->begin_transaction();
try {
    // Delete from meal_template_details
    $delete_details_stmt = $con->prepare("DELETE FROM meal_template_details WHERE template_id = ?");
    $delete_details_stmt->bind_param("i", $template_id);
    $delete_details_stmt->execute();

    // Delete from meal_template
    $delete_template_stmt = $con->prepare("DELETE FROM meal_template WHERE template_id = ?");
    $delete_template_stmt->bind_param("i", $template_id);
    $delete_template_stmt->execute();

    // Commit transaction
    $con->commit();
    header("Location: meal_plan_admin.php?success=1");
    exit();
} catch (Exception $e) {
    // Rollback transaction
    $con->rollback();
    header("Location: meal_plan_admin.php?error=3");
    exit();
}
?>
