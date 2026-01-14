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
    <title>Manajemen Data Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="navbar">
        <h1>🎓 Data Siswa</h1>
        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>

    <div class="container">
        
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <a href="admin_tambah_siswa.php" class="btn" style="width:auto; background:#27ae60;">+ Tambah Siswa Baru</a>
            
            <form method="GET" style="display:flex; gap:10px;">
                <select name="kelas" style="padding:8px; margin:0;">
                    <option value="">Semua Kelas</option>
                    <?php
                    $k = mysqli_query($koneksi, "SELECT * FROM tabel_kelas");
                    while($row = mysqli_fetch_array($k)){
                        $selected = (isset($_GET['kelas']) && $_GET['kelas'] == $row['id_kelas']) ? 'selected' : '';
                        echo "<option value='".$row['id_kelas']."' $selected>".$row['nama_kelas']."</option>";
                    }
                    ?>
                </select>

                <select name="angkatan" style="padding:8px; margin:0;">
                    <option value="">Semua Angkatan</option>
                    <?php
                    // Ambil tahun angkatan yang ada di database agar dinamis
                    $a = mysqli_query($koneksi, "SELECT DISTINCT angkatan FROM tabel_siswa ORDER BY angkatan DESC");
                    while($row = mysqli_fetch_array($a)){
                        $selected = (isset($_GET['angkatan']) && $_GET['angkatan'] == $row['angkatan']) ? 'selected' : '';
                        echo "<option value='".$row['angkatan']."' $selected>".$row['angkatan']."</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn-small" style="background:#34495e; border:none; cursor:pointer;">Filter</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Angkatan</th>
                    <th>Kelas</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Logika Filter
                $where = " WHERE 1=1 ";
                if(isset($_GET['kelas']) && $_GET['kelas'] != ''){
                    $where .= " AND s.id_kelas = '".$_GET['kelas']."'";
                }
                if(isset($_GET['angkatan']) && $_GET['angkatan'] != ''){
                    $where .= " AND s.angkatan = '".$_GET['angkatan']."'";
                }

                $query = "SELECT s.*, k.nama_kelas FROM tabel_siswa s 
                          JOIN tabel_kelas k ON s.id_kelas = k.id_kelas 
                          $where 
                          ORDER BY s.angkatan DESC, s.id_kelas ASC, s.nama_lengkap ASC";
                
                $result = mysqli_query($koneksi, $query);
                
                while($d = mysqli_fetch_array($result)){
                ?>
                <tr>
                    <td><b><?= $d['nis'] ?></b></td>
                    <td><?= $d['nama_lengkap'] ?></td>
                    <td><?= $d['angkatan'] ?></td>
                    <td><span style="background:#3498db; color:white; padding:2px 8px; border-radius:10px; font-size:12px;"><?= $d['nama_kelas'] ?></span></td>
                    <td align="center">
                        <a href="cetak_kartu.php?nis=<?= $d['nis'] ?>" target="_blank" class="btn-small" style="background:#34495e;">🖨️ Cetak</a>
                        
                        <a href="admin_edit_siswa.php?nis=<?= $d['nis'] ?>" class="btn-small btn-green">Edit</a>
                        <a href="admin_hapus_siswa.php?nis=<?= $d['nis'] ?>" class="btn-small btn-red" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>