<?php
include '../koneksi.php';

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "INSERT INTO akun (username, password, nama, role, status_akun) 
              VALUES ('$user', '$pass', '$nama', 'pegawai', 'aktif')";

    if (mysqli_query($conn, $query)) {
        header("location:admin_dashboard.php?page=pegawai&pesan=tambah_berhasil");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
