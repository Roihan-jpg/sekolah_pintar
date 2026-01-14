<?php
session_start();
include 'koneksi.php'; 

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
            
            <?php 
            $jam_sekarang = date('H:i');
            // Contoh: Menu Absen HILANG setelah jam 09:00 pagi
            if($jam_sekarang <= "09:00"): 
            ?>
                <div class="card">
                    <h3>📷 Absensi Pagi (Jam Ke-1)</h3>
                    <p>Mode scan untuk absensi kehadiran harian siswa.</p>
                    <a href="guru_pilih_kelas.php" class="btn">Buka Menu Absen</a>
                </div>
            <?php else: ?>
                <div class="card" style="background:#f4f4f4; border-top-color:#999;">
                    <h3 style="color:#777;">📷 Absensi Ditutup</h3>
                    <p>Absensi harian hanya tersedia pada jam pertama (Pagi).</p>
                    <button disabled class="btn" style="background:#ccc; cursor:not-allowed;">Sudah Lewat Jam 09:00</button>
                </div>
            <?php endif; ?>

            <div class="card">
                <h3>📅 Jadwal Mengajar</h3>
                <p>Lihat jadwal mengajar Anda hari ini.</p>
                <a href="guru_jadwal.php" class="btn" style="background:#f39c12;">Lihat Jadwal</a>
            </div>

        </div>

        <div style="margin-top: 40px;">
            <h3>📋 Rekap Absensi Hari Ini (<?= date('d-m-Y') ?>)</h3>
            <p style="color:#666; font-size:14px;">Daftar siswa yang sudah Anda scan hari ini. Data akan reset otomatis besok.</p>

            <div style="background:white; border-radius:8px; overflow:hidden; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #eee;">
                            <th style="padding:12px; text-align:left; color:#555;">Jam</th>
                            <th style="padding:12px; text-align:left; color:#555;">Nama Siswa</th>
                            <th style="padding:12px; text-align:left; color:#555;">Kelas</th>
                            <th style="padding:12px; text-align:center; color:#555;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $nip = $_SESSION['username'];
                        $tgl_hari_ini = date('Y-m-d');

                        $query_rekap = "SELECT a.*, s.nama_lengkap, k.nama_kelas 
                                        FROM tabel_absensi a
                                        JOIN tabel_siswa s ON a.nis = s.nis
                                        JOIN tabel_kelas k ON s.id_kelas = k.id_kelas
                                        WHERE a.nip_scanner = '$nip' AND a.tanggal = '$tgl_hari_ini'
                                        ORDER BY a.waktu_scan DESC";
                        
                        $hasil_rekap = mysqli_query($koneksi, $query_rekap);

                        if(mysqli_num_rows($hasil_rekap) > 0){
                            while($row = mysqli_fetch_array($hasil_rekap)){
                        ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px;"><?= $row['waktu_scan'] ?></td>
                            <td style="padding:12px; font-weight:bold;"><?= $row['nama_lengkap'] ?></td>
                            <td style="padding:12px;"><span style="background:#eef; padding:2px 8px; border-radius:4px; font-size:12px;"><?= $row['nama_kelas'] ?></span></td>
                            <td style="padding:12px; text-align:center;">
                                <span style="color:#27ae60; font-weight:bold;">✔ Hadir</span>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='4' style='padding:20px; text-align:center; color:#999;'>Belum ada siswa yang discan hari ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>