<?php
include '../config/koneksi.php';
session_start();
if($_SESSION['status'] != "login" || $_SESSION['role'] != "Siswa"){ header("location:../auth/login.php"); }
$nis = $_SESSION['username'];

// --- LOGIC UPLOAD BUKTI BAYAR ---
if(isset($_POST['kirim_bukti'])){
    $bulan = $_POST['bulan_bayar']; // Contoh: "Juli"
    $nominal = $_POST['nominal'];
    
    // Proses File
    $bukti = $_FILES['bukti']['name'];
    $tmp = $_FILES['bukti']['tmp_name'];
    $ext = pathinfo($bukti, PATHINFO_EXTENSION);
    
    // Nama file unik: BUKTI_NIS_WAKTU.jpg
    $nama_baru = "BUKTI_".$nis."_".time().".".$ext;
    
    if(move_uploaded_file($tmp, "../uploads/".$nama_baru)){
        $ket = "SPP Bulan " . $bulan;
        
        // Simpan ke database dengan status PENDING
        $simpan = mysqli_query($koneksi, "INSERT INTO tabel_keuangan (nis, jenis_transaksi, nominal, keterangan, petugas_input, bukti_transfer, status)
                                          VALUES ('$nis', 'SPP', '$nominal', '$ket', 'Siswa (Upload)', '$nama_baru', 'Pending')");
        
        if($simpan) {
            echo "<script>alert('Bukti berhasil dikirim! Tunggu verifikasi Admin.'); window.location='siswa_keuangan.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal upload gambar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Pembayaran SPP</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
    <div class="navbar" style="background:#2980b9;">
        <h1>💰 Pembayaran SPP</h1>
        <a href="siswa_dashboard.php">Kembali</a>
    </div>

    <div class="container">
        
        <div class="card orange" style="text-align:left; margin-bottom:20px;">
            <h3>ℹ️ Info Transfer</h3>
            <p>Silakan transfer SPP sebesar <b>Rp 150.000</b> ke:</p>
            <ul style="margin-top:0;">
                <li><b>BCA:</b> 123-456-789 (Sekolah Pintar)</li>
                <li><b>BRI:</b> 000-111-222 (Sekolah Pintar)</li>
            </ul>
            <p><small>Setelah transfer, foto struknya dan upload di tombol "Upload Bukti" di bawah.</small></p>
        </div>

        <div class="card" style="text-align:left;">
            <h3>Tagihan SPP Tahun Ini</h3>
            <table>
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $bulan_spp = ["Juli", "Agustus", "September", "Oktober", "November", "Desember", "Januari", "Februari", "Maret", "April", "Mei", "Juni"];
                    
                    foreach($bulan_spp as $bln){
                        // Cek status per bulan
                        $cek = mysqli_query($koneksi, "SELECT * FROM tabel_keuangan WHERE nis='$nis' AND keterangan LIKE '%$bln%' AND jenis_transaksi='SPP'");
                        $data = mysqli_fetch_array($cek);
                        $ada_data = mysqli_num_rows($cek);

                        // Tentukan Tampilan Tombol & Status
                        if($ada_data > 0){
                            if($data['status'] == 'Lunas'){
                                $badge = "<span style='background:green; color:white; padding:3px 8px; border-radius:4px;'>✅ LUNAS</span>";
                                $btn = "-";
                            } else if ($data['status'] == 'Ditolak'){
                                $badge = "<span style='background:red; color:white; padding:3px 8px; border-radius:4px;'>❌ DITOLAK (Upload Ulang)</span>";
                                $btn = "<button onclick=\"bukaModal('$bln')\" class='btn-small btn-green'>Upload Ulang</button>";
                            } else {
                                $badge = "<span style='background:orange; color:white; padding:3px 8px; border-radius:4px;'>⏳ MENUNGGU VERIFIKASI</span>";
                                $btn = "Menunggu Admin";
                            }
                        } else {
                            $badge = "<span style='color:red; font-weight:bold;'>Belum Bayar</span>";
                            $btn = "<button onclick=\"bukaModal('$bln')\" class='btn-small btn-green'>Upload Bukti</button>";
                        }
                    ?>
                    <tr>
                        <td><b><?= $bln ?></b></td>
                        <td><?= $badge ?></td>
                        <td><?= $btn ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modal-upload" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:8px; width:300px; box-shadow:0 5px 15px rgba(0,0,0,0.3);">
            <h3>Upload Bukti Transfer</h3>
            <p>Bulan: <b id="text_bulan"></b></p>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="bulan_bayar" id="input_bulan">
                <input type="hidden" name="nominal" value="150000">
                
                <label>Foto Struk:</label>
                <input type="file" name="bukti" required style="margin-bottom:15px;">
                
                <div style="display:flex; gap:10px;">
                    <button type="submit" name="kirim_bukti" class="btn" style="width:100%;">Kirim</button>
                    <button type="button" onclick="tutupModal()" class="btn" style="width:100%; background:#7f8c8d;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModal(bulan){
            document.getElementById('modal-upload').style.display = 'flex';
            document.getElementById('text_bulan').innerText = bulan;
            document.getElementById('input_bulan').value = bulan;
        }
        function tutupModal(){
            document.getElementById('modal-upload').style.display = 'none';
        }
    </script>
</body>
</html>