<?php

$host = getenv("MYSQLHOST") ?: getenv("DB_HOST");
$user = getenv("MYSQLUSER") ?: getenv("DB_USER");
$pass = getenv("MYSQLPASSWORD") ?: getenv("DB_PASS");
$db   = getenv("MYSQLDATABASE") ?: getenv("DB_NAME");
$port = getenv("MYSQLPORT") ?: 3306;

if (!$host || !$user) {
    die("DATABASE ENV belum diset di Railway");
}

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}