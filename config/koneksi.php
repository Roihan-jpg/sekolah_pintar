<?php
$host = "localhost";
$user = "root";
$pass = "roihan5758";
$db   = "aplikasi_pintar";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
?>