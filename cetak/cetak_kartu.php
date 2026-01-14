<?php 
session_start();
include '../config/koneksi.php'; 

// CEK KEAMANAN
if($_SESSION['status'] != "login"){
    header("location:../auth/login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu Pelajar</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        /* CSS Khusus Print */
        @media print {
            body { background: white; margin: 0; }
            .navbar, .no-print, .btn { display: none !important; }
            .card-container { display: block; }
            .id-card { margin: 10px; display: inline-flex; border: 1px solid #ddd; page-break-inside: avoid; }
        }
        /* Biar background browser gak nabrak */
        body { background: #e0e0e0; }
    </style>
</head>
<body>

    <div class="no-print" style="text-align:center; padding:20px;">
        <h2>Pratinjau Cetak Kartu</h2>
        <button class="btn" onclick="window.print()" style="width:auto; background:#333;">🖨️ Cetak / Simpan PDF</button>
        <br><br>
        <?php if($_SESSION['role'] == 'Admin'){ ?>
            <a href="admin_siswa.php">Kembali ke Data Siswa</a>
        <?php } else { ?>
            <a href="siswa_dashboard.php">Kembali ke Dashboard</a>
        <?php } ?>
    </div>

    <div class="card-container">
        <?php
        // --- LOGIKA FILTER PINTAR ---
        $where = "";
        
        // 1. Jika SISWA yang login, PAKSA hanya tampilkan punya dia
        if($_SESSION['role'] == 'Siswa'){
            $nis_saya = $_SESSION['username'];
            $where = "WHERE tabel_siswa.nis = '$nis_saya'";
        }
        // 2. Jika ADMIN yang login, cek apakah dia minta print SATU siswa?
        else if(isset($_GET['nis'])){
            $nis_target = $_GET['nis'];
            $where = "WHERE tabel_siswa.nis = '$nis_target'";
        }
        // 3. Jika Admin tidak minta spesifik, berarti CETAK SEMUA (Where kosong)

        $query = mysqli_query($koneksi, "SELECT * FROM tabel_siswa 
                                         JOIN tabel_kelas ON tabel_siswa.id_kelas = tabel_kelas.id_kelas 
                                         $where");
        
        while($row = mysqli_fetch_array($query)){
        ?>
        
        <div class="id-card">
            <div class="header-bar"></div>
            <div class="photo-area"></div> 
            <div class="data-area">
                <div class="school-name">Sekolah Menengah Kejuruan</div>
                <h3 class="student-name"><?= $row['nama_lengkap'] ?></h3>
                <p class="student-nis">NIS: <?= $row['nis'] ?></p>
                <span class="student-class"><?= $row['nama_kelas'] ?></span>
                <div id="qrcode-<?= $row['nis'] ?>" class="qr-area"></div>
            </div>
        </div>

        <script type="text/javascript">
            new QRCode(document.getElementById("qrcode-<?= $row['nis'] ?>"), {
                text: "<?= $row['qr_code_string'] ?>",
                width: 60, height: 60,
                colorDark : "#000000", colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        </script>

        <?php } ?>
    </div>

</body>
</html>