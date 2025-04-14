<?php
session_start();
require '../user_module/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("You must be logged in to update a recipe.");
        }

        // Validate recipe ID
        if (!isset($_GET['recipe_id']) || !is_numeric($_GET['recipe_id'])) {
            throw new Exception("Invalid recipe ID.");
        }

        $recipe_id = intval($_GET['recipe_id']);
        $user_id = $_SESSION['user_id'];
        $isAdmin = $_SESSION['isAdmin'];

        // Fetch the recipe to verify ownership or admin privileges
        $sql = "SELECT user_id FROM Recipe WHERE recipe_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $stmt->bind_result($recipe_owner_id);
        $stmt->fetch();
        $stmt->close();

        if (!$recipe_owner_id) {
            throw new Exception("Recipe not found.");
        }

        if ($recipe_owner_id != $user_id && $isAdmin != 1) {
            throw new Exception("You do not have permission to update this recipe.");
        }

        // Retrieve form data
        $title = trim($_POST['title']);
        $cuisine_id = !empty($_POST['cuisine_id']) ? intval($_POST['cuisine_id']) : NULL;
        $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : NULL;
        $description = trim($_POST['description']);
        $prep_time_minutes = intval($_POST['prep_time']);
        $cook_time_minutes = intval($_POST['cook_time']);
        $servings = intval($_POST['servings']);
        $spicy = isset($_POST['spicy']) ? 1 : 0;

        // Validate required fields
        if (empty($title) || empty($description) || empty($prep_time_minutes) || empty($cook_time_minutes) || empty($servings) || empty($cuisine_id) || empty($category_id)) {
            throw new Exception("All fields are required!");
        }

        // Handle file upload
        $uploadDir = "../recipe_management_module/recipe_img/";
        $imagePath = NULL;

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image_file']['tmp_name'];
            $fileName = $_FILES['image_file']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Allowed file extensions
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
            }

            $destPath = $uploadDir . $fileName;
            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                throw new Exception("There was an error moving the uploaded file.");
            }

            $imagePath = $destPath;
        }

        // Convert minutes to HH:MM:SS format
        $prep_time = sprintf('%02d:%02d:00', floor($prep_time_minutes / 60), $prep_time_minutes % 60);
        $cook_time = sprintf('%02d:%02d:00', floor($cook_time_minutes / 60), $cook_time_minutes % 60);

        // Update recipe details
        $sql = "UPDATE Recipe 
                SET cuisine_id = ?, category_id = ?, title = ?, description = ?, prep_time = ?, cook_time = ?, servings = ?, spicy = ?, image_path = IFNULL(?, image_path)
                WHERE recipe_id = ?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $con->error);
        }

        $stmt->bind_param("iissssissi", $cuisine_id, $category_id, $title, $description, $prep_time, $cook_time, $servings, $spicy, $imagePath, $recipe_id);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update recipe. Error: " . $stmt->error);
        }

        // Delete existing ingredients and steps
        $con->query("DELETE FROM Recipe_Ingredient WHERE recipe_id = $recipe_id");
        $con->query("DELETE FROM Step WHERE recipe_id = $recipe_id");

        // Validate and insert ingredients
        if (!empty($_POST['ingredient_names']) && !empty($_POST['quantities']) && !empty($_POST['units'])) {
            foreach ($_POST['ingredient_names'] as $index => $ingredient_name) {
                $ingredient_name = trim($ingredient_name);
                $quantity = floatval($_POST['quantities'][$index]);
                $unit = trim($_POST['units'][$index]);

                if (empty($ingredient_name) || $quantity <= 0 || empty($unit)) {
                    throw new Exception("All ingredient fields are required!");
                }

                // Check if the ingredient already exists
                $sql = "SELECT ingredient_id FROM Ingredient WHERE LOWER(ingredient_name) = LOWER(?)";
                $stmt_check = $con->prepare($sql);
                if (!$stmt_check) {
                    throw new Exception("Failed to prepare statement: " . $con->error);
                }

                $stmt_check->bind_param("s", $ingredient_name);
                $stmt_check->execute();
                $stmt_check->bind_result($ingredient_id);
                $stmt_check->fetch();
                $stmt_check->close();

                // Insert new ingredient if it doesn't exist
                if (empty($ingredient_id)) {
                    $sql = "INSERT INTO Ingredient (ingredient_name) VALUES (?)";
                    $stmt_insert = $con->prepare($sql);
                    if (!$stmt_insert) {
                        throw new Exception("Failed to prepare statement: " . $con->error);
                    }

                    $stmt_insert->bind_param("s", $ingredient_name);
                    $stmt_insert->execute();
                    $ingredient_id = $stmt_insert->insert_id;
                    $stmt_insert->close();
                }

                // Insert into Recipe_Ingredient table
                $sql = "INSERT INTO Recipe_Ingredient (recipe_id, ingredient_id, quantity, units) VALUES (?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $con->error);
                }

                $stmt->bind_param("iids", $recipe_id, $ingredient_id, $quantity, $unit);
                $stmt->execute();
            }
        } else {
            throw new Exception("At least one ingredient is required!");
        }

        // Validate and insert steps
        if (!empty($_POST['steps'])) {
            $stmt = $con->prepare("INSERT INTO Step (recipe_id, step_num, instruction) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $con->error);
            }

            foreach ($_POST['steps'] as $index => $instruction) {
                $step_number = $index + 1;
                $instruction = trim($instruction);

                if (empty($instruction)) {
                    throw new Exception("All steps must have a description!");
                }

                $stmt->bind_param("iis", $recipe_id, $step_number, $instruction);
                $stmt->execute();
            }
        } else {
            throw new Exception("At least one step is required!");
        }

        $_SESSION['success'] = "Recipe updated successfully!";
        
        header("Location: recipe.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: edit_recipe.php?recipe_id=" . $recipe_id);
        exit();
    } finally {
        $con->close();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: recipe.php");
    exit();
}
