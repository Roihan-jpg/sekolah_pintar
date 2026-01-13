<?php
session_start();
include 'koneksi.php';

// Cek Security (Hanya Admin)
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Admin"){
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Pembayaran</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="navbar">
        <h1>💰 Verifikasi Pembayaran Masuk</h1>
        <a href="index.php">Dashboard</a>
    </div>

    <div class="container">
        <h2>Daftar Menunggu Konfirmasi</h2>
        <p>Cek mutasi bank anda sesuai bukti transfer di bawah ini, lalu klik Terima/Tolak.</p>

        <?php
        // Query ambil data keuangan Pending + Nama Siswa + Kelas
        $query = "SELECT k.*, s.nama_lengkap, kl.nama_kelas 
                  FROM tabel_keuangan k
                  JOIN tabel_siswa s ON k.nis = s.nis
                  JOIN tabel_kelas kl ON s.id_kelas = kl.id_kelas
                  WHERE k.status = 'Pending' AND k.bukti_transfer != '' 
                  ORDER BY k.tanggal_bayar ASC";
        
        $result = mysqli_query($koneksi, $query);
        
        // Cek jika kosong
        if(mysqli_num_rows($result) == 0){
            echo "<div class='card' style='background:#f9f9f9; color:#777;'>
                    <h3>Tidak ada pembayaran baru yang perlu diverifikasi.</h3>
                  </div>";
        } else {
        ?>

        <table>
            <thead>
                <tr>
                    <th>Tgl Upload</th>
                    <th>Siswa</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>
                    <th>Bukti Transfer</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while($d = mysqli_fetch_array($result)){
                ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($d['tanggal_bayar'])) ?></td>
                    <td>
                        <b><?= $d['nama_lengkap'] ?></b><br>
                        <small>Kelas: <?= $d['nama_kelas'] ?></small>
                    </td>
                    <td><?= $d['keterangan'] ?></td>
                    <td style="font-weight:bold; color:#27ae60;">
                        Rp <?= number_format($d['nominal'], 0, ',', '.') ?>
                    </td>
                    <td>
                        <a href="uploads/<?= $d['bukti_transfer'] ?>" target="_blank" class="btn-small" style="background:#3498db;">
                            🖼️ Lihat Bukti
                        </a>
                    </td>
                    <td align="center">
                        <a href="proses_keuangan.php?id=<?= $d['id_keuangan'] ?>&aksi=terima" 
                           class="btn-small btn-green" onclick="return confirm('Yakin uang sudah masuk? Status akan jadi LUNAS.')">
                           ✔ Terima
                        </a>
                        <br><br> <a href="proses_keuangan.php?id=<?= $d['id_keuangan'] ?>&aksi=tolak" 
                           class="btn-small btn-red" onclick="return confirm('Tolak pembayaran ini?')">
                           ✖ Tolak
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
</body>
</html>