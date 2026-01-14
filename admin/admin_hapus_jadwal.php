<?php
include '../config/koneksi.php';
$id = $_GET['id'];
$kelas = $_GET['kelas']; // Biar pas balik tetep di kelas yg sama

mysqli_query($koneksi, "DELETE FROM tabel_jadwal WHERE id_jadwal='$id'");
header("location:admin_jadwal.php?kelas=$kelas");
?>