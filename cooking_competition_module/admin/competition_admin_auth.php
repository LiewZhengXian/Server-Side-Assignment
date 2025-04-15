<?php
session_start();

$redirectPage = (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1) 
    ? '../admin/admin_competition.php' 
    : '../cooking_competition_module/competition_main.php';

header("Location: $redirectPage");
exit();