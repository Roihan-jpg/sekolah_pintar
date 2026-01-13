<?php
session_start();
include 'koneksi.php';
if($_SESSION['role'] != "Admin"){ header("location:login.php"); }

// Filter Kelas
$kelas_pilih = isset($_GET['kelas']) ? $_GET['kelas'] : '';
?>
<!DOCTYPE html>
<html>
<head><title>Kelola Jadwal Pelajaran</title><link rel="stylesheet" href="style.css"></head>
<body>

    <div class="navbar">
        <h1>📅 Jadwal Pelajaran</h1>
        <a href="index.php">Dashboard</a>
    </div>

    <div class="container">
        
        <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
            <a href="admin_tambah_jadwal.php" class="btn" style="background:#27ae60;">+ Tambah Jadwal</a>
            
            <form method="GET">
                <select name="kelas" onchange="this.form.submit()" style="padding:10px;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php
                    $k = mysqli_query($koneksi, "SELECT * FROM tabel_kelas ORDER BY nama_kelas ASC");
                    while($r = mysqli_fetch_array($k)){
                        $sel = ($kelas_pilih == $r['id_kelas']) ? 'selected' : '';
                        echo "<option value='$r[id_kelas]' $sel>$r[nama_kelas]</option>";
                    }
                    ?>
                </select>
            </form>
        </div>

        <?php if($kelas_pilih == ''){ ?>
            <div style="text-align:center; padding:50px; color:#666;">
                <h3>👈 Silakan Pilih Kelas di pojok kanan atas untuk melihat jadwal.</h3>
            </div>
        <?php } else { ?>

        <table>
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengajar</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Urutan Hari Custom (Senin dulu)
                $sql = "SELECT j.*, g.nama_guru 
                        FROM tabel_jadwal j 
                        JOIN tabel_guru g ON j.nip_guru = g.nip 
                        WHERE j.id_kelas = '$kelas_pilih' 
                        ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), j.jam_mulai ASC";
                
                $q = mysqli_query($koneksi, $sql);
                while($d = mysqli_fetch_array($q)){
                ?>
                <tr>
                    <td><b><?= $d['hari'] ?></b></td>
                    <td><?= date('H:i', strtotime($d['jam_mulai'])) ?> - <?= date('H:i', strtotime($d['jam_selesai'])) ?></td>
                    <td><?= $d['mata_pelajaran'] ?></td>
                    <td><?= $d['nama_guru'] ?></td>
                    <td align="center">
                        <a href="admin_hapus_jadwal.php?id=<?= $d['id_jadwal'] ?>&kelas=<?= $kelas_pilih ?>" 
                           class="btn-small btn-red" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
</body>
</html>