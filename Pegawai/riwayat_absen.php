<?php
session_start();
include '../koneksi.php';

// Set zona waktu Makassar
date_default_timezone_set('Asia/Makassar');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pegawai') {
    header("location:../login.php");
    exit;
}

$id_pegawai = $_SESSION['id_akun'];
$nama_user = $_SESSION['nama'] ?? 'Pegawai';

// Ambil riwayat absen
$sql = "SELECT * FROM absensi WHERE id_pegawai = '$id_pegawai' ORDER BY tanggal_absensi DESC";
$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi | Perpustakaan</title>
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

        /* SIDEBAR - Struktur Identik */
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

        /* CONTENT AREA - Struktur Identik */
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
            height: 70px; /* Menentukan tinggi tetap agar tidak goyang */
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

        /* Tombol Logout Danger Custom (Sama Ukuran) */
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

        /* Table Styling */
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .table thead { background-color: #f8f9fa; }
        .img-absensi { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="profile-section">
            <div class="profile-img-container shadow-sm">
                <i class="bi bi-person-fill"></i>
            </div>
            <h6 class="mb-0 fw-bold">PEGAWAI</h6>
            <p class="small opacity-75 mb-0"><?= $nama_user ?></p>
        </div>
        <nav class="nav flex-column mt-3">
            <a href="pegawai_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="riwayat_absen.php" class="nav-link active"><i class="bi bi-clock-history"></i> Riwayat Absen</a>
        </nav>
    </div>

    <div class="main-wrapper">
        <div class="top-navbar shadow-sm">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar" id="sidebarCollapse">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-bold">RIWAYAT ABSENSI SAYA</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-4 fw-bold"><i class="bi bi-clock-fill me-2"></i> <span id="live-clock">00:00:00</span></span>
                <button type="button" onclick="konfirmasiLogout()" class="btn btn-danger-custom shadow-sm">
                    Logout <i class="bi bi-box-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <div class="content-container">
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th> 
                                <th class="text-center">Foto</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($query) > 0) : ?>
                                <?php while($row = mysqli_fetch_assoc($query)) : ?>
                                <tr>
                                    <td class="fw-bold"><?= date('d/m/Y', strtotime($row['tanggal_absensi'])); ?></td>
                                    <td><span class="badge bg-success px-3"><?= $row['jam_masuk']; ?></span></td>
                                    <td>
                                        <?php if ($row['jam_pulang'] != NULL) : ?>
                                            <span class="badge bg-danger px-3"><?= $row['jam_pulang']; ?></span>
                                        <?php else : ?>
                                            <span class="text-muted small"><i>Belum Absen Pulang</i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <img src="../img/absensi/<?= $row['foto_absensi']; ?>" class="img-absensi" onerror="this.src='https://placehold.co/50x50?text=Foto'">
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['jam_pulang'] != NULL) : ?>
                                            <span class="badge rounded-pill bg-primary px-3">Selesai</span>
                                        <?php else : ?>
                                            <span class="badge rounded-pill bg-warning text-dark px-3">Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">Data riwayat belum tersedia.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        // 3. Konfirmasi Logout
        function konfirmasiLogout() {
            Swal.fire({
                title: 'Keluar dari Sistem?',
                text: "Anda perlu login kembali untuk mengakses halaman ini.",
                icon: 'warning',
                iconColor: '#ff4757',
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