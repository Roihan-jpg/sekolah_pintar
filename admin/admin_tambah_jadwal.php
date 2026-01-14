<?php
session_start();
include '../config/koneksi.php';

if(isset($_POST['simpan'])){
    $hari = $_POST['hari'];
    $mulai = $_POST['jam_mulai'];
    $selesai = $_POST['jam_selesai'];
    $mapel = $_POST['mapel'];
    $kelas = $_POST['id_kelas'];
    $guru = $_POST['nip_guru'];

    // Simpan
    $q = mysqli_query($koneksi, "INSERT INTO tabel_jadwal (hari, jam_mulai, jam_selesai, mata_pelajaran, id_kelas, nip_guru)
                                 VALUES ('$hari', '$mulai', '$selesai', '$mapel', '$kelas', '$guru')");
    if($q){
        echo "<script>alert('Jadwal Berhasil Disimpan!'); window.location='admin_jadwal.php?kelas=$kelas';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Tambah Jadwal</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
    <div class="navbar"><h1>+ Tambah Jadwal</h1><a href="admin_jadwal.php">Batal</a></div>
    <div class="container">
        <div class="form-container">
            <form method="POST">
                
                <label>Kelas:</label>
                <select name="id_kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php
                    $k = mysqli_query($koneksi, "SELECT * FROM tabel_kelas ORDER BY nama_kelas ASC");
                    while($r=mysqli_fetch_array($k)){ echo "<option value='$r[id_kelas]'>$r[nama_kelas]</option>"; }
                    ?>
                </select>

                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Hari:</label>
                        <select name="hari" required>
                            <option>Senin</option><option>Selasa</option><option>Rabu</option>
                            <option>Kamis</option><option>Jumat</option><option>Sabtu</option>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label>Jam Mulai:</label>
                        <input type="time" name="jam_mulai" required>
                    </div>
                    <div style="flex:1;">
                        <label>Jam Selesai:</label>
                        <input type="time" name="jam_selesai" required>
                    </div>
                </div>

                <label>Mata Pelajaran:</label>
                <input type="text" name="mapel" placeholder="Cth: Matematika Wajib" required>

                <label>Guru Pengajar:</label>
                <select name="nip_guru" required>
                    <option value="">-- Pilih Guru --</option>
                    <?php
                    $g = mysqli_query($koneksi, "SELECT * FROM tabel_guru ORDER BY nama_guru ASC");
                    while($r=mysqli_fetch_array($g)){ echo "<option value='$r[nip]'>$r[nama_guru]</option>"; }
                    ?>
                </select>

                <button type="submit" name="simpan" class="btn">Simpan Jadwal</button>
            </form>
        </div>
    </div>
</body>
</html>