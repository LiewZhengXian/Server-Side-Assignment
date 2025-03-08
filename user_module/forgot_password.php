<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="width: 400px;">
            <h2 class="text-center mb-4">Forgot Password</h2>
            <?php
                require('database.php');
                if (isset($_POST['email'])) {
                    $email = mysqli_real_escape_string($con, $_POST['email']);
                    $check_user = mysqli_query($con, "SELECT * FROM user WHERE email='$email'");
                    if (mysqli_num_rows($check_user) > 0) {
                        $token = bin2hex(random_bytes(50));
                        mysqli_query($con, "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')");
                        echo '<div class="alert alert-success text-center">Password reset link: <a href="reset_password.php?token=$token">Reset Password</a></div>';
                    } else {
                        echo '<div class="alert alert-danger text-center">Email not found.</div>';
                    }
                }
            ?>
            <form action="forgot_password.php" method="post">
                <div class="mb-3">
                    <label class="form-label">Enter your email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Request Reset Link</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
