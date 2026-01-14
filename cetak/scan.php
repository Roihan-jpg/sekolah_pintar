<!DOCTYPE html>
<html>
<head>
    <title>Scan Absensi Sekolah</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { font-family: sans-serif; text-align: center; background: #2c3e50; color: white; }
        #reader { width: 500px; margin: 0 auto; background: white; padding: 10px; border-radius: 10px;}
        #result { margin-top: 20px; font-size: 1.2em; font-weight: bold; }
    </style>
</head>
<body>

    <h1>Mode Kiosk Absensi</h1>
    <div id="reader"></div>
    <div id="result">Menunggu Scan...</div>

    <audio id="beep-sound" src="https://www.soundjay.com/button/beep-07.wav"></audio>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Mainkan suara beep
            document.getElementById('beep-sound').play();
            
            // Kirim data ke backend via AJAX
            fetch('proses_absen.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'qr_code=' + decodedText
            })
            .then(response => response.text())
            .then(data => {
                // Tampilkan pesan hasil dari server
                document.getElementById('result').innerHTML = data;
                
                // Jeda 2 detik sebelum scan berikutnya (biar gak double scan)
                html5QrcodeScanner.pause(); 
                setTimeout(() => { html5QrcodeScanner.resume(); }, 2000);
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>

</body>
</html>