<?php
include '../config/koneksi.php';
session_start();
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Siswa"){ header("location:../auth/login.php"); }
$nis = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head><title>Ganti Password</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
    <div class="navbar" style="background:#2980b9;">
        <h1>🔑 Keamanan Akun</h1>
        <a href="siswa_dashboard.php">Kembali</a>
    </div>

    <div class="container">
        <div class="form-container">
            <h3>Ganti Password Login</h3>
            
            <?php
            if(isset($_POST['ganti'])){
                $pass_lama = md5($_POST['pass_lama']);
                $pass_baru = md5($_POST['pass_baru']);
                
                // Cek Password Lama Benar Gak?
                $cek = mysqli_query($koneksi, "SELECT * FROM tabel_siswa WHERE nis='$nis' AND password='$pass_lama'");
                if(mysqli_num_rows($cek) > 0){
                    // Update Password Baru
                    mysqli_query($koneksi, "UPDATE tabel_siswa SET password='$pass_baru' WHERE nis='$nis'");
                    echo "<div style='color:green; padding:10px; border:1px solid green; margin-bottom:10px;'>✅ Password berhasil diubah!</div>";
                } else {
                    echo "<div style='color:red; padding:10px; border:1px solid red; margin-bottom:10px;'>❌ Password Lama Salah!</div>";
                }
            }
            ?>

            <form method="POST">
                <label>Password Lama:</label>
                <input type="password" name="pass_lama" required>
                
                <label>Password Baru:</label>
                <input type="password" name="pass_baru" required>
                
                <button type="submit" name="ganti" class="btn">Simpan Password Baru</button>
            </form>
        </div>
    </div>
</body>
</html>