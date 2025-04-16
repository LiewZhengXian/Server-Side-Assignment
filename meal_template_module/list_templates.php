<?php
include '../user_module/database.php';
include("../user_module/auth.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch list of meal templates
$stmt = $con->prepare("
    SELECT mt.*, 
    (SELECT COUNT(*) FROM meal_template_details mtd WHERE mtd.template_id = mt.template_id) AS meal_count
    FROM meal_template mt 
    WHERE mt.user_id = ? 
    ORDER BY mt.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$templates_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Plan Templates - Recipe Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar  -->
    <?php include("../navbar.php"); ?>

    <div class="container mt-4">
        <h1 class="text-center">Meal Plan Templates</h1>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="add_template.php" class="btn btn-success">+ Create New Template</a>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Template Name</th>
                    <th>Description</th>
                    <th>Meals Included</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($template = $templates_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($template['template_name']) ?></td>
                        <td><?= htmlspecialchars($template['description'] ?? 'No description') ?></td>
                        <td><?= $template['meal_count'] ?> meals</td>
                        <td><?= date('Y-m-d H:i', strtotime($template['created_at'])) ?></td>
                        <td>
                            <a href="view_template.php?template_id=<?= $template['template_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="edit_template.php?template_id=<?= $template['template_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_template.php?template_id=<?= $template['template_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this template?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include '../footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>