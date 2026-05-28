<?php
session_start();
include '../koneksi.php';

// Set zona waktu Makassar
date_default_timezone_set('Asia/Makassar');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pegawai') {
    header("location:../login.php");
    exit;
}

// --- TAMBAHAN LOGIKA: CEK STATUS SAKLAR ABSEN DARI ADMIN ---
$query_akses = mysqli_query($conn, "SELECT status, jam_pulang_standar FROM pengaturan_absen WHERE id = 1");
$data_akses = mysqli_fetch_assoc($query_akses);
$status_absen_admin = $data_akses['status'] ?? 'tutup';
$jam_pulang_minimal = $data_akses['jam_pulang_standar'] ?? '16:00:00';
// -----------------------------------------------------------

$id_pegawai = $_SESSION['id_akun'];
$nama_user = $_SESSION['nama'] ?? 'Pegawai';
$tgl_sekarang = date('Y-m-d');

// Cek data absensi hari ini
$cek_absen = mysqli_query($conn, "SELECT * FROM absensi WHERE id_pegawai = '$id_pegawai' AND tanggal_absensi = '$tgl_sekarang'");
$data_absen = mysqli_fetch_assoc($cek_absen);
$sudah_absen_masuk = mysqli_num_rows($cek_absen);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai | Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            transition: all 0.3s ease;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #58d68d;
            position: fixed;
            color: white;
            padding: 20px 0;
            z-index: 1000;
            transition: all 0.3s ease;
            left: 0;
        }

        .sidebar .profile-section {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar .profile-img-container {
            width: 85px;
            height: 85px;
            background-color: #e9edef;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            color: #919191;
            font-size: 3.5rem;
            overflow: hidden;
        }

        .sidebar .nav-link {
            color: white;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            padding-left: 35px;
            border-left: 4px solid white;
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 5px solid white;
        }

        .sidebar .nav-link i {
            margin-right: 15px;
            font-size: 1.2rem;
        }

        /* CONTENT AREA */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        body.sidebar-toggled .sidebar {
            left: -260px;
        }

        body.sidebar-toggled .main-wrapper {
            margin-left: 0;
        }

        .top-navbar {
            background: #3498db;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            height: 70px;
        }

        .toggle-sidebar {
            cursor: pointer;
            font-size: 1.5rem;
            margin-right: 15px;
            color: white;
            background: none;
            border: none;
            display: flex;
            align-items: center;
        }

        .content-container {
            padding: 30px;
        }

        /* Card Stat Style */
        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 25px;
            color: white;
            position: relative;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); }
        .bg-red { background: #ff4757; }
        .bg-orange { background: #ffa502; }
        .bg-blue { background: #2e86de; }

        /* Tombol Logout Danger Custom */
        .btn-danger-custom {
            background-color: #ff4757;
            border: none;
            color: white;
            font-weight: bold;
            padding: 5px 20px;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        .btn-danger-custom:hover {
            background-color: #e84118;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
        }

        /* Style untuk status terkunci */
        .lock-icon-container {
            font-size: 4rem;
            color: #ff4757;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <?php if (isset($_GET['pesan']) && trim($_GET['pesan']) == 'login_berhasil') : ?>
        <div id="notif-login-fixed" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10000; width: 100%; max-width: 400px; padding: 0 20px;">
            <div class="alert alert-success border-0 shadow-lg text-center py-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Login Berhasil!</strong>
                <div class="small opacity-75">Selamat datang, <?= $nama_user; ?></div>
            </div>
        </div>
        <script>
            setTimeout(function() {
                var el = document.getElementById('notif-login-fixed');
                if (el) { 
                    el.style.transition = "opacity 0.6s ease";
                    el.style.opacity = "0"; 
                    setTimeout(function() { el.remove(); }, 600); 
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="sidebar">
        <div class="profile-section">
            <div class="profile-img-container shadow-sm">
                <i class="bi bi-person-fill"></i>
            </div>
            <h6 class="mb-0 fw-bold text-uppercase">PEGAWAI</h6>
            <p class="small opacity-75 mb-0"><?= $nama_user ?></p>
        </div>
        <nav class="nav flex-column mt-3">
            <a href="pegawai_dashboard.php" class="nav-link active"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="riwayat_absen.php" class="nav-link"><i class="bi bi-clock-history"></i> Riwayat Absen</a>
        </nav>
    </div>

    <div class="main-wrapper">
        <div class="top-navbar shadow-sm">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar" id="sidebarCollapse">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold">DASHBOARD PEGAWAI</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-4 fw-bold"><i class="bi bi-clock-fill me-2"></i> <span id="live-clock">00:00:00</span></span>
                <button type="button" onclick="konfirmasiLogout()" class="btn btn-danger-custom shadow-sm">
                    Logout <i class="bi bi-box-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <div class="content-container">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card bg-red shadow-sm">
                        <h2 class="fw-bold"><?= ($sudah_absen_masuk > 0) ? 'Hadir' : 'Belum Absen' ?></h2>
                        <p class="mb-0 opacity-75">Status Hari Ini</p>
                        <i class="bi bi-person-check position-absolute opacity-25" style="font-size: 4rem; right: 20px; bottom: 0;"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-orange shadow-sm">
                        <h2 class="fw-bold"><?= ($sudah_absen_masuk > 0) ? $data_absen['jam_masuk'] : '-- : --' ?></h2>
                        <p class="mb-0 opacity-75">Jam Masuk</p>
                        <i class="bi bi-clock-history position-absolute opacity-25" style="font-size: 4rem; right: 20px; bottom: 0;"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-blue shadow-sm">
                        <h2 class="fw-bold"><?= date('d/m/Y') ?></h2>
                        <p class="mb-0 opacity-75">Tanggal</p>
                        <i class="bi bi-calendar-event position-absolute opacity-25" style="font-size: 4rem; right: 20px; bottom: 0;"></i>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <div class="card-body">
                    <?php if ($sudah_absen_masuk == 0) : ?>
                        
                        <?php if ($status_absen_admin == 'tutup') : ?>
                            <div class="lock-icon-container">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h2 class="fw-bold">Absensi Belum Dibuka</h2>
                            <p class="text-muted mb-4">Maaf, akses absensi saat ini masih dikunci oleh Pengelola.<br>Silakan tunggu instruksi selanjutnya untuk melakukan absen masuk.</p>
                            <button class="btn btn-secondary btn-lg px-5 py-3 shadow-sm" style="border-radius: 50px; font-weight: bold;" disabled>
                                <i class="bi bi-lock-fill me-2"></i> AKSES TERKUNCI
                            </button>
                        <?php else : ?>
                            <h2 class="fw-bold">Selamat Datang, <?= $nama_user ?>!</h2>
                            <p class="text-muted mb-4">Silakan klik tombol di bawah untuk melakukan absensi masuk hari ini.</p>
                            
                            <form action="proses_absen.php" method="POST">
                                <input type="hidden" name="lat" id="lat_input">
                                <input type="hidden" name="lng" id="lng_input">
                                <button type="submit" class="btn btn-success btn-lg px-5 py-3 shadow" style="border-radius: 50px; font-weight: bold;">
                                    <i class="bi bi-fingerprint me-2"></i> ABSEN MASUK SEKARANG
                                </button>
                            </form>
                        <?php endif; ?>

                    <?php elseif ($sudah_absen_masuk > 0 && $data_absen['jam_pulang'] == NULL) : ?>
                        <h2 class="fw-bold text-warning">Sedang Bertugas...</h2>
                        <p class="text-muted mb-4">Selamat bekerja! Jangan lupa klik tombol di bawah jika tugas Anda telah selesai.</p>
                        <button type="button" onclick="konfirmasiPulang()" class="btn btn-danger btn-lg px-5 py-3 shadow" style="border-radius: 50px; font-weight: bold;">
                            <i class="bi bi-box-arrow-right me-2"></i> ABSEN PULANG
                        </button>
                    <?php else : ?>
                        <h2 class="fw-bold text-success">Tugas Selesai!</h2>
                        <p class="text-muted mb-4">Terima kasih atas dedikasi Anda hari ini. Sampai jumpa besok!</p>
                        <div class="p-3 bg-light d-inline-block rounded-3 border">
                            <span class="me-3">Masuk: <b><?= $data_absen['jam_masuk'] ?></b></span>
                            <span>Pulang: <b><?= $data_absen['jam_pulang'] ?></b></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- LOGIKA PESAN DINAMIS DARI URL ---
        const urlParams = new URLSearchParams(window.location.search);
        const pesan = urlParams.get('pesan');
        const jarak = urlParams.get('jarak');
        const jamMin = urlParams.get('min');

        if (pesan) {
            if (pesan === 'luar_radius') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Absen!',
                    html: `Anda berada di luar jangkauan perpustakaan.<br>Jarak Anda: <b>${jarak} meter</b> dari lokasi.`,
                    confirmButtonColor: '#ff4757'
                });
            } else if (pesan === 'belum_waktunya') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Waktunya Pulang!',
                    html: `Sesuai aturan pengelola, jam pulang minimal adalah pukul <b>${jamMin}</b>.`,
                    confirmButtonColor: '#ffa502'
                });
            } else if (pesan === 'absen_berhasil') {
                Swal.fire({ icon: 'success', title: 'Absen Berhasil!', text: 'Absen berhasil, selamat bekerja Guys!', timer: 2500, showConfirmButton: false });
            } else if (pesan === 'pulang_berhasil') {
                Swal.fire({ icon: 'success', title: 'Berhasil Pulang!', text: 'Absen pulang berhasil, terima kasih untuk hari ini Guys!', timer: 2500, showConfirmButton: false });
            }
            // Bersihkan parameter URL tanpa refresh agar pesan tidak muncul lagi saat page direload
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // --- LOGIKA GEOLOCATION: AMBIL KOORDINAT OTOMATIS ---
        window.onload = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('lat_input').value = position.coords.latitude;
                    document.getElementById('lng_input').value = position.coords.longitude;
                }, function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'GPS Tidak Aktif',
                        text: 'Silakan aktifkan GPS dan izinkan lokasi agar dapat melakukan absensi.'
                    });
                });
            }
        };

        // 1. Toggle Sidebar
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-toggled');
        });

        // 2. Jam Real-time
        function updateClock() {
            var now = new Date();
            document.getElementById('live-clock').textContent =
                now.getHours().toString().padStart(2, '0') + ":" +
                now.getMinutes().toString().padStart(2, '0') + ":" +
                now.getSeconds().toString().padStart(2, '0');
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 3. Konfirmasi Pulang
        function konfirmasiPulang() {
            const sekarang = new Date();
            const jamSekarang = sekarang.getHours().toString().padStart(2, '0') + ":" + sekarang.getMinutes().toString().padStart(2, '0') + ":" + sekarang.getSeconds().toString().padStart(2, '0');
            const jamMinimal = "<?= $jam_pulang_minimal ?>";

            if (jamSekarang < jamMinimal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Waktunya!',
                    text: 'Jangan dulu babolos ambeaa depe jam pulang nanti ' + jamMinimal + ', semangat yaa!',
                    confirmButtonColor: '#ffa502'
                });
            } else {
                Swal.fire({
                    title: 'Konfirmasi Pulang',
                    text: "Yakin ingin mengakhiri jam kerja Anda?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2ecc71',
                    cancelButtonColor: '#ff4757',
                    confirmButtonText: 'Ya, Pulang',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'proses_absen_pulang.php';
                    }
                });
            }
        }

        // 4. Konfirmasi Logout
        function konfirmasiLogout() {
            Swal.fire({
                title: 'Keluar dari Sistem?',
                text: "Anda perlu login kembali untuk mengakses halaman ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff4757', 
                cancelButtonColor: '#afb1b6',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../logout.php';
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>