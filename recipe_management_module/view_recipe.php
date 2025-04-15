<?php
require '../user_module/database.php';

if (isset($_GET['recipe_id'])) {
    $recipe_id = intval($_GET['recipe_id']);

    // Fetch recipe details
    $sql = "SELECT r.title, r.description, r.prep_time, r.cook_time, r.servings, 
                   r.spicy, r.image_path, r.created_at, r.updated_at,
                   c.cuisine_name, cat.category_name, u.username
            FROM Recipe r
            LEFT JOIN Cuisine c ON r.cuisine_id = c.cuisine_id
            LEFT JOIN Category cat ON r.category_id = cat.category_id
            LEFT JOIN User u ON r.user_id = u.user_id
            WHERE r.recipe_id = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($recipe = $result->fetch_assoc()) {
        echo "<div class='container p-4 border rounded shadow'>";

        // Modal Header
        echo "<div class='modal-header bg-primary text-white'>";
        echo "<h5 class='modal-title text-center flex-grow-1 m-0'>" . htmlspecialchars($recipe['title']) . "</h5>";
        echo "</div>";

        echo "<div class='modal-body'>";

        // Recipe Creator
        echo "<p class='text-muted text-center'><strong>Created By:</strong> " . htmlspecialchars($recipe['username']) . "</p>";

        // Display Uploaded Image
        if (!empty($recipe['image_path']) && file_exists($recipe['image_path'])) {
            echo "<div class='text-center mb-3'>";
            echo "<img src='" . htmlspecialchars($recipe['image_path']) . "' class='img-fluid rounded shadow' style='max-width: 300px; height: auto;'>";
            echo "</div>";
        } else {
            echo "<p class='text-center text-muted'>No image available for this recipe.</p>";
        }

        // Convert time format (hh:ii:ss → X hours Y minutes)
        function format_time($time)
        {
            sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
            return $hours > 0 ? "$hours hrs $minutes mins" : "$minutes mins";
        }

        // Determine spicy icon
        $spicy_icon = ($recipe['spicy']) ? "🌶️" : "❌";

        // Recipe Description
        echo "<h6 class='fw-bold'>Description</h6>";
        echo "<p class='small'>" . nl2br(htmlspecialchars($recipe['description'])) . "</p>";

        // Recipe Details Table
        echo "<table class='table table-sm table-bordered'>";
        echo "<tr><th>Category</th><td>" . htmlspecialchars($recipe['category_name']) . "</td></tr>";
        echo "<tr><th>Cuisine</th><td>" . htmlspecialchars($recipe['cuisine_name']) . "</td></tr>";
        echo "<tr><th>Prep Time</th><td>" . format_time($recipe['prep_time']) . "</td></tr>";
        echo "<tr><th>Cook Time</th><td>" . format_time($recipe['cook_time']) . "</td></tr>";
        echo "<tr><th>Servings</th><td>" . htmlspecialchars($recipe['servings']) . "</td></tr>";
        echo "<tr><th>Spicy</th><td>" . $spicy_icon . "</td></tr>";
        echo "<tr><th>Created At</th><td>" . htmlspecialchars($recipe['created_at']) . "</td></tr>";
        echo "<tr><th>Updated At</th><td>" . htmlspecialchars($recipe['updated_at']) . "</td></tr>";
        echo "</table>";

        // Fetch Ingredients
        echo "<h6 class='fw-bold'>Ingredients</h6><ul>";
        $sql_ingredients = "SELECT i.ingredient_name, ri.quantity, ri.units 
                            FROM Recipe_Ingredient ri
                            JOIN Ingredient i ON ri.ingredient_id = i.ingredient_id
                            WHERE ri.recipe_id = ?";
        $stmt_ingredients = $con->prepare($sql_ingredients);
        $stmt_ingredients->bind_param("i", $recipe_id);
        $stmt_ingredients->execute();
        $result_ingredients = $stmt_ingredients->get_result();
        while ($ingredient = $result_ingredients->fetch_assoc()) {
            echo "<li class='small'>" . htmlspecialchars($ingredient['ingredient_name']) . ": " .
                htmlspecialchars($ingredient['quantity']) . " " .
                htmlspecialchars($ingredient['units']) . "</li>";
        }
        echo "</ul>";

        // Fetch Cooking Steps
        echo "<h6 class='fw-bold'>Steps</h6><ol class='list-group list-group-numbered small'>";
        $sql_steps = "SELECT step_num, instruction FROM Step WHERE recipe_id = ? ORDER BY step_num ASC";
        $stmt_steps = $con->prepare($sql_steps);
        $stmt_steps->bind_param("i", $recipe_id);
        $stmt_steps->execute();
        $result_steps = $stmt_steps->get_result();
        while ($step = $result_steps->fetch_assoc()) {
            echo "<li class='list-group-item'>" . htmlspecialchars($step['instruction']) . "</li>";
        }
        echo "</ol>";

        echo "</div>"; // Close modal-body
        echo "</div>"; // Close container
    } else {
        echo "Recipe not found.";
    }

    $stmt->close();
}
$con->close();
?>