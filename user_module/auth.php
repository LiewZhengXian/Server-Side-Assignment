<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ./user_module/login.php");
    exit();
}
?>