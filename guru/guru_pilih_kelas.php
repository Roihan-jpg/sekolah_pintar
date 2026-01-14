<?php
session_start();
include 'koneksi.php';

// Cek Login Guru
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Guru"){
    header("location:login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pilih Kelas Absensi</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .box-pilih { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; border-top: 5px solid #27ae60; }
        select { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        .btn-lanjut { background: #27ae60; color: white; width: 100%; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .btn-lanjut:hover { background: #219150; }
        .btn-back { display: block; margin-top: 15px; color: #777; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

    <div class="box-pilih">
        <h2>📷 Mulai Absensi</h2>
        <p>Silakan pilih kelas yang akan Anda ajar saat ini.</p>
        
        <form action="guru_scan.php" method="GET">
            <label style="font-weight:bold; float:left;">Pilih Kelas:</label>
            <select name="kelas" required>
                <option value="">-- Pilih Kelas --</option>
                <?php
                // Ambil semua data kelas
                $q = mysqli_query($koneksi, "SELECT * FROM tabel_kelas ORDER BY nama_kelas ASC");
                while($k = mysqli_fetch_array($q)){
                    // Kita kirim ID Kelas sebagai value
                    echo "<option value='".$k['id_kelas']."'>Kelas ".$k['nama_kelas']."</option>";
                }
                ?>
            </select>
            
            <button type="submit" class="btn-lanjut">Lanjut ke Scanner 👉</button>
        </form>

        <a href="guru_dashboard.php" class="btn-back">Kembali ke Dashboard</a>
    </div>

</body>
</html>