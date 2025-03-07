<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>User Registration</title>
</head>

<body>
    <?php
    require('database.php');
    if (isset($_REQUEST['username'])) {
        $username = stripslashes($_REQUEST['username']);
        $username = mysqli_real_escape_string($con, $username);
        $email = stripslashes($_REQUEST['email']);
        $email = mysqli_real_escape_string($con, $email);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        $query = "INSERT into `user` (username, password, email)
VALUES ('$username', '" . md5($password) . "', '$email')";
        $result = mysqli_query($con, $query);
        if ($result) {
            echo "<div class='form'>
    <h3>You are registered successfully.</h3>
    <br/>Click here to <a href='login.php'>Login</a></div>";
        }
    } else {
        ?>
        <div class="form">
            <h1>User Registration</h1>
            <form name="registration" action="" method="post">
                <input type="text" name="username" placeholder="Username" required /><br>
                <input type="email" name="email" placeholder="Email" required /><br>
                <input type="password" name="password" placeholder="Password" required /><br>
                <input type="submit" name="submit" value="Register" />
            </form>
        </div>
    <?php } ?>
</body>

</html>