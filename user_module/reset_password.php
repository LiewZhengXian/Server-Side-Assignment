<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="width: 400px;">
            <h2 class="text-center mb-4">Reset Password</h2>
            <?php
                require('database.php');
                if (isset($_GET['token'])) {
                    $token = mysqli_real_escape_string($con, $_GET['token']);
                    $query = mysqli_query($con, "SELECT * FROM password_resets WHERE token='$token' LIMIT 1");
                    if (mysqli_num_rows($query) == 1) {
                        $row = mysqli_fetch_assoc($query);
                        $email = $row['email'];
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['password'])) {
                            $password = mysqli_real_escape_string($con, $_POST['password']);
                            $hashed_password = md5($password);
                            mysqli_query($con, "UPDATE user SET password='$hashed_password' WHERE email='$email'");
                            mysqli_query($con, "DELETE FROM password_resets WHERE email='$email'");
                            header("Location: login.php?reset_success=1");
                            exit();
                        }
            ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
            <?php
                    } else {
                        echo '<div class="alert alert-danger text-center">Invalid or expired token.</div>';
                    }
                } else {
                    echo '<div class="alert alert-warning text-center">No reset token provided.</div>';
                }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>