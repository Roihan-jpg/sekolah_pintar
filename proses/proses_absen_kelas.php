<?php
session_start();
include '../config/koneksi.php';
header('Content-Type: application/json');

// Pastikan Guru Login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'Guru'){
    echo json_encode(["status" => "gagal", "nama_siswa" => "Error", "pesan_tambahan" => "Sesi Habis. Login lagi."]);
    exit;
}

$qr = $_POST['qr_code'];
$target_kelas = $_POST['id_kelas_target'];
$nip_guru = $_SESSION['username'];
$tgl = date('Y-m-d');
$waktu = date('H:i:s');

// 1. Cari Siswa berdasarkan QR
$cek = mysqli_query($koneksi, "SELECT * FROM tabel_siswa WHERE qr_code_string='$qr'");
$siswa = mysqli_fetch_array($cek);

// Jika QR tidak ada di database
if(!$siswa){
    echo json_encode([
        "status" => "gagal", 
        "nama_siswa" => "QR Tidak Valid", 
        "pesan_tambahan" => "Kode: $qr tidak terdaftar."
    ]);
    exit;
}

$nama_siswa = $siswa['nama_lengkap'];
$nis = $siswa['nis'];

// 2. VALIDASI KELAS
if($siswa['id_kelas'] != $target_kelas){
    echo json_encode([
        "status" => "gagal", 
        "nama_siswa" => $nama_siswa, 
        "pesan_tambahan" => "Siswa Salah Kelas! (Harusnya di kelas lain)"
    ]);
    exit;
}

// 3. Cek Double Absen
$cek_absen = mysqli_query($koneksi, "SELECT * FROM tabel_absensi WHERE nis='$nis' AND tanggal='$tgl'");
if(mysqli_num_rows($cek_absen) > 0){
    $data_absen = mysqli_fetch_array($cek_absen);
    echo json_encode([
        "status" => "warning", 
        "nama_siswa" => $nama_siswa, 
        "pesan_tambahan" => "Telah absen pukul " . $data_absen['waktu_scan']
    ]);
} else {
    // 4. Simpan Absen
    $simpan = mysqli_query($koneksi, "INSERT INTO tabel_absensi (nis, nip_scanner, tanggal, waktu_scan, status_kehadiran)
                                      VALUES ('$nis', '$nip_guru', '$tgl', '$waktu', 'Hadir')");
    if($simpan){
        echo json_encode([
            "status" => "sukses", 
            "nama_siswa" => $nama_siswa, 
            "pesan_tambahan" => "NIS: $nis <br> Jam: $waktu"
        ]);
    } else {
        echo json_encode([
            "status" => "gagal", 
            "nama_siswa" => "Error Database", 
            "pesan_tambahan" => mysqli_error($koneksi)
        ]);
    }
}
?>