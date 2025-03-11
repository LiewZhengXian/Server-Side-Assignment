<?php
session_start();
require '../user_module/database.php';

$user_id = $_SESSION['user_id'];

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
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Recipe Hub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'recipe.php' ? 'active' : ''; ?>" href="recipe.php">Recipes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="mealPlanningDropdown" role="button" data-bs-toggle="dropdown">
                            Meal Planning
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Plan a Meal</a></li>
                            <li><a class="dropdown-item" href="#">View Schedule</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./Community.php">Community</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Competitions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user_module/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Recipe Management Controls -->
    <div class="container mt-4">
        <h2 class="mb-3">Recipe Management</h2>
        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-secondary" id="toggleRecipes" onclick="toggleRecipes()">My Recipes</button>
            <a href="add_recipe.php" class="btn btn-primary">Add Recipe</a>
        </div>

        <!-- Recipe Table -->
        <div class="table-responsive">
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
                        <tr data-user-id="<?php echo $row['user_id']; ?>">
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td align=left><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['cuisine_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal"
                                    onclick="loadRecipeDetails(<?php echo $row['recipe_id']; ?>)">View</button>
                                <?php if ($row['user_id'] == $user_id) { ?>
                                    <button class="btn btn-warning btn-sm">Edit</button>
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                <?php } ?>
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
                <div class="modal-header border-0">
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
        let showMyRecipes = false;

        function toggleRecipes() {
            let rows = document.querySelectorAll('#recipeTable tbody tr');
            let button = document.getElementById('toggleRecipes');
            showMyRecipes = !showMyRecipes;
            rows.forEach(row => {
                if (showMyRecipes) {
                    if (row.dataset.userId !== '<?php echo $user_id; ?>') {
                        row.style.display = 'none';
                    }
                } else {
                    row.style.display = '';
                }
            });
            button.textContent = showMyRecipes ? 'All Recipes' : 'My Recipes';
        }

        function loadRecipeDetails(recipeId) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "view_recipe.php?id=" + recipeId, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById("recipeDetails").innerHTML = this.responseText;
                }
            };
            xhr.send();
        }
    </script>

</body>

</html>

<?php $con->close(); ?>