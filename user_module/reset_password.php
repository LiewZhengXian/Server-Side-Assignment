<?php
require('database.php');
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']);
    $query = mysqli_query($con, "SELECT * FROM password_resets WHERE
token='$token' LIMIT 1");
    if (mysqli_num_rows($query) == 1) {
        $row = mysqli_fetch_assoc($query);
        $email = $row['email'];
        if (
            $_SERVER["REQUEST_METHOD"] == "POST" &&
            !empty($_POST['password'])
        ) {
            $password = mysqli_real_escape_string($con, $_POST['password']);
            $hashed_password = md5($password);
            mysqli_query($con, "UPDATE users SET password='$hashed_password'
 WHERE email='$email'");
            mysqli_query($con, "DELETE FROM password_resets WHERE
 email='$email'");

            header("Location: login.php?reset_success=1");
            exit();
        }
    } else {
        echo "<p>Invalid or expired token.</p>";
    }
} else {
    echo "<p>No reset token provided.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <form method="POST">
        <label>New Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>