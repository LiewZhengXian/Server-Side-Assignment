<?php
session_start();
require '../user_module/database.php';

// Check if the user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : '';


// Fetch all cuisines and categories for filters
$cuisines = $con->query("SELECT * FROM Cuisine");
$categories = $con->query("SELECT * FROM Category");

// Fetch all recipes initially
$sql = "SELECT r.recipe_id, r.title, r.description, c.cuisine_name, cat.category_name, r.user_id, r.image_path
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .filter-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .recipe-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .recipe-card:hover {
            transform: scale(1.02);
        }

        .recipe-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .recipe-card-body {
            padding: 15px;
        }

        .recipe-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .recipe-description {
            font-size: 0.9rem;
            color: #555;
        }

        .recipe-actions {
            margin-top: 10px;
        }

        .add-recipe-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            color: #fff;
            background-color: #007bff;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }

        .add-recipe-btn:hover {
            background-color: #0056b3;
            text-decoration: none;
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
        <h2 class="mb-4 text-center">All Recipe</h2>

        <!-- Filter Box -->
        <div class="filter-box">
            <form id="searchForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchTitle" placeholder="Search by recipe name">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filterCuisine">
                            <option value="">All Cuisines</option>
                            <?php
                            $selectedCuisine = isset($_GET['cuisine']) ? $_GET['cuisine'] : '';
                            while ($row = $cuisines->fetch_assoc()) {
                                $isSelected = ($row['cuisine_name'] === $selectedCuisine) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($row['cuisine_name']) . "' $isSelected>" . htmlspecialchars($row['cuisine_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select class="form-control" id="filterCategory">
                            <option value="">All Categories</option>
                            <?php
                            $selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
                            while ($row = $categories->fetch_assoc()) {
                                $isSelected = ($row['category_name'] === $selectedCategory) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($row['category_name']) . "' $isSelected>" . htmlspecialchars($row['category_name']) . "</option>";
                            }
                            ?>
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

        <!-- Recipe Cards -->
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <div class="recipe-card">
                        <img src="<?php echo file_exists($row['image_path']) && !empty($row['image_path']) ? htmlspecialchars($row['image_path']) : 'default-image.jpg'; ?>"
                            alt="Recipe Image"
                            class="img-fluid rounded shadow"
                            style="width: 100%; height: 200px; object-fit: cover;">
                        <div class="recipe-card-body">
                            <div class="recipe-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="recipe-description" title="<?php echo htmlspecialchars($row['description']); ?>">
                                <?php
                                echo htmlspecialchars(mb_strimwidth($row['description'], 0, 50, '...'));
                                ?>
                            </div>
                            <div class="recipe-meta mt-2">
                                <span class="badge bg-info"><?php echo htmlspecialchars($row['cuisine_name']); ?></span>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($row['category_name']); ?></span>
                            </div>
                            <div class="recipe-actions">
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal"
                                    onclick="loadRecipeDetails(<?php echo $row['recipe_id']; ?>)">View</button>
                                <?php if ($row['user_id'] == $user_id || $isAdmin == 1) { ?>
                                    <a href="edit_recipe.php?recipe_id=<?php echo $row['recipe_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['recipe_id']; ?>)">Delete</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <br>

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

        function searchRecipes() {
            const title = document.getElementById('searchTitle').value;
            const cuisine = document.getElementById('filterCuisine').value;
            const category = document.getElementById('filterCategory').value;
            const ownership = document.getElementById('filterOwnership').value;

            // Update the URL with query parameters
            const params = new URLSearchParams();
            if (title) params.set('title', title);
            if (cuisine) params.set('cuisine', cuisine);
            if (category) params.set('category', category);
            if (ownership) params.set('ownership', ownership);
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);

            // Filter the recipe cards
            const rows = document.querySelectorAll('.recipe-card');
            rows.forEach(row => {
                const rowTitle = row.querySelector('.recipe-title').textContent.toLowerCase();
                const rowCuisine = row.querySelector('.badge.bg-info').textContent.toLowerCase();
                const rowCategory = row.querySelector('.badge.bg-secondary').textContent.toLowerCase();
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

        function populateFiltersFromURL() {
            const params = new URLSearchParams(window.location.search);

            // Populate the search and filter inputs
            if (params.has('title')) {
                document.getElementById('searchTitle').value = params.get('title');
            }
            if (params.has('cuisine')) {
                document.getElementById('filterCuisine').value = params.get('cuisine');
            }
            if (params.has('category')) {
                document.getElementById('filterCategory').value = params.get('category');
            }
            if (params.has('ownership')) {
                document.getElementById('filterOwnership').value = params.get('ownership');
            }

            // Trigger the search function to apply the filters
            searchRecipes();
        }

        document.addEventListener('DOMContentLoaded', populateFiltersFromURL);
    </script>

    <!-- Floating Add Recipe Button -->
    <a href="add_recipe.php" class="btn btn-primary rounded-circle add-recipe-btn">
        <i class="fas fa-plus fa-lg"></i>
    </a>
    <?php include '../footer.php'; ?>
</body>

</html>

<?php $con->close(); ?>