<?php
include '../user_module/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_module/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all meals for the next 7 days
// Fetch all meals for the next 7 days, considering duration
$sql = "SELECT * FROM meal_plans 
        WHERE user_id = ? 
        AND (
            (meal_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 DAY))
            OR 
            (DATE_ADD(meal_date, INTERVAL duration-1 DAY) >= CURDATE() 
             AND meal_date <= DATE_ADD(CURDATE(), INTERVAL 6 DAY))
        )";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$meals = [];
while ($row = $result->fetch_assoc()) {
    // Calculate end date based on duration
    $start_date = new DateTime($row['meal_date']);
    $end_date = clone $start_date;
    $end_date->modify('+' . ($row['duration'] - 1) . ' days');
    
    // Add the meal to all applicable dates
    $current_date = clone $start_date;
    while ($current_date <= $end_date) {
        $date_str = $current_date->format('Y-m-d');
        
        // Only include if it's within our weekly view
        $seven_days_later = new DateTime();
        $seven_days_later->modify('+6 days');
        
        if ($current_date >= new DateTime('today') && $current_date <= $seven_days_later) {
            $meals[$date_str][$row['meal_time']] = $row;
        }
        
        $current_date->modify('+1 day');
    }
}

// Define days and meal times
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
$meal_times = ["Breakfast", "Lunch", "Dinner"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Planning - Recipe Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar (Same as meal_plan_list.php) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Recipe Hub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Recipes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="mealPlanningDropdown" role="button" data-bs-toggle="dropdown">
                            Meal Planning
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="meal_plan_add.php">Plan a Meal</a></li>
                            <li><a class="dropdown-item" href="meal_plan_list.php">View Schedule</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../community_module/Community.php">Community</a>
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

    <div class="container mt-4">
        <h1 class="text-center">Weekly Meal Plan</h1>

        <!-- Weekly Meal Plan Table -->
        <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th></th>
                <?php 
                $monday = date('Y-m-d', strtotime('Monday this week'));
                foreach ($days as $index => $day): 
                    $date = date('Y-m-d', strtotime($monday . ' +' . $index . ' days'));
                ?>
                    <th>
                        <?= $day ?><br>
                        <small><?= date('M d', strtotime($date)) ?></small>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
            <tbody>
                <?php foreach ($meal_times as $meal_time): ?>
                    <tr>
                        <td class="table-dark"><?= $meal_time ?></td>
                        <?php 
                            // Get the current week's Monday date
                            $monday = date('Y-m-d', strtotime('Monday this week'));
                            
                            // Loop through days
                            foreach ($days as $index => $day): 
                                // Calculate the exact date for this day by adding offset to Monday
                                $date = date('Y-m-d', strtotime($monday . ' +' . $index . ' days'));
                                $meal = $meals[$date][$meal_time] ?? null;
                        ?>
                            <td>
                                <?php if ($meal): ?>
                                    <?= $meal['meal_name'] ?>
                                    <?php if ($meal['duration'] > 1): ?>
                                        (<?= $meal['duration'] ?> days)
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr>

        <!-- Existing Meal Plans List -->
        <h2>Scheduled Meals</h2>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="meal_plan_add.php" class="btn btn-success">+ Plan a Meal</a>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
            <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Meal Name</th>
                <th>Recipe</th>
                <th>Duration</th>
                <th>Actions</th>
                <th>Last Updated</th>  
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $con->prepare("SELECT mp.*, r.title AS recipe_title FROM meal_plans mp LEFT JOIN recipe r ON mp.recipe_id = r.recipe_id WHERE mp.user_id = ? ORDER BY mp.meal_date ASC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['meal_date'] ?></td>
                <td><?= $row['meal_time'] ?></td>
                <td><?= $row['meal_name'] ?></td>
                <td>
                    <?php if($row['meal_type'] == 'recipe'): ?>
                        <?= $row['recipe_title'] ?>
                    <?php else: ?>
                        Custom Meal
                    <?php endif; ?>
                </td>
                <td><?= $row['duration'] ? $row['duration'] . ' days' : '1 day' ?></td>
                <td>
                    <a href="meal_plan_edit.php?meal_id=<?= $row['meal_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="meal_plan_delete.php?meal_id=<?= $row['meal_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this meal?');">Delete</a>
                </td>
                <td><?= $row['updated_at'] ? date('Y-m-d H:i', strtotime($row['updated_at'])) : 'N/A' ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
