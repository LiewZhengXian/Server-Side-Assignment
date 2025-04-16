<?php
session_start();
ob_start(); // Start output buffering
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <!-- Navbar -->
    <?php include("../navbar.php"); ?>
    <div class="container">
        <?php
        require('database.php');
        if (isset($_POST['email'])) {
            $email = stripslashes($_REQUEST['email']);
            $email = mysqli_real_escape_string($con, $email);
            $password = stripslashes($_REQUEST['password']);
            $password = mysqli_real_escape_string($con, $password);

            // Fetch user details including isAdmin
            $query = "SELECT user_id, username, email, isAdmin FROM `User` WHERE email='$email' AND password='" . md5($password) . "'";
            $result = mysqli_query($con, $query) or die(mysqli_error($con));
            $rows = mysqli_num_rows($result);

            if ($rows == 1) {
                $user = mysqli_fetch_assoc($result);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['isAdmin'] = $user['isAdmin']; // Store isAdmin in session

                // If the user is not an admin, store login details in cookies
                if ($user['isAdmin'] == 0) {
                    setcookie("user_id", $user['user_id'], time() + (86400 * 7), "/"); // 7 days
                    setcookie("username", $user['username'], time() + (86400 * 7), "/");
                    setcookie("email", $user['email'], time() + (86400 * 7), "/");
                }

                header("Location: ../index.php");
                exit();
            } else {
                echo "<div class='login-container'>
                    <div class='alert alert-danger text-center' role='alert'>
                        <strong>Error!</strong> Email or password is incorrect.
                    </div>
                    <div class='text-center'>
                        <a href='login.php' class='btn btn-outline-primary'>Try Again</a>
                    </div>
                </div>";
            }
        } else {
            // Check if user cookies exist and log in automatically
            if (isset($_COOKIE['user_id']) && isset($_COOKIE['username']) && isset($_COOKIE['email'])) {
                $_SESSION['user_id'] = $_COOKIE['user_id'];
                $_SESSION['username'] = $_COOKIE['username'];
                $_SESSION['email'] = $_COOKIE['email'];
                $_SESSION['isAdmin'] = 0; // Regular user

                header("Location: ../index.php");
                exit();
            }
        ?>
            <div class="login-container">
                <h1 class="form-title">Login</h1>
                <form action="" method="post" name="login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password" required>
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

    <?php include '../footer.php'; ?>

</body>

</html>
<?php
ob_end_flush(); // Flush the output buffer
?>