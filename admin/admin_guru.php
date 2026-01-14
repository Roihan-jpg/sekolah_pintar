<?php
session_start();
include '../config/koneksi.php';

// Cek Security (Hanya Admin)
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Admin"){
    header("location:../auth/login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Data Guru</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="navbar">
        <h1>👨‍🏫 Data Guru</h1>
        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>

    <div class="container">
        
        <div style="margin-bottom:20px;">
            <a href="admin_tambah_guru.php" class="btn" style="width:auto; background:#27ae60;">+ Tambah Guru Baru</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:150px;">NIP</th>
                    <th>Nama Guru</th>
                    <th>Status Akun</th>
                    <th style="width:200px; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($koneksi, "SELECT * FROM tabel_guru ORDER BY nama_guru ASC");
                while($d = mysqli_fetch_array($query)){
                ?>
                <tr>
                    <td><b><?= $d['nip'] ?></b></td>
                    <td><?= $d['nama_guru'] ?></td>
                    <td>
                        <span style="background:#eef; color:#333; padding:2px 8px; border-radius:4px; font-size:12px;">Aktif</span>
                    </td>
                    <td align="center">
                        <a href="admin_edit_guru.php?nip=<?= $d['nip'] ?>" class="btn-small btn-green">Edit / Reset</a>
                        <a href="admin_hapus_guru.php?nip=<?= $d['nip'] ?>" class="btn-small btn-red" onclick="return confirm('Yakin hapus guru ini?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>