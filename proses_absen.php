<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php");
}
?>
<?php
include 'koneksi.php';

// Ambil data QR
$qr_code = $_POST['qr_code'];
$tanggal_hari_ini = date('Y-m-d');
$waktu_sekarang = date('H:i:s');

// 1. Cek apakah QR Code Siswa Valid?
$cek_siswa = mysqli_query($koneksi, "SELECT * FROM tabel_siswa WHERE qr_code_string = '$qr_code'");
$data_siswa = mysqli_fetch_array($cek_siswa);

if (!$data_siswa) {
    echo "<span style='color:red'>QR Code Tidak Dikenal!</span>";
    exit();
}

$nis = $data_siswa['nis'];
$nama = $data_siswa['nama_lengkap'];

// 2. Cek apakah sudah absen hari ini?
$cek_absen = mysqli_query($koneksi, "SELECT * FROM tabel_absensi WHERE nis = '$nis' AND tanggal = '$tanggal_hari_ini'");

if (mysqli_num_rows($cek_absen) > 0) {
    echo "<span style='color:orange'>Siswa $nama SUDAH Absen hari ini.</span>";
} else {
    // 3. Tentukan status (Misal: Lewat jam 07:15 dianggap Terlambat)
    $status = ($waktu_sekarang > "07:15:00") ? "Terlambat" : "Hadir";
    
    // NIP Scanner (Guru) kita hardcode dulu '19800101' sesuai data dummy
    $insert = mysqli_query($koneksi, "INSERT INTO tabel_absensi (nis, nip_scanner, tanggal, waktu_scan, status_kehadiran) 
              VALUES ('$nis', '19800101', '$tanggal_hari_ini', '$waktu_sekarang', '$status')");
    
    if ($insert) {
        echo "<span style='color:lightgreen'>Berhasil: $nama ($status)</span>";
    } else {
        echo "Error Database";
    }
}
?>