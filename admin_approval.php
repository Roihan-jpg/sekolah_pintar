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
    <title>Admin Approval</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="navbar" style="background:#c0392b;"> <h1>📩 Approval Izin Masuk</h1>
        <a href="index.php">Dashboard</a>
    </div>

    <div class="container">
        <h2>Daftar Menunggu Persetujuan</h2>
        
        <?php
        $cek = mysqli_query($koneksi, "SELECT * FROM tabel_perizinan WHERE status_approval='Pending'");
        if(mysqli_num_rows($cek) == 0) {
            echo "<div style='text-align:center; padding:50px; color:#999;'><h3>Tidak ada pengajuan izin baru.</h3></div>";
        } else {
        ?>
        
        <table>
            <thead>
                <tr>
                    <th>Tanggal Izin</th>
                    <th>NIS</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Bukti</th>
                    <th style="width:180px; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while($d = mysqli_fetch_array($cek)){
                ?>
                <tr>
                    <td><?= date('d F Y', strtotime($d['tanggal_izin'])) ?></td>
                    <td><b><?= $d['nis'] ?></b></td>
                    <td><?= $d['jenis_izin'] ?></td>
                    <td><?= $d['keterangan'] ?></td>
                    <td>
                        <a href="uploads/<?= $d['file_bukti'] ?>" target="_blank" style="color:#3498db; text-decoration:none;">
                            📷 Lihat Foto
                        </a>
                    </td>
                    <td align="center">
                        <a href="proses_approval.php?id=<?= $d['id_izin'] ?>&aksi=terima&nis=<?= $d['nis'] ?>&tgl=<?= $d['tanggal_izin'] ?>&jns=<?= $d['jenis_izin'] ?>" 
                           class="btn-small btn-green" onclick="return confirm('Yakin terima?')">✔ Terima</a>
                           
                        <a href="proses_approval.php?id=<?= $d['id_izin'] ?>&aksi=tolak" 
                           class="btn-small btn-red" onclick="return confirm('Tolak izin?')">✖ Tolak</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
</body>
</html>