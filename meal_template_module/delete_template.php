<?php
include '../user_module/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate and get template ID
if (!isset($_GET['template_id']) || !is_numeric($_GET['template_id'])) {
    header("Location: list_templates.php?error=1");
    exit();
}

$template_id = intval($_GET['template_id']);

// Verify template belongs to user
$verify_stmt = $con->prepare("SELECT template_id FROM meal_template WHERE template_id = ? AND user_id = ?");
$verify_stmt->bind_param("ii", $template_id, $user_id);
$verify_stmt->execute();
$result = $verify_stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: list_templates.php?error=2");
    exit();
}

// Delete template (CASCADE will delete related details)
$delete_stmt = $con->prepare("DELETE FROM meal_template WHERE template_id = ?");
$delete_stmt->bind_param("i", $template_id);

try {
    $delete_stmt->execute();
    header("Location: list_templates.php?success=1");
    exit();
} catch (Exception $e) {
    header("Location: list_templates.php?error=3");
    exit();
}
?>