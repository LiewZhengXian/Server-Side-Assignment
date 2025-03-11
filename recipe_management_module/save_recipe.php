<?php
session_start();
require '../user_module/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_id = $_SESSION['user_id']; // Ensure the user is logged in and user_id is set
    $title = trim($_POST['title']);
    $cuisine_id = !empty($_POST['cuisine_id']) ? intval($_POST['cuisine_id']) : NULL;
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : NULL;
    $image_url = trim($_POST['image_url']);
    $description = trim($_POST['description']);
    $prep_time_minutes = intval($_POST['prep_time']); // Input is in minutes
    $cook_time_minutes = intval($_POST['cook_time']); // Input is in minutes
    $servings = intval($_POST['servings']);
    $spicy = isset($_POST['spicy']) ? 1 : 0;

    // Validate required fields
    if (empty($title) || empty($description) || empty($prep_time_minutes) || empty($cook_time_minutes) || empty($servings)) {
        $_SESSION['error'] = "All fields except Image URL, Cuisine, and Category are required!";
        header("Location: add_recipe.php");
        exit();
    }

    // Convert minutes to HH:MM:SS format
    $prep_hours = floor($prep_time_minutes / 60);
    $prep_minutes = $prep_time_minutes % 60;
    $prep_time = sprintf('%02d:%02d:00', $prep_hours, $prep_minutes);

    $cook_hours = floor($cook_time_minutes / 60);
    $cook_minutes = $cook_time_minutes % 60;
    $cook_time = sprintf('%02d:%02d:00', $cook_hours, $cook_minutes);

    // Insert recipe into the database
    $stmt = $con->prepare("INSERT INTO Recipe (user_id, cuisine_id, category_id, title, description, prep_time, cook_time, servings, spicy, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssssiss", $user_id, $cuisine_id, $category_id, $title, $description, $prep_time, $cook_time, $servings, $spicy, $image_url);

    if ($stmt->execute()) {
        $recipe_id = $stmt->insert_id; // Get the last inserted recipe ID

        // Insert ingredients
        if (!empty($_POST['ingredient_names']) && !empty($_POST['quantities']) && !empty($_POST['units'])) {
            foreach ($_POST['ingredient_names'] as $index => $ingredient_name) {
                $ingredient_name = trim($ingredient_name);
                $quantity = floatval($_POST['quantities'][$index]);
                $unit = trim($_POST['units'][$index]);

                // Check if the ingredient already exists (case-insensitive)
                $sql = "SELECT ingredient_id FROM Ingredient WHERE LOWER(ingredient_name) = LOWER(?)";
                $stmt_check = $con->prepare($sql);
                $stmt_check->bind_param("s", $ingredient_name);
                $stmt_check->execute();
                $stmt_check->bind_result($ingredient_id);
                $stmt_check->fetch();
                $stmt_check->close();

                // If the ingredient does not exist, insert it
                if (empty($ingredient_id)) {
                    $sql = "INSERT INTO Ingredient (ingredient_name) VALUES (?)";
                    $stmt_insert = $con->prepare($sql);
                    $stmt_insert->bind_param("s", $ingredient_name);
                    $stmt_insert->execute();
                    $ingredient_id = $stmt_insert->insert_id;
                    $stmt_insert->close();
                }

                // Insert into Recipe_Ingredient table
                if ($ingredient_id > 0 && $quantity > 0 && !empty($unit)) {
                    $sql = "INSERT INTO Recipe_Ingredient (recipe_id, ingredient_id, quantity, units) VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), units = VALUES(units)";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("iids", $recipe_id, $ingredient_id, $quantity, $unit);
                    $stmt->execute();
                }
            }
        }

        // Insert steps
        if (!empty($_POST['steps'])) {
            $stmt = $con->prepare("INSERT INTO Step (recipe_id, step_num, instruction) VALUES (?, ?, ?)");
            foreach ($_POST['steps'] as $index => $instruction) {
                $step_number = $index + 1;
                $instruction = trim($instruction);

                if (!empty($instruction)) {
                    $stmt->bind_param("iis", $recipe_id, $step_number, $instruction);
                    $stmt->execute();
                }
            }
        }

        $_SESSION['success'] = "Recipe added successfully!";
        header("Location: recipe.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add recipe. Error: " . $stmt->error;
        header("Location: add_recipe.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: add_recipe.php");
    exit();
}

$con->close();
?>