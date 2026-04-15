<?php
$host = getenv("mysql.railway.internalmysql.railway.internal");
$user = getenv("root");
$pass = getenv("NOnibzDGnoEcPvtdxCYpOHSNqwqGoBeL");
$db   = getenv("railway");
$port = getenv("3306");

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>