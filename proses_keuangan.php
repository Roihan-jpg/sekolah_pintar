<?php
session_start();
include 'koneksi.php';

// Cek Security
if($_SESSION['role'] != "Admin"){ header("location:login.php"); }

$id = $_GET['id'];
$aksi = $_GET['aksi'];

if($aksi == "terima"){
    // Ubah status jadi Lunas
    $update = mysqli_query($koneksi, "UPDATE tabel_keuangan SET status='Lunas', petugas_input='Admin (Verified)' WHERE id_keuangan='$id'");
    
    if($update){
        echo "<script>alert('Pembayaran DITERIMA! Status siswa kini Lunas.'); window.location='admin_approval_keuangan.php';</script>";
    }
} 
else if($aksi == "tolak"){
    // Ubah status jadi Ditolak (Siswa harus upload ulang nanti)
    $update = mysqli_query($koneksi, "UPDATE tabel_keuangan SET status='Ditolak' WHERE id_keuangan='$id'");
    
    if($update){
        echo "<script>alert('Pembayaran DITOLAK.'); window.location='admin_approval_keuangan.php';</script>";
    }
}
?>