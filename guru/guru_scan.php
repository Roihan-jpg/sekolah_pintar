<?php
session_start();
include '../config/koneksi.php';

if($_SESSION['role'] != "Guru"){ header("location:../auth/login.php"); }

// Ambil ID Kelas dari pilihan guru sebelumnya
if(!isset($_GET['kelas'])){ header("location:guru_pilih_kelas.php"); }
$id_kelas = $_GET['kelas'];

// Cari Nama Kelas biar judulnya enak dilihat
$q_kelas = mysqli_query($koneksi, "SELECT nama_kelas FROM tabel_kelas WHERE id_kelas='$id_kelas'");
$d_kelas = mysqli_fetch_array($q_kelas);
$nama_kelas = $d_kelas['nama_kelas'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scanner Kelas <?= $nama_kelas ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #2c3e50; color: white; text-align: center; font-family: sans-serif; }
        
        /* Area Kamera */
        #reader { width: 100%; max-width: 400px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; }
        
        /* Area Hasil Scan */
        #result-card {
            background: white; color: #333; 
            width: 90%; max-width: 400px; margin: 20px auto;
            padding: 20px; border-radius: 10px;
            display: none; /* Sembunyikan dulu */
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            animation: popIn 0.3s ease-out;
        }
        
        @keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .status-badge { padding: 5px 15px; border-radius: 20px; color: white; font-weight: bold; display: inline-block; margin-bottom: 10px; }
        .bg-success { background: #27ae60; }
        .bg-error { background: #c0392b; }
        .bg-warn { background: #f39c12; }

        .siswa-nama { font-size: 1.2em; font-weight: bold; margin: 5px 0; color: #2c3e50; }
        .siswa-info { color: #7f8c8d; font-size: 0.9em; }

        .btn-ganti { display: inline-block; margin-top: 20px; padding: 10px 20px; background: rgba(255,255,255,0.1); color: white; text-decoration: none; border-radius: 5px; border: 1px solid rgba(255,255,255,0.3); }
        .btn-ganti:hover { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body>

    <div class="navbar" style="background:#27ae60; display:flex; justify-content:space-between; align-items:center; padding: 10px 20px;">
        <a href="guru_pilih_kelas.php" style="color:white; text-decoration:none; font-weight:bold; font-size:14px;">⬅ Kembali</a>
        <h1 style="font-size:16px; margin:0;">📷 Absensi Kelas <b><?= $nama_kelas ?></b></h1>
        <div style="width:60px;">  
        </div>
    </div>

    <div id="reader"></div>

    <div id="result-card">
        <div id="status-icon" style="font-size: 40px; margin-bottom: 10px;">✅</div>
        <span id="badge-status" class="status-badge bg-success">BERHASIL</span>
        <div class="siswa-nama" id="text-nama">Nama Siswa</div>
        <div class="siswa-info" id="text-info">NIS: 123456</div>
    </div>

    <div id="loading-text" style="display:none; margin-top:10px;">⏳ Memproses data...</div>

    <a href="guru_pilih_kelas.php" class="btn-ganti">🔄 Ganti Kelas Lain</a>

    <audio id="beep" src="https://www.soundjay.com/button/beep-07.wav"></audio>
    <audio id="beep-error" src="https://www.soundjay.com/button/beep-10.wav"></audio>

    <script>
        var targetKelas = "<?= $id_kelas ?>";
        var isProcessing = false; 

        function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;
            
            // UI Loading
            document.getElementById('loading-text').style.display = 'block';
            document.getElementById('result-card').style.display = 'none';

            // Kirim ke Backend
            let formData = new FormData();
            formData.append('qr_code', decodedText);
            formData.append('id_kelas_target', targetKelas);

            fetch('proses_absen_kelas.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                document.getElementById('loading-text').style.display = 'none';
                showResult(data);
            })
            .catch(err => {
                alert("Error koneksi: " + err);
                isProcessing = false;
            });
        }

        function showResult(data){
            let card = document.getElementById('result-card');
            let badge = document.getElementById('badge-status');
            let icon = document.getElementById('status-icon');
            let nama = document.getElementById('text-nama');
            let info = document.getElementById('text-info');
            let sound = document.getElementById('beep');
            let soundErr = document.getElementById('beep-error');

            // Isi Data
            nama.innerText = data.nama_siswa || "Tidak Dikenal";
            info.innerHTML = data.pesan_tambahan;

            // Atur Warna & Suara berdasarkan Status
            if(data.status == 'sukses') {
                badge.className = "status-badge bg-success";
                badge.innerText = "HADIR";
                icon.innerText = "✅";
                sound.play();
            } 
            else if(data.status == 'gagal') {
                badge.className = "status-badge bg-error";
                badge.innerText = "GAGAL";
                icon.innerText = "⛔";
                soundErr.play();
            }
            else { // Warning (Sudah Absen)
                badge.className = "status-badge bg-warn";
                badge.innerText = "SUDAH ABSEN";
                icon.innerText = "⚠️";
                soundErr.play();
            }

            // Tampilkan Card
            card.style.display = 'block';

            // Jeda 2.5 detik sebelum scan lagi
            setTimeout(() => { 
                isProcessing = false; 
                card.style.display = 'none'; // Sembunyikan lagi biar siap scan baru
            }, 2500);
        }

        let scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        scanner.render(onScanSuccess);
    </script>
</body>
</html>