<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    // Ambil data dari form login
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Menggunakan MD5 sesuai input dummy sebelumnya

    // Query ke tabel 'akun' (sesuai database)
    $query = mysqli_query($conn, "SELECT * FROM akun WHERE username='$username' AND PASSWORD='$password'");

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        // Simpan data ke session
        $_SESSION['id_akun'] = $data['id_akun'];
        $_SESSION['nama']    = $data['username'];
        $_SESSION['role']    = $data['role'];

        // Cek Role untuk menentukan halaman dashboard
        if ($data['role'] == "pengelola") {
            header("location:pengelola/admin_dashboard.php?pesan=login_berhasil");
        } else if ($data['role'] == "pegawai") {
            header("location:pegawai/pegawai_dashboard.php?pesan=login_berhasil");
        }
        exit;
    } else {

        header("location: login.php?pesan=gagal");
        exit;
    }
}
