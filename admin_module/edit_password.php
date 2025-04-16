<?php
session_start();
require '../user_module/database.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    header("Location: ../index.php"); // Redirect non-admin users to the homepage
    exit();
}

// Check if user_id is provided
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: manage_user.php"); // Redirect to manage user page if no user_id is provided
    exit();
}

$user_id = intval($_GET['user_id']);
$errorMessage = "";
$successMessage = "";

// Fetch the current username of the user
$sql = "SELECT username FROM User WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: manage_user.php"); // Redirect if the user does not exist
    exit();
}

$currentUsername = $user['username'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate passwords
    if (empty($newPassword) || empty($confirmPassword)) {
        $errorMessage = "Both password fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "Passwords do not match. Please try again.";
    } else {
        // Hash the new password using md5
        $hashedPassword = md5($newPassword);

        // Update the password in the database
        $sql = "UPDATE User SET password = ? WHERE user_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $user_id);

        if ($stmt->execute()) {
            $successMessage = "Password updated successfully!";
        } else {
            $errorMessage = "Failed to update password. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 10px;
        }

        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 10px;
        }

        .back-button {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include("../navbar.php"); ?>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Edit Password</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($currentUsername); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Change Password</button>
                <!-- Back Button -->
                <a href="manage_user.php" class="btn btn-secondary w-100 back-button">Back</a>
                <!-- Display error or success message -->
                <?php if (!empty($errorMessage)): ?>
                    <div class="error-message"><?php echo $errorMessage; ?></div>
                <?php elseif (!empty($successMessage)): ?>
                    <div class="success-message"><?php echo $successMessage; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>

</html>