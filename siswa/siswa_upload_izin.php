<?php
include '../config/koneksi.php';
session_start();
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Siswa"){ header("location:../auth/login.php"); }
$nis = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head><title>Izin Siswa</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
    <div class="navbar" style="background:#2980b9;">
        <h1>📩 Layanan Izin</h1>
        <a href="siswa_dashboard.php">Kembali</a>
    </div>

    <div class="container">
        <div class="dashboard-grid">
            
            <div class="card" style="text-align:left;">
                <h3>Buat Pengajuan Baru</h3>
                <?php
                if (isset($_POST['upload'])) {
                    $tgl = $_POST['tanggal'];
                    $jenis = $_POST['jenis'];
                    $ket = $_POST['keterangan'];
                    $foto = $_FILES['foto_surat']['name'];
                    $tmp = $_FILES['foto_surat']['tmp_name'];
                    $lokasi = "../uploads/" . $foto;
                    
                    if(move_uploaded_file($tmp, $lokasi)) {
                        $q = mysqli_query($koneksi, "INSERT INTO tabel_perizinan (nis, tanggal_izin, jenis_izin, keterangan, file_bukti, status_approval) 
                                                     VALUES ('$nis', '$tgl', '$jenis', '$ket', '$foto', 'Pending')");
                        if ($q) echo "<div style='color:green; margin-bottom:10px;'>✅ Terkirim! Cek status di samping/bawah.</div>";
                    }
                }
                ?>
                <form method="POST" enctype="multipart/form-data">
                    <label>Tanggal:</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                    <label>Jenis:</label>
                    <select name="jenis"><option>Sakit</option><option>Izin</option></select>
                    <label>Keterangan:</label>
                    <textarea name="keterangan" required placeholder="Alasan..."></textarea>
                    <label>Foto Surat:</label>
                    <input type="file" name="foto_surat" required>
                    <button type="submit" name="upload" class="btn">Kirim Pengajuan</button>
                </form>
            </div>

            <div class="card" style="text-align:left;">
                <h3>Riwayat & Status Pengajuan</h3>
                <table>
                    <thead>
                        <tr><th>Tanggal</th><th>Jenis</th><th>Status Admin</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $q = mysqli_query($koneksi, "SELECT * FROM tabel_perizinan WHERE nis='$nis' ORDER BY tanggal_izin DESC");
                        while($d = mysqli_fetch_array($q)){
                            // Warna Badge Status
                            $st = $d['status_approval'];
                            $color = 'orange'; // Pending
                            if($st == 'Disetujui') $color = 'green';
                            if($st == 'Ditolak') $color = 'red';
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($d['tanggal_izin'])) ?></td>
                            <td><?= $d['jenis_izin'] ?></td>
                            <td>
                                <span style="background:<?= $color ?>; color:white; padding:3px 8px; border-radius:5px; font-size:12px;">
                                    <?= strtoupper($st) ?>
                                </span>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>
</html>