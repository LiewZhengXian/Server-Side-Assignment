<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
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
        .btn-login {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 600;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        require('database.php');
        if (isset($_POST['username'])) {
            $username = stripslashes($_REQUEST['username']);
            $username = mysqli_real_escape_string($con, $username);
            $password = stripslashes($_REQUEST['password']);
            $password = mysqli_real_escape_string($con, $password);

            // Modify the query to select the user_id along with other fields
            $query = "SELECT user_id, username FROM `user` WHERE username='$username' AND password='" . md5($password) . "'";
            $result = mysqli_query($con, $query) or die(mysqli_error($con));
            $rows = mysqli_num_rows($result);

            // Check if user exists
            if ($rows == 1) {
                // Fetch the user_id and username
                $user = mysqli_fetch_assoc($result);
                
                // Store the user_id and username in the session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to the homepage or dashboard
                header("Location: ../index.php");
                exit();
            } else {
                echo "<div class='login-container'>
                    <div class='alert alert-danger text-center' role='alert'>
                        <strong>Error!</strong> Username or password is incorrect.
                    </div>
                    <div class='text-center'>
                        <a href='login.php' class='btn btn-outline-primary'>Try Again</a>
                    </div>
                </div>";
            }
        } else {
        ?>
        <div class="login-container">
            <h1 class="form-title">Welcome Back</h1>
            <form action="" method="post" name="login">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-login">Login</button>
            </form>
            <div class="links">
                <p class="mt-3">Forgot Password? <a href="forgot_password.php">Reset Here</a></p>
                <p>Not registered yet? <a href="registration.php">Register Here</a></p>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
