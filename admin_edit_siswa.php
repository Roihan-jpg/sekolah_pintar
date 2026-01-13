<?php
session_start();
include 'koneksi.php';
$nis = $_GET['nis'];
$data = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM tabel_siswa WHERE nis='$nis'"));

if(isset($_POST['update'])){
    $nama = $_POST['nama'];
    $angkatan = $_POST['angkatan'];
    $kelas = $_POST['id_kelas'];
    
    // Reset Password jika dicentang
    if(isset($_POST['reset_pass'])){
        $pass_baru = md5($nis);
        $extra_query = ", password='$pass_baru'";
        $msg_pass = " & Password direset.";
    } else {
        $extra_query = "";
        $msg_pass = "";
    }

    $update = mysqli_query($koneksi, "UPDATE tabel_siswa SET 
                                      nama_lengkap='$nama', 
                                      angkatan='$angkatan', 
                                      id_kelas='$kelas' 
                                      $extra_query
                                      WHERE nis='$nis'");
    if($update){
        echo "<script>alert('Data Berhasil Diupdate$msg_pass'); window.location='admin_siswa.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Siswa</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="navbar"><h1>Edit Siswa</h1><a href="admin_siswa.php">Batal</a></div>
    <div class="container">
        <div class="form-container">
            <form method="POST">
                <label>NIS (Tidak bisa diubah):</label>
                <input type="text" value="<?= $data['nis'] ?>" disabled style="background:#eee;">
                
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" value="<?= $data['nama_lengkap'] ?>" required>

                <label>Angkatan:</label>
                <input type="number" name="angkatan" value="<?= $data['angkatan'] ?>" required>

                <label>Kelas:</label>
                <select name="id_kelas" required>
                    <?php
                    $k = mysqli_query($koneksi, "SELECT * FROM tabel_kelas");
                    while($r=mysqli_fetch_array($k)){
                        $selected = ($data['id_kelas'] == $r['id_kelas']) ? 'selected' : '';
                        echo "<option value='$r[id_kelas]' $selected>$r[nama_kelas]</option>"; 
                    }
                    ?>
                </select>
                
                <div style="margin: 20px 0; padding: 10px; background: #fff3cd; border: 1px solid #ffeeba;">
                    <input type="checkbox" name="reset_pass" style="width:auto; margin:0;"> 
                    <label style="display:inline;">Reset Password ke Default (Sama dengan NIS)?</label>
                </div>

                <button type="submit" name="update" class="btn">Update Data</button>
            </form>
        </div>
    </div>
</body>
</html>