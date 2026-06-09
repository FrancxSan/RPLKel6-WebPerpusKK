<?php
date_default_timezone_set('Asia/Makassar');
$host = "localhost";
$user = "root";
$pass = "";
$db = "database_perpustakaan";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {


    die("connection failed: " . mysqli_connect_error());
}
?>