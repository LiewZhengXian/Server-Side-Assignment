<?php
session_start();
require '../user_module/database.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    // Redirect non-admin users to the user recipe page or login page
    header("Location: recipe_user.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch all cuisines and categories for filters
$cuisines = $con->query("SELECT * FROM Cuisine");
$categories = $con->query("SELECT * FROM Category");

// Fetch all recipes initially
$sql = "SELECT r.recipe_id, r.title, r.description, c.cuisine_name, cat.category_name, r.user_id 
        FROM Recipe r 
        LEFT JOIN Cuisine c ON r.cuisine_id = c.cuisine_id
        LEFT JOIN Category cat ON r.category_id = cat.category_id";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-sm {
            border-radius: 20px;
        }

        .actions-column {
            width: 15%;
        }

        .title-column {
            width: 20%;
        }

        .description-column {
            width: 40%;
        }

        .cuisine-column {
            width: 10%;
        }

        .category-column {
            width: 15%;
        }

        .search-bar {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background-color: #343a40;
            color: #ffffff;
        }

        .modal-title {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['success'])): ?>
        <script>
            alert("<?php echo $_SESSION['success']; ?>");
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <!-- Navbar -->
    <?php include("../navbar.php"); ?>

    <!-- Recipe Management Controls -->
    <div class="container mt-4">
        <h2 class="mb-4 text-center">Recipe Management</h2>

        <!-- Add Recipe Button -->
        <div class="d-flex justify-content-end mb-3">
            <a href="add_recipe.php" class="btn btn-success btn-lg">+ Add Recipe</a>
        </div>

        <!-- Search and Filter Form -->
        <div class="search-bar mb-4">
            <form id="searchForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchTitle" placeholder="Search by recipe name">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filterCuisine">
                            <option value="">All Cuisines</option>
                            <?php while ($row = $cuisines->fetch_assoc()) { ?>
                                <option value="<?php echo $row['cuisine_name']; ?>"><?php echo $row['cuisine_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filterCategory">
                            <option value="">All Categories</option>
                            <?php while ($row = $categories->fetch_assoc()) { ?>
                                <option value="<?php echo $row['category_name']; ?>"><?php echo $row['category_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="filterOwnership">
                            <option value="all">All Recipes</option>
                            <option value="my">My Recipes</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-primary w-100" onclick="searchRecipes()">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Recipe Table -->
        <div class="table-container">
            <table class="table table-striped table-hover table-bordered text-center" id="recipeTable">
                <thead class="table-dark">
                    <tr>
                        <th class="title-column">Title</th>
                        <th class="description-column">Description</th>
                        <th class="cuisine-column">Cuisine</th>
                        <th class="category-column">Category</th>
                        <th class="actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr data-user-id="<?php echo $row['user_id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-cuisine="<?php echo htmlspecialchars($row['cuisine_name']); ?>" data-category="<?php echo htmlspecialchars($row['category_name']); ?>">
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td align="left"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['cuisine_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal"
                                    onclick="loadRecipeDetails(<?php echo $row['recipe_id']; ?>)">View</button>
                                <a href="edit_recipe.php?recipe_id=<?php echo $row['recipe_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['recipe_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Recipe Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recipe Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="recipeDetails">
                    <!-- Recipe details will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadRecipeDetails(recipeId) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "view_recipe.php?recipe_id=" + recipeId, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById("recipeDetails").innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        function confirmDelete(recipeId) {
            if (confirm("Are you sure you want to delete this recipe?")) {
                window.location.href = "delete_recipe.php?recipe_id=" + recipeId;
            }
        }
    </script>

    <?php include '../footer.php'; ?>
</body>

</html>

<?php $con->close(); ?>