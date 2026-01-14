<?php
include 'koneksi.php';
session_start();
// (Bagian Cek Login tetap sama, jangan dihapus)
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Siswa"){ header("location:login.php"); }
$nis_siswa = $_SESSION['username']; 

// Ambil Data Lengkap Siswa untuk Tampilan Kartu
$data_siswa = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM tabel_siswa JOIN tabel_kelas ON tabel_siswa.id_kelas = tabel_kelas.id_kelas WHERE nis='$nis_siswa'"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <div class="navbar" style="background:#2980b9;">
        <h1>🎓 Panel Siswa</h1>
        <div style="display:flex; align-items:center;">
            <span style="margin-right:15px;"><?= $_SESSION['nama'] ?></span>
            <a href="logout.php" class="btn-small btn-red">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="dashboard-grid">
            <div class="card orange">
                <h3>💰 Bayar SPP</h3>
                <p>Cek tunggakan & upload bukti transfer.</p>
                <a href="siswa_keuangan.php" class="btn">Info Pembayaran</a>
            </div>
            <div class="card" style="border-top-color: #e67e22;">
                <h3>📅 Jadwal Saya</h3>
                <p>Cek mata pelajaran hari ini & besok.</p>
                <a href="siswa_jadwal.php" class="btn" style="background:#e67e22;">Lihat Jadwal</a>
            </div>

            <div class="card" style="border-top-color: #34495e;">
                <h3>🔑 Akun Saya</h3>
                <p>Ganti password login anda.</p>
                <a href="siswa_ganti_password.php" class="btn" style="background:#34495e;">Ganti Password</a>
            </div>
            
            <div class="card">
                <h3>🆔 Kartu Pelajar Digital</h3>
                <div style="display:flex; justify-content:center; margin-bottom:15px;">
                    <div class="id-card">
                        <div class="header-bar"></div>
                        <div class="photo-area"></div> 
                        <div class="data-area" style="text-align:left;"> <div class="school-name">Sekolah Menengah Kejuruan</div>
                            <h3 class="student-name"><?= $data_siswa['nama_lengkap'] ?></h3>
                            <p class="student-nis">NIS: <?= $data_siswa['nis'] ?></p>
                            <span class="student-class"><?= $data_siswa['nama_kelas'] ?></span>
                            <div id="qrcode-dashboard" class="qr-area"></div>
                        </div>
                    </div>
                </div>
                <a href="cetak_kartu.php" target="_blank" class="btn" style="background:#34495e;">🖨️ Download / Cetak Kartu</a>
            </div>

            <div class="card orange">
                <h3>📩 Izin / Sakit</h3>
                <p>Upload surat izin jika tidak masuk.</p>
                <a href="siswa_upload_izin.php" class="btn">Ajukan Izin</a>
            </div>

            <div class="card" style="grid-column: span 2; text-align:left;">
                <h3>📅 Riwayat Kehadiran</h3>
                <table>
                    <thead><tr><th>Tanggal</th><th>Jam</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($koneksi, "SELECT * FROM tabel_absensi WHERE nis='$nis_siswa' ORDER BY tanggal DESC LIMIT 5");
                        while($d = mysqli_fetch_array($q)){
                        ?>
                        <tr>
                            <td><?= $d['tanggal'] ?></td>
                            <td><?= $d['waktu_scan'] ?></td>
                            <td><?= $d['status_kehadiran'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script type="text/javascript">
        new QRCode(document.getElementById("qrcode-dashboard"), {
            text: "<?= $data_siswa['qr_code_string'] ?>",
            width: 60, height: 60,
            colorDark : "#000000", colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>