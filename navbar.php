<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        html {
            overflow-y: scroll;
        }

        .navbar {
            background: linear-gradient(90deg, #343a40, #212529);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            padding: 1rem 1.5rem;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
            color: #f8f9fa;
        }

        .nav-link {
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease, background-color 0.3s ease, transform 0.2s ease;
        }

        .nav-link:hover {
            color: #ffc107;
            transform: scale(1.05);
        }

        .nav-link.active {
            font-weight: bold;
            color: #ffc107 !important;
        }

        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            background-color: #343a40;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            font-size: 1rem;
            color: #f8f9fa;
            padding: 0.5rem 1rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #495057;
            color: #ffc107;
        }

        .navbar-toggler {
            border: none;
        }

        .navbar-toggler-icon {
            background-color: #fff;
            border-radius: 0.25rem;
        }

        .user-dropdown {
            background-color: #007BFF;
            color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 0.75rem 1.25rem;
            /* Increase padding for a larger dropdown */
        }

        .user-dropdown:hover {
            background-color: #343a40;
        }

        .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="/Server-Side-Assignment/index.php">Recipe Hub</a>

            <!-- Toggler for Mobile View -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Home -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="/Server-Side-Assignment/index.php">Home</a>
                    </li>

                    <!-- Recipes Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['recipe.php', 'recipe_user.php', 'recipe_admin.php', 'add_recipe.php', 'edit_recipe.php']) ? 'active' : ''; ?>" href="#" id="recipesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Recipes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'recipe.php' || (basename($_SERVER['PHP_SELF']) == 'recipe_admin.php') || (basename($_SERVER['PHP_SELF']) == 'recipe_user.php')) ? 'active' : ''; ?>" href="/Server-Side-Assignment/recipe_management_module/recipe.php">View Recipes</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'add_recipe.php' ? 'active' : ''; ?>" href="/Server-Side-Assignment/recipe_management_module/add_recipe.php">Add Recipe</a></li>
                        </ul>
                    </li>

                    <!-- Meal Planning Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['meal_plan_add.php', 'meal_plan_list.php', 'meal_plan_display.php', 'list_templates.php', 'view_template.php', 'edit_template.php', 'meal_plan_edit.php']) ? 'active' : ''; ?>" href="#" id="mealPlanningDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Meal Planning
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'meal_plan_add.php' ? 'active' : ''; ?>" href="/Server-Side-Assignment/meal_planning_module/meal_plan_add.php">Plan a Meal</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'meal_plan_list.php' ? 'active' : ''; ?>" href="/Server-Side-Assignment/meal_planning_module/meal_plan_list.php">View Schedule</a></li>
                            <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'list_templates.php' || (basename($_SERVER['PHP_SELF']) == 'view_template.php') || (basename($_SERVER['PHP_SELF']) == 'edit_template.php')) ? 'active' : ''; ?>" href="/Server-Side-Assignment/meal_template_module/list_templates.php">Manage Templates</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'meal_plan_display.php' ? 'active' : ''; ?>" href="/Server-Side-Assignment/meal_planning_module/meal_plan_display.php">Display Schedule table</a></li>
                        </ul>
                    </li>

                    <!-- Community Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['Community.php', 'Add_post.php']) ? 'active' : ''; ?>" href="#" id="communityDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Community
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'Community.php' ? 'active' : ''; ?>" href="/Server-Side-Assignment/community_module/Community.php">View Community</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'Add_post.php' ? 'active' : ''; ?>" href="/Server-Side-Assignment/community_module/Add_post.php">Share Recipe</a></li>
                        </ul>
                    </li>

                    <!-- Competitions -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'competition_main.php' ? 'active' : ''; ?>" href="/Server-Side-Assignment/cooking_competition_module/competition_main.php">Competitions</a>
                    </li>
                </ul>

                <!-- User Dropdown -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center user-dropdown" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/Server-Side-Assignment/user_module/change_password.php">Change Password</a></li>
                            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                                <li><a class="dropdown-item" href="/Server-Side-Assignment/user_module/add_admin.php">Add Admin</a></li>
                            <?php endif; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/Server-Side-Assignment/user_module/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>

</html>