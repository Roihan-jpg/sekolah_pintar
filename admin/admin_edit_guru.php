<?php
session_start();
include '../config/koneksi.php';
$nip = $_GET['nip'];
$data = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM tabel_guru WHERE nip='$nip'"));

if(isset($_POST['update'])){
    $nama = $_POST['nama'];
    
    // Logika Reset Password
    if(isset($_POST['reset_pass'])){
        $pass_baru = md5($nip); // Reset jadi NIP
        $extra_query = ", password='$pass_baru'";
        $msg_pass = " & Password berhasil direset ke NIP.";
    } else {
        $extra_query = "";
        $msg_pass = "";
    }

    $update = mysqli_query($koneksi, "UPDATE tabel_guru SET nama_guru='$nama' $extra_query WHERE nip='$nip'");
    
    if($update){
        echo "<script>alert('Data Guru Diupdate$msg_pass'); window.location='admin_guru.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Guru</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
    <div class="navbar"><h1>Edit Data Guru</h1><a href="admin_guru.php">Batal</a></div>
    <div class="container">
        <div class="form-container">
            <form method="POST">
                <label>NIP (Tidak bisa diubah):</label>
                <input type="text" value="<?= $data['nip'] ?>" disabled style="background:#eee;">
                
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" value="<?= $data['nama_guru'] ?>" required>
                
                <div style="margin: 20px 0; padding: 15px; background: #fff3cd; border: 1px solid #ffeeba; border-radius:5px;">
                    <h4 style="margin-top:0;">🔐 Lupa Password?</h4>
                    <input type="checkbox" name="reset_pass" id="reset" style="width:auto; margin:0;"> 
                    <label for="reset" style="display:inline; font-weight:normal;">Centang ini untuk mereset password guru menjadi NIP (<b><?= $data['nip'] ?></b>)</label>
                </div>

                <button type="submit" name="update" class="btn">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</body>
</html>