<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['simpan'])){
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    
    // Password default = NIP
    $password = md5($nip);

    // Cek apakah NIP sudah ada?
    $cek = mysqli_query($koneksi, "SELECT * FROM tabel_guru WHERE nip='$nip'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Gagal! NIP sudah terdaftar.');</script>";
    } else {
        $insert = mysqli_query($koneksi, "INSERT INTO tabel_guru (nip, nama_guru, password) VALUES ('$nip', '$nama', '$password')");
        if($insert){
            echo "<script>alert('Berhasil menambah Guru!'); window.location='admin_guru.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Tambah Guru</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
    <div class="navbar"><h1>+ Tambah Guru</h1><a href="admin_guru.php">Batal</a></div>
    <div class="container">
        <div class="form-container">
            <p style="text-align:center; background:#e8f8f5; padding:10px; border-radius:5px; color:#16a085;">
                ℹ️ Password default guru baru adalah <b>NIP</b> mereka sendiri.
            </p>
            <form method="POST">
                <label>NIP (Nomor Induk Pegawai):</label>
                <input type="text" name="nip" required placeholder="Contoh: 19850101" autocomplete="off">
                
                <label>Nama Lengkap (Beserta Gelar):</label>
                <input type="text" name="nama" required placeholder="Contoh: Budi Santoso, S.Pd.">

                <button type="submit" name="simpan" class="btn">Simpan Data</button>
            </form>
        </div>
    </div>
</body>
</html>