<?php
session_start();
include '../config/koneksi.php';

// Jika sudah login, cek role dan lempar ke dashboard masing-masing
if(isset($_SESSION['status']) && $_SESSION['status'] == "login"){
    if($_SESSION['role'] == 'Admin'){ header("location:../admin/dashboard.php"); }
    else if($_SESSION['role'] == 'Guru'){ header("location:../guru/guru_dashboard.php"); }
    else if($_SESSION['role'] == 'Siswa'){ header("location:../siswa/siswa_dashboard.php"); }
    exit;
}

// Proses Login
if(isset($_POST['login'])){
    $username = $_POST['username']; 
    $password = md5($_POST['password']); // Enkripsi MD5 (Sesuai kode asli)

    // --- PERBAIKAN: MENGGUNAKAN PREPARED STATEMENT ---

    // 1. CEK ADMIN
    // Siapkan template query dengan tanda tanya (?) sebagai placeholder
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tabel_user WHERE username=? AND password=?");
    // Bind parameter: "ss" artinya string, string (untuk username dan password)
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    // Eksekusi query
    mysqli_stmt_execute($stmt);
    // Ambil hasil
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        $d = mysqli_fetch_array($result);
        $_SESSION['username'] = $d['username'];
        $_SESSION['nama']     = $d['nama_lengkap'];
        $_SESSION['role']     = "Admin";
        $_SESSION['status']   = "login";
        header("location:../admin/dashboard.php");
        exit;
    }
    mysqli_stmt_close($stmt); // Tutup statement agar bisa dipakai ulang

    // 2. CEK GURU (Login pakai NIP)
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tabel_guru WHERE nip=? AND password=?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        $d = mysqli_fetch_array($result);
        $_SESSION['username'] = $d['nip'];
        $_SESSION['nama']     = $d['nama_guru'];
        $_SESSION['role']     = "Guru";
        $_SESSION['status']   = "login";
        header("location:../guru/guru_dashboard.php");
        exit;
    }
    mysqli_stmt_close($stmt);

    // 3. CEK SISWA (Login pakai NIS)
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tabel_siswa WHERE nis=? AND password=?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        $d = mysqli_fetch_array($result);
        $_SESSION['username'] = $d['nis'];
        $_SESSION['nama']     = $d['nama_lengkap'];
        $_SESSION['role']     = "Siswa";
        $_SESSION['status']   = "login";
        header("location:../siswa/siswa_dashboard.php");
        exit;
    }
    mysqli_stmt_close($stmt);

    $error = "Akun tidak ditemukan atau Password salah!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem Sekolah</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <style>
        body { background: #2c3e50; display: flex; justify-content: center; align-items: center; height: 100vh; margin:0; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); width: 100%; max-width: 350px; text-align: center; }
        .alert { background: #ffdede; color: red; padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 14px;}
        input { width: 100%; margin-bottom: 15px; }
        .btn { width: 100%; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>👋 Login Sistem</h2>
        <p style="color:#666; font-size:14px;">Masukkan Username/NIP/NIS</p>

        <?php if(isset($error)) { echo "<div class='alert'>$error</div>"; } ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username / NIP / NIS" required autocomplete="off">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn">MASUK</button>
        </form>
    </div>
</body>
</html>