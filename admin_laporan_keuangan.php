<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php");
}
?>
<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="navbar">
        <h1>📊 Laporan Keuangan</h1>
        <a href="index.php">Dashboard</a>
    </div>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <h2 style="margin:0;">Rekapitulasi Dana Masuk</h2>
                <small style="color:#666;">Data diurutkan dari transaksi terbaru</small>
            </div>
            <button onclick="window.print()" class="btn" style="width:auto; background:#7f8c8d;">🖨️ Cetak Laporan</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th style="text-align:right;">Nominal (Rp)</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($koneksi, "SELECT k.*, s.nama_lengkap FROM tabel_keuangan k 
                                                 JOIN tabel_siswa s ON k.nis = s.nis 
                                                 ORDER BY k.tanggal_bayar DESC");
                $total_uang = 0;
                while($row = mysqli_fetch_array($query)){
                    $total_uang += $row['nominal'];
                ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal_bayar'])) ?></td>
                    <td><?= $row['nama_lengkap'] ?></td>
                    <td><span style="background:#eef; padding:3px 8px; border-radius:4px; font-size:12px; color:#333;"><?= $row['jenis_transaksi'] ?></span></td>
                    <td><?= $row['keterangan'] ?></td>
                    <td align="right" style="font-family:monospace; font-size:14px;">
                        <?= number_format($row['nominal'], 0, ',', '.') ?>
                    </td>
                    <td><?= $row['petugas_input'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr style="background-color:#ecf0f1; font-weight:bold;">
                    <td colspan="4" align="right" style="padding:15px;">TOTAL PEMASUKAN</td>
                    <td align="right" style="color:#27ae60; font-size:16px;">Rp <?= number_format($total_uang, 0, ',', '.') ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>