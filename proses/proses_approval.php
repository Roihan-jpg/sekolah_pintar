<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php");
}
?>
<?php
include 'koneksi.php';

$id_izin = $_GET['id'];
$aksi = $_GET['aksi'];

if ($aksi == 'tolak') {
    mysqli_query($koneksi, "UPDATE tabel_perizinan SET status_approval='Ditolak' WHERE id_izin='$id_izin'");
    header("Location: admin_approval.php");
} 
else if ($aksi == 'terima') {
    $nis = $_GET['nis'];
    $tgl = $_GET['tgl'];
    $jenis = $_GET['jns'];

    // 1. Cek Kuota Semester Ini (Hanya hitung yang sudah DISETUJUI)
    $bulan = date('m', strtotime($tgl));
    $tahun = date('Y', strtotime($tgl));
    
    if ($bulan >= 7) { $start="$tahun-07-01"; $end="$tahun-12-31"; } 
    else { $start="$tahun-01-01"; $end="$tahun-06-30"; }

    $cek_kuota = mysqli_query($koneksi, "SELECT * FROM tabel_perizinan 
                                         WHERE nis='$nis' AND status_approval='Disetujui' 
                                         AND tanggal_izin BETWEEN '$start' AND '$end'");
    
    if (mysqli_num_rows($cek_kuota) >= 3) {
        echo "<script>alert('GAGAL! Kuota siswa ini sudah habis (3x). Izin otomatis Ditolak.'); window.location='admin_approval.php';</script>";
        // Opsional: Update jadi ditolak karena kuota habis
        mysqli_query($koneksi, "UPDATE tabel_perizinan SET status_approval='Ditolak' WHERE id_izin='$id_izin'");
    } else {
        // 2. Jika Kuota Aman -> Update Status & Masuk ke Absen
        mysqli_query($koneksi, "UPDATE tabel_perizinan SET status_approval='Disetujui' WHERE id_izin='$id_izin'");
        
        // Masukkan ke tabel absensi agar rekap harian aman
        mysqli_query($koneksi, "INSERT INTO tabel_absensi (nis, nip_scanner, tanggal, waktu_scan, status_kehadiran) 
                                VALUES ('$nis', '-', '$tgl', '00:00:00', '$jenis')");
        
        header("Location: admin_approval.php");
    }
}
?>