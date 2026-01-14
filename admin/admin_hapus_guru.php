<?php
session_start();
include 'koneksi.php';

if($_SESSION['role'] != "Admin"){ header("location:login.php"); }

$nip = $_GET['nip'];
$hapus = mysqli_query($koneksi, "DELETE FROM tabel_guru WHERE nip='$nip'");

if($hapus){
    echo "<script>alert('Data Guru berhasil dihapus.'); window.location='admin_guru.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus.'); window.location='admin_guru.php';</script>";
}
?>