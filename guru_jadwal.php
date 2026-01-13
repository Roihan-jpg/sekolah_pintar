<?php
include 'koneksi.php';
session_start();

// Cek Login Guru (Sesuai logic di guru_dashboard.php)
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Guru"){ 
    header("location:login.php"); 
}

// Ambil NIP dari session (di login.php username diisi nip untuk guru)
$nip = $_SESSION['username'];
$nama_guru = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Mengajar Saya</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Mengambil style card jadwal yang sama dengan siswa_jadwal.php agar konsisten */
        .jadwal-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .hari-card { 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            width: 350px; /* Sedikit lebih lebar buat tombol absen */
            overflow: hidden; 
            border-top: 5px solid #27ae60; /* Default Hijau Guru */
            margin-bottom: 20px;
        }
        .hari-header {
            background: #f8f9fa;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #eee;
            color: #333;
            text-transform: uppercase;
        }
        .mapel-list { padding: 0; margin: 0; list-style: none; }
        .mapel-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            flex-direction: column; /* Biar tombol ada di bawah info */
            gap: 10px;
        }
        .mapel-item:last-child { border-bottom: none; }
        
        .jam-badge {
            background: #eef;
            color: #333;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .mapel-info h4 { margin: 5px 0 2px 0; font-size: 16px; color: #2c3e50; }
        .kelas-badge { 
            background: #27ae60; color: white; 
            padding: 2px 8px; border-radius: 10px; 
            font-size: 11px; text-transform: uppercase;
        }

        /* Warna Warni Header Hari (Opsional, biar cantik kayak siswa) */
        .hari-senin { border-top-color: #e74c3c; } 
        .hari-selasa { border-top-color: #e67e22; } 
        .hari-rabu { border-top-color: #f1c40f; } 
        .hari-kamis { border-top-color: #2ecc71; } 
        .hari-jumat { border-top-color: #1abc9c; } 
        .hari-sabtu { border-top-color: #9b59b6; }
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
            <p>Silakan pilih kelas untuk memulai absensi.</p>
        </div>

        <div class="jadwal-container">
            <?php
            // Array Hari
            $daftar_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            
            // Cek hari ini dalam bahasa Indonesia untuk highlight (Opsional)
            $hari_inggris = date('l');
            $trans = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
            $hari_ini = $trans[$hari_inggris];

            foreach($daftar_hari as $hari){
                // Query: Ambil jadwal GURU ini, JOIN dengan tabel kelas biar muncul nama kelasnya
                // Sesuai relasi di aplikasi_pintar.sql
                $query = "SELECT j.*, k.nama_kelas 
                          FROM tabel_jadwal j 
                          JOIN tabel_kelas k ON j.id_kelas = k.id_kelas 
                          WHERE j.nip_guru = '$nip' AND j.hari = '$hari' 
                          ORDER BY j.jam_mulai ASC";
                
                $result = mysqli_query($koneksi, $query);
                
                // Hanya tampilkan kartu jika ada jadwal
                if(mysqli_num_rows($result) > 0){
                    $class_warna = "hari-" . strtolower($hari);
                    
                    // Efek border tebal jika hari ini
                    $style_tambahan = ($hari == $hari_ini) ? "border: 2px solid #27ae60; transform:scale(1.02);" : "";
            ?>
            
            <div class="hari-card <?= $class_warna ?>" style="<?= $style_tambahan ?>">
                <div class="hari-header">
                    <?= $hari ?> <?= ($hari == $hari_ini) ? "<span style='font-size:10px; background:red; color:white; padding:2px 5px; border-radius:3px;'>HARI INI</span>" : "" ?>
                </div>
                <ul class="mapel-list">
                    <?php while($d = mysqli_fetch_array($result)){ ?>
                    <li class="mapel-item">
                        
                        <div style="display:flex; justify-content:space-between; align-items:start;">
                            <div>
                                <div class="jam-badge">
                                    ⏰ <?= date('H:i', strtotime($d['jam_mulai'])) ?> - <?= date('H:i', strtotime($d['jam_selesai'])) ?>
                                </div>
                                <div class="mapel-info">
                                    <h4><?= $d['mata_pelajaran'] ?></h4>
                                    <span class="kelas-badge"><?= $d['nama_kelas'] ?></span>
                                </div>
                            </div>
                        </div>

                        <a href="guru_scan.php?kelas=<?= $d['id_kelas'] ?>&nama_kelas=<?= $d['nama_kelas'] ?>" class="btn-small" style="background:#2980b9; text-align:center; display:block; text-decoration:none; color:white; padding:8px; border-radius:4px;">
                            📷 Buka Scanner Kelas
                        </a>

                    </li>
                    <?php } ?>
                </ul>
            </div>

            <?php 
                } // End If Rows > 0
            } // End Foreach
            ?>
        </div>
        
        <?php if(mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tabel_jadwal WHERE nip_guru='$nip'")) == 0){ ?>
            <div style="text-align:center; padding:50px; color:#777;">
                <h3>📭 Anda belum memiliki jadwal mengajar. Hubungi Admin.</h3>
            </div>
        <?php } ?>

    </div>
</body>
</html>