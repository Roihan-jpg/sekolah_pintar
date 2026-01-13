<?php
session_start();
include 'koneksi.php';

if($_SESSION['role'] != "Admin"){ header("location:login.php"); }

$nis = $_GET['nis'];

// Cek apakah siswa punya data penting
// Ini optional, untuk mencegah error database yang kasar
$cek_absen = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tabel_absensi WHERE nis='$nis'"));
$cek_uang = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tabel_keuangan WHERE nis='$nis'"));

if($cek_absen > 0 || $cek_uang > 0){
    echo "<script>alert('GAGAL HAPUS! Siswa ini memiliki $cek_absen data absensi dan $cek_uang data keuangan. Hapus data tersebut terlebih dahulu.'); window.location='admin_siswa.php';</script>";
} else {
    // Hapus Izin dulu (karena ini biasanya kurang penting)
    mysqli_query($koneksi, "DELETE FROM tabel_perizinan WHERE nis='$nis'");
    
    // Hapus Siswa
    $hapus = mysqli_query($koneksi, "DELETE FROM tabel_siswa WHERE nis='$nis'");
    
    if($hapus){
        echo "<script>alert('Siswa berhasil dihapus.'); window.location='admin_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus database.'); window.location='admin_siswa.php';</script>";
    }
}
?>