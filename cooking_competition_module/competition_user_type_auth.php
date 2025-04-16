<?php
session_start();

$redirectPage = (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1) 
    ? 'admin/admin_competition.php' 
    : 'competition_main.php';

header("Location: $redirectPage");
exit();