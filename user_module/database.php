<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'recipehub_db';

$con = new mysqli($host, $user, $pass, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
