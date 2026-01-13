<?php
session_start();
if($_SESSION['role'] != "Guru"){ header("location:login.php"); }

// Ambil data dari URL
$id_kelas_target = $_GET['kelas'];
$nama_kelas_target = $_GET['nama_kelas'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scan Absen Kelas <?= $nama_kelas_target ?></title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #2c3e50; color: white; text-align: center; }
        #reader { width: 100%; max-width: 500px; margin: 20px auto; background: white; padding: 10px; border-radius: 10px;}
        #result-box { 
            margin-top: 20px; padding: 15px; border-radius: 8px; 
            font-size: 1.2em; font-weight: bold; background: rgba(0,0,0,0.2);
            min-height: 50px;
        }
        .btn-back { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

    <h2 style="margin-bottom:5px;">Mode Absen Kelas</h2>
    <h1 style="margin-top:0; color:#f1c40f;"><?= $nama_kelas_target ?></h1>
    
    <div id="reader"></div>
    
    <div id="result-box">Menunggu Scan...</div>
    
    <audio id="beep-sound" src="https://www.soundjay.com/button/beep-07.wav"></audio>

    <a href="guru_jadwal.php" class="btn-back">Kembali ke Jadwal</a>

    <script>
        // Variabel ID Kelas Target biar JS tahu
        var targetKelas = "<?= $id_kelas_target ?>";

        function onScanSuccess(decodedText, decodedResult) {
            // Mainkan suara beep
            document.getElementById('beep-sound').play();
            
            // Tampilkan loading
            document.getElementById('result-box').innerHTML = "Memproses...";
            document.getElementById('result-box').style.backgroundColor = "orange";

            // Kirim data ke backend via AJAX
            // Kita kirim QR Code + ID Kelas Target
            fetch('proses_absen_kelas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'qr_code=' + decodedText + '&id_kelas_target=' + targetKelas
            })
            .then(response => response.json()) // Kita pakai JSON biar enak olah errornya
            .then(data => {
                let box = document.getElementById('result-box');
                
                // Tampilkan Pesan dari Server
                box.innerHTML = data.pesan;
                
                // Ubah warna box sesuai status
                if(data.status == 'sukses') {
                    box.style.backgroundColor = "#27ae60"; // Hijau
                } else if(data.status == 'gagal') {
                    box.style.backgroundColor = "#c0392b"; // Merah
                } else {
                    box.style.backgroundColor = "#f39c12"; // Kuning (Warning/Sudah Absen)
                }
                
                // Jeda 2.5 detik sebelum scan berikutnya
                html5QrcodeScanner.pause(); 
                setTimeout(() => { 
                    box.innerHTML = "Siap Scan Berikutnya...";
                    box.style.backgroundColor = "rgba(0,0,0,0.2)";
                    html5QrcodeScanner.resume(); 
                }, 2500);
            })
            .catch(err => {
                console.error(err);
                document.getElementById('result-box').innerHTML = "Error Koneksi Server";
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>

</body>
</html>