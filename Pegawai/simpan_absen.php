<?php
session_start();
include '../koneksi.php';

// Set zona waktu agar akurat dengan jam lokal
date_default_timezone_set('Asia/Makassar');

// 1. Proteksi Sesi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pegawai') {
    header("location:../login.php");
    exit;
}

if ($_POST) {
    $id_pegawai = $_SESSION['id_akun'];
    $tgl_sekarang = date('Y-m-d');
    $jam_sekarang = date('H:i:s');
    
    // Ambil data dari POST dan pastikan koordinat adalah Float
    $foto = $_POST['foto'] ?? '';
    $lat_user = isset($_POST['lat']) ? (float)$_POST['lat'] : 0;
    $lng_user = isset($_POST['lng']) ? (float)$_POST['lng'] : 0;

    // ============================================================
    // 2. CEK APAKAH SUDAH ABSEN HARI INI (Proteksi Double Data)
    // ============================================================
    $cek_absen = mysqli_query($conn, "SELECT id_absensi FROM absensi WHERE id_pegawai = '$id_pegawai' AND tanggal_absensi = '$tgl_sekarang'");
    if (mysqli_num_rows($cek_absen) > 0) {
        header("location:pegawai_dashboard.php?pesan=sudah_absen");
        exit;
    }

    // ============================================================
    // 3. AMBIL SETTING LOKASI DARI DATABASE
    // ============================================================
    $query_pengaturan = mysqli_query($conn, "SELECT * FROM pengaturan_absen WHERE id = 1");
    $konfig = mysqli_fetch_assoc($query_pengaturan);

    $lat_pusat = (float)$konfig['latitude_kantor']; 
    $lng_pusat = (float)$konfig['longitude_kantor']; 
    $radius_maksimal = (int)$konfig['radius_maksimal']; 

    // Fungsi menghitung jarak antara dua titik koordinat (Haversine Formula sederhana)
    function hitungJarak($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad((float)$lat1)) * sin(deg2rad((float)$lat2)) +  cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * cos(deg2rad((float)$theta));
        $dist = acos(pemin_max($dist, -1, 1)); // Proteksi angka acos agar tidak error
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1609.344); // Hasil dalam meter
    }

    // Fungsi pembantu agar acos tidak menghasilkan NaN pada jarak yang sangat dekat
    function pemin_max($val, $min, $max) {
        return ($val >= $max) ? $max : (($val <= $min) ? $min : $val);
    }

    $jarak = hitungJarak($lat_user, $lng_user, $lat_pusat, $lng_pusat);

    // 4. Validasi Jarak Sesuai Jangkauan
    if ($jarak > $radius_maksimal) {
        header("location:pegawai_dashboard.php?pesan=luar_radius&jarak=" . round($jarak));
        exit;
    }

    // 5. Proses Simpan Foto ke Folder img/absensi/
    if (!empty($foto)) {
        $foto = str_replace('data:image/jpeg;base64,', '', $foto);
        $foto = str_replace(' ', '+', $foto);
        $data_foto = base64_decode($foto);
        $nama_file_foto = uniqid() . '.png';
        $folder_simpan = '../img/absensi/' . $nama_file_foto;

        if (!file_exists('../img/absensi/')) {
            mkdir('../img/absensi/', 0777, true);
        }
        file_put_contents($folder_simpan, $data_foto);
    } else {
        $nama_file_foto = ""; // Jika tidak ada foto
    }

    // 6. Simpan ke Database
    $query = "INSERT INTO absensi (id_pegawai, tanggal_absensi, jam_masuk, foto_absensi, latitude, longitude) 
              VALUES ('$id_pegawai', '$tgl_sekarang', '$jam_sekarang', '$nama_file_foto', '$lat_user', '$lng_user')";

    if (mysqli_query($conn, $query)) {
        header("location:pegawai_dashboard.php?pesan=absen_berhasil");
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($conn);
    }
}
?>