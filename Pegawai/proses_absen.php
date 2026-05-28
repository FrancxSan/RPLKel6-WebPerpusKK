<?php
session_start();
include '../koneksi.php';

// 1. Validasi Akun Sesuai Flowchart
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pegawai') {
    header("location:../login.php");
    exit;
}

$id_pegawai = $_SESSION['id_akun'];
$nama_user = $_SESSION['nama'] ?? 'Pegawai';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Absensi Real-Time</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <style>
        body { background-color: #f4f7f6; }
        .header-blue { background-color: #3498db; color : white; padding: 15px; }
        #my_camera { width: 100% !important; height: auto !important; border-radius: 15px; overflow: hidden; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="header-blue shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-camera-fill me-2"></i> Verifikasi Absensi</h5>
        <a href="pegawai_dashboard.php" class="btn btn-sm btn-light text-primary fw-bold">Batal</a>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div id="my_camera" class="mx-auto mb-3"></div>
                    <div id="results" class="d-none mb-3"></div>
                    
                    <div class="alert alert-info small border-0 py-2">
                        <i class="bi bi-geo-alt-fill me-1"></i> 
                        Lokasi Anda sedang diverifikasi...
                    </div>

                    <form id="form-absen" action="simpan_absen.php" method="POST">
                        <input type="hidden" name="foto" id="foto_data">
                        <input type="hidden" name="lat" id="lat_data">
                        <input type="hidden" name="lng" id="lng_data">
                        
                        <button type="button" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow" onclick="take_snapshot()" style="border-radius: 50px;">
                            <i class="bi bi-fingerprint me-2"></i> AMBIL FOTO & SIMPAN DATA
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Konfigurasi Kamera
    Webcam.set({
        width: 480,
        height: 360,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');

    // 3. Mengatur Titik Lokasi Sesuai Flowchart
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('lat_data').value = position.coords.latitude;
            document.getElementById('lng_data').value = position.coords.longitude;
        });
    } else {
        alert("Geolocation tidak didukung oleh browser ini.");
    }

    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            document.getElementById('foto_data').value = data_uri;
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'" class="img-fluid rounded"/>';
            
            // Otomatis submit setelah ambil foto sesuai flowchart
            document.getElementById('form-absen').submit();
        });
    }
</script>

</body>
</html>