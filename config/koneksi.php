<?php

$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$db   = getenv("MYSQLDATABASE");
$port = getenv("MYSQLPORT");

// DEBUG kalau ENV kosong
if (!$host || !$user || !$db) {
    die("ENV Railway tidak terbaca. Cek Variables Railway.");
}

// koneksi aman
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// cek error detail
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

echo "DB CONNECT OK"; // sementara untuk test
?>