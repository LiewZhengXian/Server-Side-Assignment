<?php
session_start();
require '../user_module/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {

        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("You must be logged in to add a recipe.");
        }

        // Retrieve form data
        $user_id = $_SESSION['user_id'];
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

        // Validate prep time, cook time, and servings
        if ($prep_time_minutes < 0.01) {
            throw new Exception("Preparation time must be greater than or equal to 0.01 minutes!");
        }

        if ($cook_time_minutes < 0.01) {
            throw new Exception("Cooking time must be greater than or equal to 0.01 minutes!");
        }

        if ($servings < 1) {
            throw new Exception("Servings must be at least 1!");
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
        } else {
            throw new Exception("Image is required!");
        }

        // Convert minutes to HH:MM:SS format
        $prep_time = sprintf('%02d:%02d:00', floor($prep_time_minutes / 60), $prep_time_minutes % 60);
        $cook_time = sprintf('%02d:%02d:00', floor($cook_time_minutes / 60), $cook_time_minutes % 60);

        // Insert recipe into the database
        $stmt = $con->prepare("INSERT INTO Recipe (user_id, cuisine_id, category_id, title, description, prep_time, cook_time, servings, spicy, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $con->error);
        }

        $stmt->bind_param("iisssssiss", $user_id, $cuisine_id, $category_id, $title, $description, $prep_time, $cook_time, $servings, $spicy, $imagePath);
        if (!$stmt->execute()) {
            throw new Exception("Failed to add recipe. Error: " . $stmt->error);
        }

        $recipe_id = $stmt->insert_id;

        // Validate and insert ingredients
        if (!empty($_POST['ingredient_names']) && !empty($_POST['quantities']) && !empty($_POST['units'])) {
            foreach ($_POST['ingredient_names'] as $index => $ingredient_name) {
                $ingredient_name = trim($ingredient_name);
                $quantity = floatval($_POST['quantities'][$index]);
                $unit = trim($_POST['units'][$index]);

                // Reset $ingredient_id for each iteration
                $ingredient_id = null;

                if (empty($ingredient_name) || empty($quantity) || empty($unit)) {
                    throw new Exception("All ingredient fields are required!");
                }

                if ($quantity < 0.01) {
                    throw new Exception("Ingredient quantity must be greater than or equal to 0.01!");
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

                // Check if the combination of recipe_id and ingredient_id already exists
                $sql = "SELECT 1 FROM Recipe_Ingredient WHERE recipe_id = ? AND ingredient_id = ?";
                $stmt_check_composite = $con->prepare($sql);
                if (!$stmt_check_composite) {
                    throw new Exception("Failed to prepare statement: " . $con->error);
                }

                $stmt_check_composite->bind_param("ii", $recipe_id, $ingredient_id);
                $stmt_check_composite->execute();
                $stmt_check_composite->store_result();

                if ($stmt_check_composite->num_rows == 0) {
                    // Insert into Recipe_Ingredient table if the combination does not exist
                    $sql = "INSERT INTO Recipe_Ingredient (recipe_id, ingredient_id, quantity, units) VALUES (?, ?, ?, ?)";
                    $stmt = $con->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Failed to prepare statement: " . $con->error);
                    }

                    $stmt->bind_param("iids", $recipe_id, $ingredient_id, $quantity, $unit);
                    $stmt->execute();
                    $stmt->close();
                }

                $stmt_check_composite->close();
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

        $_SESSION['success'] = "Recipe added successfully!";
        header("Location: recipe.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: add_recipe.php");
        exit();
    } finally {
        $con->close();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: add_recipe.php");
    exit();
}
