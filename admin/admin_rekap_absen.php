<?php
session_start();
include '../config/koneksi.php';

// Cek Security
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Admin"){ header("location:../auth/login.php"); }

// --- LOGIKA FILTER ---
$bulan_pilih = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilih = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$kelas_pilih = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';

// Nama Bulan
$nama_bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
$judul_bulan = $nama_bulan[(int)$bulan_pilih];

// Hitung Hari
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan_pilih, $tahun_pilih);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* --- CSS KHUSUS REKAP (OVERRIDE STYLE GLOBAL) --- */
        .container { max-width: 98%; } /* Lebarkan container agar tabel muat */
        
        .table-rekap { 
            font-size: 11px; 
            width: 100%; 
            border-collapse: collapse; 
            background: white; /* Pastikan background putih */
        }
        
        /* Paksa Warna Teks Header & Sel jadi Hitam (Fix Masalah Font Putih) */
        .table-rekap th, .table-rekap td { 
            border: 1px solid #bbb; 
            padding: 4px; 
            text-align: center; 
            color: #333 !important; /* PENTING: Override warna teks */
        }

        .table-rekap th { 
            background: #eee !important; /* Header Abu-abu */
            font-weight: bold;
        }

        /* Kolom Nama & Kelas rata kiri */
        .col-nama { text-align: left !important; min-width: 150px; }
        .col-kelas { text-align: center !important; width: 60px; }
        
        /* Warna Sel Status (Background Terang, Teks Gelap) */
        .ket-H { background-color: #dff0d8 !important; color: #27ae60 !important; font-weight: bold; } 
        .ket-S { background-color: #fcf8e3 !important; color: #f39c12 !important; font-weight: bold; }
        .ket-I { background-color: #d9edf7 !important; color: #2980b9 !important; font-weight: bold; }
        .ket-A { background-color: #f2dede !important; color: #c0392b !important; font-weight: bold; }
        
        /* Mode Cetak */
        @media print {
            .navbar, .filter-box, .btn { display: none; }
            body, .container { background: white; margin: 0; padding: 0; width: 100%; max-width: 100%; }
            .table-rekap { border: 1px solid #000; }
            .table-rekap th, .table-rekap td { border: 1px solid #000; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>📊 Rekap Absensi</h1>
        <a href="dashboard.php">Dashboard</a>
    </div>

    <div class="container">
        
        <div class="filter-box" style="background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #ddd; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
            
            <form method="GET" style="display:flex; gap:10px; align-items:center;">
                <select name="id_kelas" style="padding:6px; border-radius:4px; border:1px solid #ccc;">
                    <option value="">-- Semua Kelas --</option>
                    <?php
                    $k = mysqli_query($koneksi, "SELECT * FROM tabel_kelas ORDER BY nama_kelas ASC");
                    while($r = mysqli_fetch_array($k)){
                        $sel = ($kelas_pilih == $r['id_kelas']) ? 'selected' : '';
                        echo "<option value='$r[id_kelas]' $sel>$r[nama_kelas]</option>";
                    }
                    ?>
                </select>

                <select name="bulan" style="padding:6px; border-radius:4px; border:1px solid #ccc;">
                    <?php
                    for($i=1; $i<=12; $i++){
                        $sel = ($i == $bulan_pilih) ? 'selected' : '';
                        echo "<option value='$i' $sel>".$nama_bulan[$i]."</option>";
                    }
                    ?>
                </select>

                <input type="number" name="tahun" value="<?= $tahun_pilih ?>" style="width:70px; padding:5px; border-radius:4px; border:1px solid #ccc;">

                <button type="submit" class="btn-small" style="background:#2980b9; color:white; border:none; cursor:pointer;">🔍 Filter</button>
            </form>

            <button onclick="window.print()" class="btn-small" style="background:#7f8c8d; color:white; border:none; cursor:pointer;">🖨️ Cetak</button>
        </div>

        <div style="text-align:center; margin-bottom:20px;">
            <h2 style="margin:0;">REKAPITULASI KEHADIRAN</h2>
            <p style="margin:5px 0; color:#555;">Periode: <?= $judul_bulan ?> <?= $tahun_pilih ?></p>
        </div>

        <table class="table-rekap">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2" class="col-nama">Nama Siswa</th>
                    <th rowspan="2" class="col-kelas">Kelas</th> <th colspan="<?= $jumlah_hari ?>">Tanggal</th>
                    <th colspan="4">Total</th>
                </tr>
                <tr>
                    <?php for($d=1; $d<=$jumlah_hari; $d++){ 
                        echo "<th style='width:20px;'>$d</th>"; 
                    } ?>
                    
                    <th style="background:#dff0d8 !important;">H</th>
                    <th style="background:#fcf8e3 !important;">S</th>
                    <th style="background:#d9edf7 !important;">I</th>
                    <th style="background:#f2dede !important;">A</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // --- LOGIKA QUERY PINTAR ---
                // 1. Base Query: Join Siswa dengan Kelas
                $sql = "SELECT s.*, k.nama_kelas FROM tabel_siswa s 
                        JOIN tabel_kelas k ON s.id_kelas = k.id_kelas";
                
                // 2. Jika filter kelas dipilih, tambahkan WHERE
                if($kelas_pilih != ''){
                    $sql .= " WHERE s.id_kelas = '$kelas_pilih'";
                }

                // 3. Urutkan berdasarkan Kelas dulu, baru Nama (Biar rapi)
                $sql .= " ORDER BY k.nama_kelas ASC, s.nama_lengkap ASC";

                $siswa_qry = mysqli_query($koneksi, $sql);
                $no = 1;

                if(mysqli_num_rows($siswa_qry) > 0) {
                    while($s = mysqli_fetch_array($siswa_qry)){
                        $nis = $s['nis'];
                        $total_H = 0; $total_S = 0; $total_I = 0; $total_A = 0;
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="col-nama"><?= $s['nama_lengkap'] ?></td>
                    <td class="col-kelas"><b><?= $s['nama_kelas'] ?></b></td>
                    
                    <?php
                    // Ambil Data Absen 1 Bulan Penuh untuk Siswa ini (Sekali Query biar cepat)
                    // Kita pakai teknik array indexing biar gak query 30x per siswa
                    $bln_str = sprintf("%02d", $bulan_pilih);
                    $absen_sebulan = mysqli_query($koneksi, "SELECT DAY(tanggal) as tgl, status_kehadiran FROM tabel_absensi 
                                                             WHERE nis='$nis' AND MONTH(tanggal)='$bulan_pilih' AND YEAR(tanggal)='$tahun_pilih'");
                    
                    // Masukkan ke array temporary [tgl => status]
                    $data_absen = [];
                    while($row = mysqli_fetch_array($absen_sebulan)){
                        $data_absen[$row['tgl']] = $row['status_kehadiran'];
                    }

                    // Loop Cetak Kotak Tanggal
                    for($d=1; $d<=$jumlah_hari; $d++){
                        // Tentukan style jika Minggu
                        $tgl_full = "$tahun_pilih-$bln_str-" . sprintf("%02d", $d);
                        $is_minggu = (date('l', strtotime($tgl_full)) == 'Sunday');
                        $style_td = $is_minggu ? "background:#eee;" : "";

                        // Cek status dari array
                        $kode = "-";
                        $class_warna = "";

                        if(isset($data_absen[$d])){
                            $st = $data_absen[$d];
                            if($st == 'Hadir') { $kode="H"; $class_warna="ket-H"; $total_H++; }
                            elseif($st == 'Sakit') { $kode="S"; $class_warna="ket-S"; $total_S++; }
                            elseif($st == 'Izin')  { $kode="I"; $class_warna="ket-I"; $total_I++; }
                            elseif($st == 'Alpa')  { $kode="A"; $class_warna="ket-A"; $total_A++; }
                        }

                        // Jika status ada warnanya, timpa style background minggu
                        if($class_warna != "") $style_td = "";

                        echo "<td style='$style_td' class='$class_warna'>$kode</td>";
                    }
                    ?>
                    
                    <td style="background:#dff0d8; font-weight:bold;"><?= $total_H ?></td>
                    <td style="background:#fcf8e3; font-weight:bold;"><?= $total_S ?></td>
                    <td style="background:#d9edf7; font-weight:bold;"><?= $total_I ?></td>
                    <td style="background:#f2dede; font-weight:bold; color:red;"><?= $total_A ?></td>
                </tr>
                <?php 
                    } // End While
                } else {
                    echo "<tr><td colspan='".($jumlah_hari+7)."' style='padding:20px;'>Belum ada data siswa.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div style="margin-top:30px; float:right; text-align:center; display:none;" class="ttd-area">
            <style> @media print { .ttd-area { display:block !important; } } </style>
            <p>Jember, <?= date('d F Y') ?></p>
            <p>Kepala Sekolah</p>
            <br><br><br>
            <p><b>( ..................................... )</b></p>
        </div>

    </div>
</body>
</html>