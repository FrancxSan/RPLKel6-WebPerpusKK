<?php
session_start();
// Menghapus semua data sesi sesuai alur "Mengeluarkan user dari web"
session_destroy();

// Kembali ke login dan mengirim pesan agar muncul notifikasi "Logout Berhasil"
header("location:login.php?pesan=logout_berhasil");
exit;
?>