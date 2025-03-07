<?php
require('database.php');
if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $check_user = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check_user) > 0) {
        $token = bin2hex(random_bytes(50));
        mysqli_query($con, "INSERT INTO password_resets (email, token) VALUES
('$email', '$token')");
        echo "<p>Password reset link: <a href='reset_password.php?token=$token'>Reset
Password</a></p>";
    } else {
        echo "<p>Email not found.</p>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password</title>
</head>
<body>
    <h1>Forgot Password</h1>
    <form action="forgot_password.php" method="post">
        <input type="email" name="email" placeholder="Enter your email" required />
        <input type="submit" value="Request Reset Link" />
    </form>
</body>
</html>