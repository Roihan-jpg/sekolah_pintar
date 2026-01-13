<?php
session_start();
// Cek Login & Role Guru
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Guru"){
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Guru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar" style="background:#27ae60;">
        <h1>👨‍🏫 Panel Guru</h1>
        <div style="display:flex; align-items:center;">
            <span style="margin-right:15px; font-size:14px;">Halo, <?= $_SESSION['nama'] ?></span>
            <a href="logout.php" class="btn-small btn-red">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Selamat Datang, Bapak/Ibu Guru</h2>
        <div class="dashboard-grid">
            <div class="card">
                <h3>📷 Mulai Absensi Kelas</h3>
                <p>Mode Kiosk untuk Scan QR Code Siswa.</p>
                <a href="scan.php" class="btn">Buka Scanner</a>
            </div>
        </div>
    </div>
</body>
</html>