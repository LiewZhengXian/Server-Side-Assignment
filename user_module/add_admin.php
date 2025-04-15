<?php
session_start();
require('database.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: ../index.php"); // Redirect non-admin users to the homepage
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .add-admin-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .btn-add-admin {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 600;
        }

        .success-message {
            text-align: center;
            padding: 30px;
            background-color: #d4edda;
            border-radius: 10px;
            margin: 100px auto;
            max-width: 450px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-link:hover {
            background-color: #0069d9;
            color: white;
        }
    </style>
</head>

<body>
    <?php include("../navbar.php"); ?>
    <?php
    if (isset($_POST['username'])) {
        $username = stripslashes($_REQUEST['username']);
        $username = mysqli_real_escape_string($con, $username);
        $email = stripslashes($_REQUEST['email']);
        $email = mysqli_real_escape_string($con, $email);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);

        // Insert new admin account with isAdmin = 1
        $query = "INSERT INTO `User` (username, password, email, isAdmin)
                  VALUES ('$username', '" . md5($password) . "', '$email', 1)";
        $result = mysqli_query($con, $query);

        if ($result) {
            echo "<div class='success-message'>
                    <h3 class='mb-4'><i class='bi bi-check-circle-fill'></i> Admin Added Successfully!</h3>
                    <p>The new admin account has been created successfully.</p>
                    <a href='../index.php' class='back-link'>Back to Home</a>
                  </div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Error: Unable to add admin. Please try again.</div>";
        }
    } else {
    ?>
        <div class="container">
            <div class="add-admin-container">
                <h1 class="form-title">Add Admin</h1>
                <form name="add_admin" action="" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter admin email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-add-admin">Add Admin</button>
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../footer.php'; ?>
</body>

</html>