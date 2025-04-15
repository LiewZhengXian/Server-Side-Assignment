<?php
session_start();
require('database.php');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .registration-container {
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

        .btn-register {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 600;
        }

        .error-message {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 5px;
            color: #721c24;
            font-size: 0.875em;
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

        .form-text {
            margin-top: 5px;
            font-size: 0.875em;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include("../navbar.php"); ?>

    <div class="container">
        <?php
        $errorMessage = ""; // Initialize error message
        if (isset($_POST['username'])) {
            $username = stripslashes($_POST['username']);
            $username = mysqli_real_escape_string($con, $username);
            $email = stripslashes($_POST['email']);
            $email = mysqli_real_escape_string($con, $email);
            $password = stripslashes($_POST['password']);
            $password = mysqli_real_escape_string($con, $password);

            // Check if username or email already exists
            $checkQuery = "SELECT * FROM `user` WHERE username='$username' OR email='$email'";
            $checkResult = mysqli_query($con, $checkQuery);

            if (mysqli_num_rows($checkResult) > 0) {
                $errorMessage = "Username or email already exists. Please try again with a different username or email.";
            } else {
                // Insert new user
                $query = "INSERT INTO `user` (username, password, email) VALUES ('$username', '" . md5($password) . "', '$email')";
                $result = mysqli_query($con, $query);

                if ($result) {
                    echo "<div class='success-message'>
                            <h3 class='mb-4'><i class='bi bi-check-circle-fill'></i> Registration Successful!</h3>
                            <p>Your account has been created successfully.</p>
                            <a href='login.php' class='btn btn-primary'>Login Now</a>
                          </div>";
                    exit(); // Stop further execution after success
                } else {
                    $errorMessage = "Something went wrong. Please try again later.";
                }
            }
        }
        ?>
        <div class="registration-container">
            <h1 class="form-title">Create Account</h1>
            <form name="registration" action="" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                    <div class="form-text">Username must be unique and will be used for login.</div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    <div class="form-text">We'll never share your email with anyone else.</div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                    <div class="form-text">Use a strong password with at least 8 characters.</div>
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-register">Register</button>
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
                <!-- Display error message below the button -->
                <?php if (!empty($errorMessage)) : ?>
                    <div class="error-message"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>