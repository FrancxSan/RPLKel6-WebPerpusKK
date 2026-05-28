<?php
session_start();
include '../koneksi.php';

// Set zona waktu agar sinkron dengan jam admin
date_default_timezone_set('Asia/Makassar');

// 1. Validasi Sesi Pegawai
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pegawai') {
    header("location:../login.php");
    exit;
}

// 2. Persiapan Data
$id_pegawai = $_SESSION['id_akun'];
$tgl_hari_ini = date('Y-m-d');
$jam_sekarang = date('H:i:s');

// ============================================================
// TAMBAHAN: VALIDASI JAM PULANG MINIMAL DARI ADMIN
// ============================================================
$query_pengaturan = mysqli_query($conn, "SELECT jam_pulang_standar FROM pengaturan_absen WHERE id = 1");
$data_set = mysqli_fetch_assoc($query_pengaturan);
$jam_pulang_minimal = $data_set['jam_pulang_standar'] ?? '16:00:00';

// Jika jam sekarang masih kurang dari jam yang ditentukan admin
if ($jam_sekarang < $jam_pulang_minimal) {
    header("location:pegawai_dashboard.php?pesan=belum_waktunya&min=$jam_pulang_minimal");
    exit;
}
// ============================================================

// 3. Update Database
// Mencari data absen milik pegawai tersebut yang tanggalnya hari ini dan jam_pulangnya masih kosong
$query = "UPDATE absensi SET jam_pulang = '$jam_sekarang' 
          WHERE id_pegawai = '$id_pegawai' 
          AND tanggal_absensi = '$tgl_hari_ini' 
          AND jam_pulang IS NULL";

if (mysqli_query($conn, $query)) {
    // Cek apakah ada baris yang ter-update
    if (mysqli_affected_rows($conn) > 0) {
        header("location:pegawai_dashboard.php?pesan=pulang_berhasil");
    } else {
        // Jika pegawai klik dua kali atau belum absen masuk sama sekali
        header("location:pegawai_dashboard.php?pesan=gagal_pulang");
    }
} else {
    echo "Gagal mencatat jam pulang: " . mysqli_error($conn);
}
?>