<?php

function cekLogin(){

if(!isset($_SESSION['user'])){
header("Location:index.php");
exit;
}

}

function cekAdmin(){

cekLogin();

if($_SESSION['user']['role']!="admin"){
die("Akses ditolak");
}

}

function cekSiswa(){

cekLogin();

if($_SESSION['user']['role']!="siswa"){
die("Akses ditolak");
}

}