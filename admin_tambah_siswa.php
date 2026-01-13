<?php
session_start();
include 'koneksi.php';

if(isset($_POST['simpan'])){
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $angkatan = $_POST['angkatan'];
    $kelas = $_POST['id_kelas'];
    
    // 1. Generate QR Code Unik (Misal format: SCH-[NIS])
    $qr_string = "SCH-" . $nis;
    
    // 2. Generate Password Default (NIS di-MD5)
    $password = md5($nis);

    // 3. Cek apakah NIS sudah ada?
    $cek = mysqli_query($koneksi, "SELECT * FROM tabel_siswa WHERE nis='$nis'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Gagal! NIS sudah terdaftar.');</script>";
    } else {
        $insert = mysqli_query($koneksi, "INSERT INTO tabel_siswa (nis, nama_lengkap, angkatan, id_kelas, qr_code_string, password) 
                                          VALUES ('$nis', '$nama', '$angkatan', '$kelas', '$qr_string', '$password')");
        if($insert){
            echo "<script>alert('Berhasil menambah siswa!'); window.location='admin_siswa.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Tambah Siswa</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="navbar"><h1>+ Tambah Siswa</h1><a href="admin_siswa.php">Batal</a></div>
    <div class="container">
        <div class="form-container">
            <form method="POST">
                <label>NIS (Nomor Induk Siswa):</label>
                <input type="text" name="nis" required placeholder="Cth: 1234567890">
                
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" required placeholder="Nama Siswa">

                <label>Angkatan (Tahun Masuk):</label>
                <input type="number" name="angkatan" required value="<?= date('Y') ?>">

                <label>Kelas:</label>
                <select name="id_kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php
                    $k = mysqli_query($koneksi, "SELECT * FROM tabel_kelas");
                    while($r=mysqli_fetch_array($k)){ echo "<option value='$r[id_kelas]'>$r[nama_kelas]</option>"; }
                    ?>
                </select>

                <button type="submit" name="simpan" class="btn">Simpan Data</button>
            </form>
        </div>
    </div>
</body>
</html>