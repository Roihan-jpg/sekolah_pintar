
<?php 
session_start();
include '../config/koneksi.php'; 

// --- SECURITY CHECK ---
// Cek apakah user sudah login DAN apakah rolenya ADMIN
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Admin"){
    // Jika bukan admin, tendang ke login
    header("location:../auth/login.php");
    exit();
}

// Hitung Statistik Cepat
$tgl = date('Y-m-d');

// 1. Total Siswa
$q1 = mysqli_query($koneksi, "SELECT count(*) as total FROM tabel_siswa");
$d1 = mysqli_fetch_array($q1);
$total_siswa = $d1['total'];

// 2. Yang Hadir Hari Ini
$q2 = mysqli_query($koneksi, "SELECT count(*) as total FROM tabel_absensi WHERE tanggal='$tgl' AND status_kehadiran='Hadir'");
$d2 = mysqli_fetch_array($q2);
$hadir_hari_ini = $d2['total'];

// 3. Uang Masuk Hari Ini
$q3 = mysqli_query($koneksi, "SELECT SUM(nominal) as total FROM tabel_keuangan WHERE date(tanggal_bayar) = CURDATE()");
$d3 = mysqli_fetch_array($q3);
$uang_hari_ini = $d3['total'] ? $d3['total'] : 0;

// ... (Kode statistik lama) ...

// 4. Cek Pembayaran Pending
$q4 = mysqli_query($koneksi, "SELECT count(*) as total FROM tabel_keuangan WHERE status='Pending'");
$d4 = mysqli_fetch_array($q4);
$total_pending_bayar = $d4['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Sekolah Pintar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="navbar">
    <h1>🏫 Sekolah Pintar</h1>
    <div style="display:flex; align-items:center;">
        <span style="font-size:14px; margin-right:15px;">Halo, <b><?= $_SESSION['nama'] ?></b></span>
        <a href="../auth/logout.php" style="background:#c0392b; padding:5px 10px; border-radius:4px; font-size:12px;">Logout</a>
    </div>
</div>

    <div class="container">
        <h2>Selamat Datang, Admin!</h2>
        <p>Berikut adalah ringkasan data sekolah hari ini (<?= date('d-m-Y') ?>)</p>

        <div class="dashboard-grid">
            <div class="card orange">
                <h3>💰 Verifikasi Pembayaran</h3>
                <div class="stat-number"><?= $total_pending_bayar ?></div>
                <p>Menunggu Konfirmasi</p>
                <a href="admin_approval_keuangan.php" class="btn">Cek Pembayaran</a>
            </div>
            <div class="card">
                <h3>Total Siswa</h3>
                <div class="stat-number"><?= $total_siswa ?></div>
                <p>Siswa Terdaftar</p>
            </div>
            <div class="card green">
                <h3>Hadir Hari Ini</h3>
                <div class="stat-number"><?= $hadir_hari_ini ?></div>
                <p>Siswa sudah scan</p>
            </div>
            <div class="card orange">
                <h3>Uang Masuk</h3>
                <div class="stat-number">Rp <?= number_format((float)$uang_hari_ini, 0, ',', '.') ?></div>
                <p>Transaksi Hari Ini</p>
            </div>
        </div>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ddd;">

        <h3>Menu Aplikasi</h3>
        
        <div class="dashboard-grid">
            
            <div class="card" style="border-top-color: #8e44ad;">
                <h3>👨‍🏫 Data Guru</h3>
                <p>Tambah guru & reset password akun.</p>
                <a href="admin_guru.php" class="btn" style="background:#8e44ad;">Kelola Guru</a>
            </div>

            <div class="card" style="border-top-color: #f1c40f;">
                <h3>📅 Jadwal Pelajaran</h3>
                <p>Atur jadwal mengajar guru & kelas.</p>
                <a href="admin_jadwal.php" class="btn" style="background:#f1c40f; color:#333;">Kelola Jadwal</a>
            </div>
            
            <div class="card">
                <h3>🎓 Data Siswa</h3>
                <p>Tambah, Edit, Hapus data siswa & Angkatan.</p>
                <a href="admin_siswa.php" class="btn">Kelola Siswa</a>
            </div>

            <div class="card" style="border-top-color: #2980b9;">
                <h3>📊 Laporan Absensi</h3>
                <p>Cetak rekap kehadiran bulanan per kelas.</p>
                <a href="admin_rekap_absen.php" class="btn" style="background:#2980b9;">Buka Laporan</a>
            </div>

            <div class="card">
                <h3>📷 Scan Absen</h3>
                <p>Mode Kiosk untuk Guru/Siswa scan QR Code.</p>
                <a href="scan.php" class="btn">Buka Scanner</a>
            </div>

            <div class="card">
                <h3>📝 Rekap Absen</h3>
                <p>Lihat siapa yang hadir, telat, atau alpa.</p>
                <a href="admin_rekap.php" class="btn">Lihat Laporan</a>
            </div>

            <div class="card">
                <h3>🖨️ Cetak Kartu</h3>
                <p>Print ID Card Siswa beserta QR Code.</p>
                <a href="cetak_kartu.php" class="btn">Cetak ID</a>
            </div>

            <div class="card red">
                <h3>📩 Approval Izin</h3>
                <p>Cek foto surat sakit dan setujui izin.</p>
                <a href="admin_approval.php" class="btn">Cek Surat Masuk</a>
            </div>

            <div class="card red">
                <h3>📤 Upload Izin (Siswa)</h3>
                <p>Halaman untuk siswa upload foto surat.</p>
                <a href="siswa_upload_izin.php" class="btn">Form Siswa</a>
            </div>

            <div class="card orange">
                <h3>💰 Bayar SPP</h3>
                <p>Input pembayaran SPP atau Tabungan.</p>
                <a href="admin_bayar.php" class="btn">Input Transaksi</a>
            </div>
            
            <div class="card orange">
                <h3>📊 Laporan Keuangan</h3>
                <p>Rekap uang masuk harian/bulanan.</p>
                <a href="admin_laporan_keuangan.php" class="btn">Lihat Data</a>
            </div>

        </div>
    </div>

</body>
</html>