<?php
include '../config/koneksi.php';
session_start();

// Cek Login Siswa
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Siswa"){ header("location:../auth/login.php"); }

$nis = $_SESSION['username'];

// 1. Cari Tahu Siswa ini Kelas Apa?
$siswa = mysqli_fetch_array(mysqli_query($koneksi, "SELECT id_kelas, nama_lengkap FROM tabel_siswa WHERE nis='$nis'"));
$id_kelas = $siswa['id_kelas'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Pelajaran Saya</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Style Tabel Jadwal yang Lebih Cantik */
        .jadwal-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .hari-card { 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            width: 300px; 
            overflow: hidden; 
            border-top: 5px solid #2980b9; /* Default Biru */
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
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
        }
        .mapel-item:last-child { border-bottom: none; }
        .jam-badge {
            background: #eee;
            color: #555;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-right: 10px;
            font-weight: bold;
            min-width: 80px;
            text-align: center;
        }
        .mapel-info h4 { margin: 0; font-size: 14px; color: #333; }
        .mapel-info p { margin: 3px 0 0 0; font-size: 12px; color: #777; }
        
        /* Warna Warni per Hari */
        .hari-senin { border-top-color: #e74c3c; } /* Merah */
        .hari-selasa { border-top-color: #e67e22; } /* Orange */
        .hari-rabu { border-top-color: #f1c40f; } /* Kuning */
        .hari-kamis { border-top-color: #2ecc71; } /* Hijau */
        .hari-jumat { border-top-color: #1abc9c; } /* Tosca */
        .hari-sabtu { border-top-color: #9b59b6; } /* Ungu */
    </style>
</head>
<body>

    <div class="navbar" style="background:#2980b9;">
        <h1>📅 Jadwal Pelajaran</h1>
        <a href="siswa_dashboard.php">Kembali</a>
    </div>

    <div class="container">
        
        <div style="text-align:center; margin-bottom:30px;">
            <h2>Halo, <?= $siswa['nama_lengkap'] ?></h2>
            <p>Berikut adalah jadwal pelajaran untuk kelasmu.</p>
        </div>

        <div class="jadwal-container">
            <?php
            // Array Hari untuk Looping
            $daftar_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            
            foreach($daftar_hari as $hari){
                // Cek apakah ada jadwal di hari ini?
                $query = "SELECT j.*, g.nama_guru 
                          FROM tabel_jadwal j 
                          JOIN tabel_guru g ON j.nip_guru = g.nip 
                          WHERE j.id_kelas = '$id_kelas' AND j.hari = '$hari' 
                          ORDER BY j.jam_mulai ASC";
                $result = mysqli_query($koneksi, $query);
                
                // Hanya tampilkan kartu hari jika ada isinya
                if(mysqli_num_rows($result) > 0){
                    $class_warna = "hari-" . strtolower($hari);
            ?>
            
            <div class="hari-card <?= $class_warna ?>">
                <div class="hari-header"><?= $hari ?></div>
                <ul class="mapel-list">
                    <?php while($d = mysqli_fetch_array($result)){ ?>
                    <li class="mapel-item">
                        <div class="jam-badge">
                            <?= date('H:i', strtotime($d['jam_mulai'])) ?> - <?= date('H:i', strtotime($d['jam_selesai'])) ?>
                        </div>
                        <div class="mapel-info">
                            <h4><?= $d['mata_pelajaran'] ?></h4>
                            <?php if($d['nip_guru'] != '99999999'){ ?>
                                <p>👨‍🏫 <?= $d['nama_guru'] ?></p>
                            <?php } else { ?>
                                <p style="color:#27ae60; font-style:italic;">Istirahat</p>
                            <?php } ?>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>

            <?php 
                } // End If
            } // End Foreach
            ?>
        </div>

        <?php if(mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM tabel_jadwal WHERE id_kelas='$id_kelas'")) == 0){ ?>
            <div style="text-align:center; padding:50px; color:#777;">
                <h3>📭 Belum ada jadwal yang diatur untuk kelas ini.</h3>
            </div>
        <?php } ?>

    </div>
</body>
</html>