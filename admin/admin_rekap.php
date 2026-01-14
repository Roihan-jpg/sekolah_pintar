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
    <title>Rekap Absensi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h1>📝 Rekap Absensi</h1>
        <a href="index.php">Kembali ke Dashboard</a>
    </div>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Data Absensi Hari Ini (<?= date('d-m-Y') ?>)</h3>
            <button onclick="window.print()" class="btn" style="width:auto; margin:0;">Cetak PDF</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jam Scan</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tgl = date('Y-m-d');
                $query = "SELECT abs.*, s.nama_lengkap, k.nama_kelas 
                          FROM tabel_absensi abs
                          JOIN tabel_siswa s ON abs.nis = s.nis
                          JOIN tabel_kelas k ON s.id_kelas = k.id_kelas
                          WHERE abs.tanggal = '$tgl' ORDER BY abs.waktu_scan DESC";
                $result = mysqli_query($koneksi, $query);
                $no = 1;
                while($row = mysqli_fetch_array($result)) {
                    // Warna status
                    $color = ($row['status_kehadiran'] == 'Hadir') ? 'green' : 'red';
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['waktu_scan'] ?></td>
                    <td><?= $row['nis'] ?></td>
                    <td><?= $row['nama_lengkap'] ?></td>
                    <td><?= $row['nama_kelas'] ?></td>
                    <td style="color:<?= $color ?>; font-weight:bold;"><?= $row['status_kehadiran'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>