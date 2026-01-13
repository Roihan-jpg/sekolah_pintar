<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json'); // Return format JSON

// Ambil data dari AJAX
$qr_code = $_POST['qr_code'];
$id_kelas_target = $_POST['id_kelas_target'];

$tanggal_hari_ini = date('Y-m-d');
$waktu_sekarang = date('H:i:s');
$nip_guru = $_SESSION['username']; // Guru yang lagi scan

// 1. Cek Data Siswa Berdasarkan QR
$cek_siswa = mysqli_query($koneksi, "SELECT * FROM tabel_siswa WHERE qr_code_string = '$qr_code'");
$data_siswa = mysqli_fetch_array($cek_siswa);

if (!$data_siswa) {
    echo json_encode(["status" => "gagal", "pesan" => "❌ QR Code Tidak Dikenal!"]);
    exit();
}

$nis = $data_siswa['nis'];
$nama = $data_siswa['nama_lengkap'];
$kelas_siswa = $data_siswa['id_kelas'];

// 2. VALIDASI KELAS (PENTING!)
// Cek apakah ID Kelas siswa SAMA dengan ID Kelas jadwal guru?
if ($kelas_siswa != $id_kelas_target) {
    // Cari nama kelas aslinya dia biar pesannya jelas
    $q_kelas_asal = mysqli_fetch_array(mysqli_query($koneksi, "SELECT nama_kelas FROM tabel_kelas WHERE id_kelas='$kelas_siswa'"));
    $nama_kelas_asal = $q_kelas_asal['nama_kelas'];
    
    echo json_encode([
        "status" => "gagal", 
        "pesan" => "⛔ SALAH KELAS!<br><small>$nama adalah siswa kelas $nama_kelas_asal</small>"
    ]);
    exit();
}

// 3. Cek apakah sudah absen hari ini?
$cek_absen = mysqli_query($koneksi, "SELECT * FROM tabel_absensi WHERE nis = '$nis' AND tanggal = '$tanggal_hari_ini'");

if (mysqli_num_rows($cek_absen) > 0) {
    $data_absen = mysqli_fetch_array($cek_absen);
    $ket = $data_absen['status_kehadiran'];
    
    // Kalau sudah absen, kita kasih info aja (Warning)
    echo json_encode([
        "status" => "warning", 
        "pesan" => "⚠️ SUDAH ABSEN.<br><small>$nama sudah tercatat ($ket) hari ini.</small>"
    ]);
} else {
    // 4. Input Absen Baru
    // Status default 'Hadir' karena discan langsung oleh guru di kelas
    $status = "Hadir"; 
    
    // Simpan NIP Guru sebagai nip_scanner (Bukti guru ini yang memvalidasi)
    $insert = mysqli_query($koneksi, "INSERT INTO tabel_absensi (nis, nip_scanner, tanggal, waktu_scan, status_kehadiran) 
              VALUES ('$nis', '$nip_guru', '$tanggal_hari_ini', '$waktu_sekarang', '$status')");
    
    if ($insert) {
        echo json_encode([
            "status" => "sukses", 
            "pesan" => "✅ BERHASIL!<br>$nama ($status)"
        ]);
    } else {
        echo json_encode(["status" => "gagal", "pesan" => "Error Database."]);
    }
}
?>