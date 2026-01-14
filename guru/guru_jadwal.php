<?php
include 'koneksi.php';
session_start();

if($_SESSION['status'] != "login" || $_SESSION['role'] != "Guru"){ 
    header("location:login.php"); 
}

$nip = $_SESSION['username'];
$nama_guru = $_SESSION['nama'];

// --- PENGATURAN BATAS JAM PERTAMA ---
// Pelajaran yang mulai SEBELUM jam ini dianggap Jam Pertama (Wajib Absen).
// Pelajaran SETELAH jam ini dianggap Jam Lanjutan (Tidak perlu absen lagi).
$batas_jam_absen = "08:30:00"; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Mengajar</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .jadwal-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .hari-card { 
            background: white; width: 350px; border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 20px;
        }
        .hari-senin { border-top: 5px solid #e74c3c; } 
        .hari-selasa { border-top: 5px solid #e67e22; } 
        .hari-rabu { border-top: 5px solid #f1c40f; } 
        .hari-kamis { border-top: 5px solid #2ecc71; } 
        .hari-jumat { border-top: 5px solid #1abc9c; } 
        
        .hari-header { background: #f8f9fa; padding: 10px; font-weight: bold; text-align: center; border-bottom: 1px solid #eee; }
        .mapel-item { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        
        /* Tombol Scan */
        .btn-scan {
            display: block; width: 100%; text-align: center; background: #2980b9; 
            color: white; padding: 10px; border-radius: 5px; text-decoration: none; margin-top: 10px;
        }
        .btn-scan:hover { background: #3498db; }

        /* Label KBM Berlangsung (Pengganti Tombol) */
        .label-kbm {
            display: block; width: 100%; text-align: center; background: #eee; border: 1px dashed #ccc;
            color: #777; padding: 8px; border-radius: 5px; margin-top: 10px; font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="navbar" style="background:#27ae60;">
        <h1>📅 Jadwal Mengajar</h1>
        <a href="guru_dashboard.php">Kembali</a>
    </div>

    <div class="container">
        <div style="text-align:center; margin-bottom:30px;">
            <h2>Halo, <?= $nama_guru ?></h2>
            <p>Tombol absensi hanya muncul pada jadwal Jam Pertama (sebelum <?= date('H:i', strtotime($batas_jam_absen)) ?>).</p>
        </div>

        <div class="jadwal-container">
            <?php
            $daftar_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            $hari_ini_inggris = date('l');
            $trans = ['Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat'];
            $hari_ini = isset($trans[$hari_ini_inggris]) ? $trans[$hari_ini_inggris] : '';

            foreach($daftar_hari as $hari){
                $query = "SELECT j.*, k.nama_kelas 
                          FROM tabel_jadwal j 
                          JOIN tabel_kelas k ON j.id_kelas = k.id_kelas 
                          WHERE j.nip_guru = '$nip' AND j.hari = '$hari' 
                          ORDER BY j.jam_mulai ASC";
                $result = mysqli_query($koneksi, $query);

                if(mysqli_num_rows($result) > 0){
                    $style_active = ($hari == $hari_ini) ? "border: 2px solid #27ae60; transform:scale(1.02);" : "";
            ?>
            <div class="hari-card hari-<?= strtolower($hari) ?>" style="<?= $style_active ?>">
                <div class="hari-header">
                    <?= $hari ?> <?= ($hari == $hari_ini) ? "<span style='color:red; font-size:12px;'>(Hari Ini)</span>" : "" ?>
                </div>
                
                <?php while($d = mysqli_fetch_array($result)){ ?>
                <div class="mapel-item">
                    <b>⏰ <?= date('H:i', strtotime($d['jam_mulai'])) ?> - <?= date('H:i', strtotime($d['jam_selesai'])) ?></b><br>
                    <span style="font-size:18px; font-weight:bold; color:#2c3e50;"><?= $d['mata_pelajaran'] ?></span><br>
                    <span style="background:#27ae60; color:white; padding:2px 8px; border-radius:10px; font-size:12px;">Kelas <?= $d['nama_kelas'] ?></span>
                    
                    <?php 
                    // Cek apakah ini jam pertama?
                    if($d['jam_mulai'] < $batas_jam_absen) { 
                    ?>
                        <a href="guru_scan.php?kelas=<?= $d['id_kelas'] ?>&nama_kelas=<?= $d['nama_kelas'] ?>" class="btn-scan">
                            📷 Absen Masuk (Jam 1)
                        </a>
                    <?php } else { ?>
                        <div class="label-kbm">
                            ✅ KBM Berlangsung
                        </div>
                    <?php } ?>

                </div>
                <?php } ?>
            </div>
            <?php } } ?>
        </div>
    </div>
</body>
</html>