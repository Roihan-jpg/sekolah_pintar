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
    <title>Input Pembayaran</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h1>💰 Kasir Sekolah</h1>
        <a href="index.php">Kembali ke Dashboard</a>
    </div>

    <div class="container">
        <div class="form-container">
            <h2 style="text-align:center; margin-top:0;">Input Transaksi</h2>
            
            <?php
            if (isset($_POST['bayar'])) {
                // ... (Copy paste logic PHP dari admin_bayar.php sebelumnya ke sini) ...
                // Hapus logic PHP di sini biar tidak panjang di chat, pakai yang lama saja
                // Pastikan logic ada di atas form
            }
            ?>

            <form method="POST">
                <label>Siswa:</label>
                <select name="nis" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php
                    $q = mysqli_query($koneksi, "SELECT * FROM tabel_siswa JOIN tabel_kelas ON tabel_siswa.id_kelas = tabel_kelas.id_kelas");
                    while($s = mysqli_fetch_array($q)){
                    ?>
                    <option value="<?= $s['nis'] ?>"><?= $s['nama_lengkap'] ?> - <?= $s['nama_kelas'] ?></option>
                    <?php } ?>
                </select>

                <label>Jenis Transaksi:</label>
                <select name="jenis">
                    <option value="SPP">SPP Bulanan</option>
                    <option value="Tabungan">Tabungan</option>
                    <option value="Uang Gedung">Uang Gedung</option>
                </select>

                <label>Nominal (Rp):</label>
                <input type="number" name="nominal" placeholder="0" required>

                <label>Keterangan:</label>
                <input type="text" name="keterangan" placeholder="Catatan...">

                <button type="submit" name="bayar" class="btn" style="width:100%">Simpan Data</button>
            </form>
        </div>
    </div>
</body>
</html>