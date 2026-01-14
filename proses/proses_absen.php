<?php
// 1. Paling atas wajib session_start agar bisa baca data login
session_start();
include 'koneksi.php';

// 2. Cek apakah user sudah login? Jika belum, tendang.
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    echo "Gagal: Anda belum login.";
    exit;
}

// 3. AMBIL VARIABEL DARI FORM & SESSION (INI BAGIAN PENTINGNYA)
// Ambil QR dari hasil scan (dikirim via AJAX/Form)
$qr_code = isset($_POST['qr_code']) ? $_POST['qr_code'] : '';

// Ambil NIP Guru dari Session (Otomatis mendeteksi siapa yang login)
if($_SESSION['role'] == 'Guru'){
    $nip_guru = $_SESSION['username']; // Username guru isinya NIP
} else {
    // Jika Admin yang scan, kita kosongkan atau pakai NIP default sistem (jika ada)
    // Karena tabel_absensi mewajibkan relasi ke tabel_guru
    // Opsi aman: Set NULL (jika database mengizinkan) atau NIP '99999999' (Sistem)
    $nip_guru = '99999999'; 
}

$tanggal_hari_ini = date('Y-m-d');
$waktu_sekarang = date('H:i:s');

// 4. Validasi Data Kosong
if(empty($qr_code)){
    echo "Error: QR Code tidak terbaca.";
    exit;
}

// 5. Cek apakah QR Code Siswa Valid?
$cek_siswa = mysqli_query($koneksi, "SELECT * FROM tabel_siswa WHERE qr_code_string = '$qr_code'");
$data_siswa = mysqli_fetch_array($cek_siswa);

if (!$data_siswa) {
    echo "<span style='color:red'>QR Code Tidak Dikenal!</span>";
    exit();
}

$nis = $data_siswa['nis'];
$nama = $data_siswa['nama_lengkap'];

// 6. Cek apakah sudah absen hari ini?
$cek_absen = mysqli_query($koneksi, "SELECT * FROM tabel_absensi WHERE nis = '$nis' AND tanggal = '$tanggal_hari_ini'");

if (mysqli_num_rows($cek_absen) > 0) {
    echo "<span style='color:orange'>Siswa $nama SUDAH Absen hari ini.</span>";
} else {
    // 7. Tentukan status (Terlambat/Hadir)
    $status = ($waktu_sekarang > "07:15:00") ? "Terlambat" : "Hadir";
    
    // 8. INSERT KE DATABASE (Fixed)
    // Perhatikan variabel $nip_guru sekarang sudah terdefinisi di atas
    $insert = mysqli_query($koneksi, "INSERT INTO tabel_absensi (nis, nip_scanner, tanggal, waktu_scan, status_kehadiran) 
              VALUES ('$nis', '$nip_guru', '$tanggal_hari_ini', '$waktu_sekarang', '$status')");
    
    if ($insert) {
        echo "<span style='color:lightgreen'>Berhasil: $nama ($status)</span>";
    } else {
        // Tampilkan error asli database jika masih gagal (untuk debugging)
        echo "Error Database: " . mysqli_error($koneksi);
    }
}
?>