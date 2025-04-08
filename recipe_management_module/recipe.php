<?php
session_start();
require '../user_module/database.php';

$user_id = $_SESSION['user_id'];

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
            <a href="add_recipe.php" class="btn btn-primary">Add Recipe</a>
        </div>

        <!-- Search and Filter Form -->
        <form id="searchForm" class="mb-3">
            <div class="row">
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
                    <button type="button" class="btn btn-primary" onclick="searchRecipes()">Search</button>
                </div>
            </div>
        </form>

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
                        <tr data-user-id="<?php echo $row['user_id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-cuisine="<?php echo htmlspecialchars($row['cuisine_name']); ?>" data-category="<?php echo htmlspecialchars($row['category_name']); ?>">
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td align=left><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['cuisine_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal"
                                    onclick="loadRecipeDetails(<?php echo $row['recipe_id']; ?>)">View</button>
                                <?php if ($row['user_id'] == $user_id) { ?>
                                    <a href="edit_recipe.php?id=<?php echo $row['recipe_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['recipe_id']; ?>)">Delete</button>
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

        function confirmDelete(recipeId) {
            if (confirm("Are you sure you want to delete this recipe?")) {
                window.location.href = "delete_recipe.php?id=" + recipeId;
            }
        }

        function searchRecipes() {
            const title = document.getElementById('searchTitle').value.toLowerCase();
            const cuisine = document.getElementById('filterCuisine').value.toLowerCase();
            const category = document.getElementById('filterCategory').value.toLowerCase();
            const ownership = document.getElementById('filterOwnership').value;

            const rows = document.querySelectorAll('#recipeTable tbody tr');
            rows.forEach(row => {
                const rowTitle = row.dataset.title.toLowerCase();
                const rowCuisine = row.dataset.cuisine.toLowerCase();
                const rowCategory = row.dataset.category.toLowerCase();
                const rowUserId = row.dataset.userId;

                const matchesTitle = rowTitle.includes(title);
                const matchesCuisine = !cuisine || rowCuisine === cuisine;
                const matchesCategory = !category || rowCategory === category;
                const matchesOwnership = ownership === 'all' || (ownership === 'my' && rowUserId === '<?php echo $user_id; ?>');

                if (matchesTitle && matchesCuisine && matchesCategory && matchesOwnership) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>

</body>

</html>

<?php $con->close(); ?>