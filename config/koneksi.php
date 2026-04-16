<?php

$host = getenv("MYSQLHOST") ?: getenv("DB_HOST");
$user = getenv("MYSQLUSER") ?: getenv("DB_USER");
$pass = getenv("MYSQLPASSWORD") ?: getenv("DB_PASS");
$db   = getenv("MYSQLDATABASE") ?: getenv("DB_NAME");
$port = getenv("MYSQLPORT") ?: 3306;

$conn = null;

if ($host && $user) {
    $conn = mysqli_connect($host, $user, $pass, $db, $port);

    if (!$conn) {
        error_log("DB ERROR: " . mysqli_connect_error());
    }
} else {
    error_log("ENV DATABASE tidak ditemukan di Railway");
}