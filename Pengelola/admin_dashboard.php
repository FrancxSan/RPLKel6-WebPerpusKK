<?php
session_start();
include '../koneksi.php';

date_default_timezone_set('Asia/Makassar');

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengelola") {
    header("location:../login.php");
    exit;
}

// --- LOGIKA BARU: UPDATE LOKASI KANTOR & JAM PULANG ---
if (isset($_POST['update_lokasi'])) {
    $lat = mysqli_real_escape_string($conn, $_POST['lat_kantor']);
    $long = mysqli_real_escape_string($conn, $_POST['long_kantor']);
    $rad = mysqli_real_escape_string($conn, $_POST['radius']);
    $jam_pulang = mysqli_real_escape_string($conn, $_POST['jam_pulang_standar']); // Tambah variabel jam pulang
    
    mysqli_query($conn, "UPDATE pengaturan_absen SET 
        latitude_kantor = '$lat', 
        longitude_kantor = '$long', 
        radius_maksimal = '$rad',
        jam_pulang_standar = '$jam_pulang' 
        WHERE id = 1");
        
    header("location:admin_dashboard.php?page=dashboard&pesan=lokasi_diperbarui");
    exit;
}

// Tambahan Logika Aksi Update Status Absen
if (isset($_GET['aksi_absen'])) {
    $status_baru = mysqli_real_escape_string($conn, $_GET['aksi_absen']);
    mysqli_query($conn, "UPDATE pengaturan_absen SET status = '$status_baru' WHERE id = 1");
    header("location:admin_dashboard.php?page=dashboard&pesan=absen_diperbarui");
    exit;
}

if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $aksi = $_GET['aksi'];

    if ($aksi == 'hapus') {
        mysqli_query($conn, "DELETE FROM pegawai WHERE id_akun='$id'");
        mysqli_query($conn, "DELETE FROM akun WHERE id_akun='$id'");
    } elseif ($aksi == 'nonaktif') {
        mysqli_query($conn, "UPDATE akun SET status_akun='nonaktif' WHERE id_akun='$id'");
    } elseif ($aksi == 'aktifkan') {
        mysqli_query($conn, "UPDATE akun SET status_akun='aktif' WHERE id_akun='$id'");
    }
    header("location:admin_dashboard.php?page=pegawai&pesan=berhasil");
    exit;
}

$page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
            overflow: hidden;
            color: #919191;
            font-size: 3.5rem;
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

        .btn-logout-admin {
            background-color: #ff4757;
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-logout-admin:hover {
            background-color: #e84118;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
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

        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 25px;
            color: white;
            position: relative;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .bg-red {
            background: #ff4757;
        }

        .bg-orange {
            background: #ffa502;
        }

        .bg-blue {
            background: #2e86de;
        }

        /* Tambahan Style Kontrol Absen */
        .card-control-absen {
            border-radius: 15px;
            border: none;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        @media print {

            .sidebar,
            .top-navbar,
            .btn,
            .no-print,
            .alert {
                display: none !important;
            }

            .main-wrapper {
                margin-left: 0 !important;
            }

            .content-container {
                padding: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['pesan']) && trim($_GET['pesan']) == 'login_berhasil') : ?>
        <div id="notif-login-fixed" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10000; width: 100%; max-width: 400px; padding: 0 20px;">
            <div class="alert alert-success border-0 shadow-lg text-center py-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Login Berhasil!</strong>
                <div class="small opacity-75">Selamat datang, <?= $_SESSION['nama'] ?? 'Pengelola'; ?></div>
            </div>
        </div>
        <script>
            setTimeout(function() {
                var el = document.getElementById('notif-login-fixed');
                if (el) {
                    el.style.opacity = "0";
                    setTimeout(function() {
                        el.remove();
                    }, 600);
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <div class="sidebar">
        <div class="profile-section">
            <div class="profile-img-container shadow-sm"><i class="bi bi-person-fill"></i></div>
            <h6 class="mb-0 fw-bold text-uppercase">PENGELOLA</h6>
            <p class="small opacity-75 mb-0"><?= $_SESSION['nama'] ?></p>
        </div>
        <nav class="nav flex-column mt-3">
            <a href="?page=dashboard" class="nav-link <?= $page == 'dashboard' ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="?page=absensi" class="nav-link <?= $page == 'absensi' ? 'active' : '' ?>"><i class="bi bi-calendar-check"></i> Data Absensi</a>
            <a href="?page=pegawai" class="nav-link <?= $page == 'pegawai' ? 'active' : '' ?>"><i class="bi bi-people-fill"></i> Kelola Pegawai</a>
            <a href="?page=terlewat" class="nav-link <?= $page == 'terlewat' ? 'active' : '' ?>"><i class="bi bi-person-x-fill"></i> Absen Terlewat</a>
            <a href="?page=laporan" class="nav-link <?= $page == 'laporan' ? 'active' : '' ?>"><i class="bi bi-printer-fill"></i> Unduh Laporan</a>
        </nav>
    </div>

    <div class="main-wrapper">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar" id="sidebarCollapse"><i class="bi bi-list"></i></button>
                <h5 class="mb-0 fw-bold">DASHBOARD PENGELOLA</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-4 fw-bold"><i class="bi bi-clock-fill me-2"></i> <span id="live-clock">00:00:00</span></span>
                <button type="button" onclick="konfirmasiLogout()" class="btn btn-sm btn-logout-admin fw-bold shadow-sm px-3">
                    Logout <i class="bi bi-box-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <div class="content-container">
            <?php if ($page == 'dashboard') : ?>
                <?php
                // Ambil Pengaturan Absen Terkini
                $st_q = mysqli_query($conn, "SELECT * FROM pengaturan_absen WHERE id = 1");
                $st_data = mysqli_fetch_assoc($st_q);
                $status_absen = $st_data['status'] ?? 'tutup';
                $lat_kantor = $st_data['latitude_kantor'] ?? '';
                $long_kantor = $st_data['longitude_kantor'] ?? '';
                $radius = $st_data['radius_maksimal'] ?? '100';
                $jam_pulang_min = $st_data['jam_pulang_standar'] ?? '16:00:00'; // Tambah fetch jam pulang

                $tgl_hari_ini = date('Y-m-d');
                $total_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM akun WHERE role='pegawai'"))['jml'];
                $hadir_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM absensi WHERE tanggal_absensi='$tgl_hari_ini'"))['jml'];
                $terlambat_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM absensi WHERE tanggal_absensi='$tgl_hari_ini' AND jam_masuk > '08:00:00'"))['jml'];
                $q_miss = "SELECT id_akun FROM akun WHERE role='pegawai' AND status_akun='aktif' AND id_akun NOT IN (SELECT id_pegawai FROM absensi WHERE tanggal_absensi='$tgl_hari_ini')";
                $miss_count = mysqli_num_rows(mysqli_query($conn, $q_miss));
                ?>

                <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'lokasi_diperbarui') : ?>
                    <script>Swal.fire('Berhasil!', 'Lokasi & Waktu Operasional telah diperbarui.', 'success');</script>
                <?php endif; ?>

                <div class="row mb-4 g-4">
                    <div class="col-md-7">
                        <div class="card card-control-absen p-4 h-100 border-start border-4 border-<?= ($status_absen == 'buka' ? 'success' : 'danger') ?>">
                            <h5 class="fw-bold mb-1"><i class="bi bi-shield-lock-fill me-2"></i> Kendali Akses Absensi</h5>
                            <p class="text-muted small mb-4">Membuka atau mengunci akses tombol absen bagi pegawai.</p>
                            <div class="d-flex align-items-center justify-content-between bg-light p-3 rounded-3">
                                <span class="badge bg-<?= ($status_absen == 'buka' ? 'success' : 'danger') ?> p-2 px-3 fw-bold">
                                    <i class="bi bi-<?= ($status_absen == 'buka' ? 'unlock' : 'lock') ?>-fill me-1"></i> STATUS: <?= strtoupper($status_absen) ?>
                                </span>
                                <?php if ($status_absen == 'tutup') : ?>
                                    <button onclick="updateAbsen('buka')" class="btn btn-success fw-bold shadow-sm"><i class="bi bi-play-fill"></i> BUKA SEKARANG</button>
                                <?php else : ?>
                                    <button onclick="updateAbsen('tutup')" class="btn btn-danger fw-bold shadow-sm"><i class="bi bi-stop-fill"></i> TUTUP SEKARANG</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius:15px;">
                            <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-primary"></i> Area Perpustakaan</h5>
                            <form action="" method="POST">
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="small fw-bold">Latitude</label>
                                        <input type="text" name="lat_kantor" class="form-control form-control-sm" value="<?= $lat_kantor ?>" placeholder="-5.xxxx" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-bold">Longitude</label>
                                        <input type="text" name="long_kantor" class="form-control form-control-sm" value="<?= $long_kantor ?>" placeholder="119.xxxx" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="small fw-bold">Radius (Meter)</label>
                                        <input type="number" name="radius" class="form-control form-control-sm" value="<?= $radius ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-bold text-danger">Jam Pulang Min.</label>
                                        <input type="time" name="jam_pulang_standar" class="form-control form-control-sm" value="<?= $jam_pulang_min ?>" required>
                                    </div>
                                </div>
                                <button type="submit" name="update_lokasi" class="btn btn-primary btn-sm w-100 fw-bold">SIMPAN PERUBAHAN</button>
                            </form>
                        </div>
                    </div>
                </div>

                <?php if ($miss_count > 0) : ?>
                    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                        <div><strong>Perhatian:</strong> Ada <?= $miss_count ?> pegawai aktif yang belum absen hari ini. <a href="?page=terlewat" class="alert-link">Lihat Daftar</a></div>
                    </div>
                <?php endif; ?>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card bg-red">
                            <h2 class="fw-bold"><?= $total_p ?></h2>
                            <p class="mb-0 opacity-75">Total Pegawai</p><i class="bi bi-people position-absolute opacity-25" style="font-size: 4rem; right: 20px; bottom: 0;"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-orange">
                            <h2 class="fw-bold"><?= $hadir_p ?></h2>
                            <p class="mb-0 opacity-75">Hadir Hari Ini</p><i class="bi bi-person-check position-absolute opacity-25" style="font-size: 4rem; right: 20px; bottom: 0;"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-blue">
                            <h2 class="fw-bold"><?= $terlambat_p ?></h2>
                            <p class="mb-0 opacity-75">Terlambat</p><i class="bi bi-clock-history position-absolute opacity-25" style="font-size: 4rem; right: 20px; bottom: 0;"></i>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-lightning-fill text-warning"></i> Absensi Terbaru (Hari Ini)</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Pegawai</th>
                                    <th>Jam Masuk</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res_rt = mysqli_query($conn, "SELECT a.*, ak.username FROM absensi a JOIN akun ak ON a.id_pegawai=ak.id_akun WHERE a.tanggal_absensi='$tgl_hari_ini' ORDER BY a.jam_masuk DESC");
                                while ($r = mysqli_fetch_assoc($res_rt)) : $is_late = (strtotime($r['jam_masuk']) > strtotime('08:00:00')); ?>
                                    <tr>
                                        <td class="fw-bold text-uppercase"><?= $r['username'] ?></td>
                                        <td><?= $r['jam_masuk'] ?></td>
                                        <td><span class="badge bg-<?= $is_late ? 'danger' : 'success' ?>"><?= $is_late ? 'Terlambat' : 'Hadir' ?></span></td>
                                    </tr>
                                <?php endwhile;
                                if (mysqli_num_rows($res_rt) == 0) echo "<tr><td colspan='3' class='text-center py-4 text-muted'>Belum ada pegawai yang absen hari ini.</td></tr>"; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'pegawai') : ?>
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Manajemen Data & Akun Pegawai</h5>
                        <a href="tambah_pegawai_final.php" class="btn btn-primary btn-sm fw-bold">+ TAMBAH BARU</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>NIP</th>
                                    <th>Nama Pegawai</th>
                                    <th>Status Akun</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_peg = "SELECT a.id_akun, a.username, a.status_akun, p.* FROM akun a 
                                           LEFT JOIN pegawai p ON a.id_akun = p.id_akun 
                                           WHERE a.role='pegawai' ORDER BY p.nama_pegawai ASC";
                                $res_p = mysqli_query($conn, $sql_peg);
                                while ($p = mysqli_fetch_assoc($res_p)) : $is_aktif = ($p['status_akun'] == 'aktif'); ?>
                                    <tr>
                                        <td><code class="text-dark fw-bold"><?= $p['nip'] ?? '-' ?></code></td>
                                        <td class="fw-bold text-uppercase"><?= $p['nama_pegawai'] ?? $p['username'] ?></td>
                                        <td><span class="badge rounded-pill bg-<?= $is_aktif ? 'success' : 'secondary' ?>"><?= ucfirst($p['status_akun']) ?></span></td>
                                        <td class="text-center">
                                            <div class="btn-group gap-1">
                                                <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#detailPegawai<?= $p['id_akun'] ?>">
                                                    <i class="bi bi-eye"></i> Detail
                                                </button>
                                                <a href="edit_pegawai_final.php?id=<?= $p['id_akun'] ?>" class="btn btn-primary btn-sm fw-bold shadow-sm">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <button onclick="konfirmasiStatus('<?= $p['id_akun'] ?>', '<?= $p['status_akun'] ?>')" class="btn btn-<?= $is_aktif ? 'warning' : 'success' ?> btn-sm fw-bold shadow-sm">
                                                    <i class="bi bi-power"></i>
                                                </button>
                                                <button onclick="konfirmasiHapus('<?= $p['id_akun'] ?>')" class="btn btn-danger btn-sm shadow-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <div class="modal fade text-start" id="detailPegawai<?= $p['id_akun'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title fw-bold"><i class="bi bi-person-vcard me-2"></i>Profil Lengkap Pegawai</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="row g-3">
                                                                <div class="col-6"><small class="text-muted d-block">NIP</small><strong><?= $p['nip'] ?? '-' ?></strong></div>
                                                                <div class="col-6"><small class="text-muted d-block">Status Kepegawaian</small><span class="badge bg-light text-dark border"><?= $p['status_kepegawaian'] ?? '-' ?></span></div>
                                                                <div class="col-12 border-bottom pb-2 mt-3"><small class="text-muted d-block">Nama Lengkap</small>
                                                                    <h6 class="fw-bold text-uppercase mb-0"><?= $p['nama_pegawai'] ?? $p['username'] ?></h6>
                                                                </div>
                                                                <div class="col-6"><small class="text-muted d-block">Pangkat/Gol. Ruang</small><span><?= $p['pangkat_gol_ruang'] ?? '-' ?></span></div>
                                                                <div class="col-6"><small class="text-muted d-block">Jabatan</small><span><?= $p['jabatan'] ?? '-' ?></span></div>
                                                                <div class="col-6"><small class="text-muted d-block">Jenis Kelamin</small><span class="text-capitalize"><?= $p['jenis_kelamin'] ?? '-' ?></span></div>
                                                                <div class="col-6"><small class="text-muted d-block">Tanggal Lahir</small><span><?= $p['tanggal_lahir'] ? date('d/m/Y', strtotime($p['tanggal_lahir'])) : '-' ?></span></div>
                                                                <div class="col-12 bg-light p-2 rounded mt-2"><small class="text-muted d-block">Alamat Lengkap</small>
                                                                    <p class="mb-0 small"><?= $p['alamat'] ?? '-' ?></p>
                                                                </div>
                                                                <div class="col-12 mt-3"><small class="text-muted d-block italic">Username Login:</small><span class="badge bg-dark"><?= $p['username'] ?></span></div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-secondary w-100 fw-bold" data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'absensi') : ?>
                <?php include 'data_absensi.php'; ?>

            <?php elseif ($page == 'terlewat') : ?>
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-danger mb-4"><i class="bi bi-person-x-fill me-2"></i> Pegawai Belum Absen Hari Ini</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tgl_skrg = date('Y-m-d');
                                $res_miss = mysqli_query($conn, "SELECT * FROM akun WHERE role='pegawai' AND status_akun='aktif' AND id_akun NOT IN (SELECT id_pegawai FROM absensi WHERE tanggal_absensi='$tgl_skrg')");
                                $no = 1;
                                if (mysqli_num_rows($res_miss) > 0) :
                                    while ($m = mysqli_fetch_assoc($res_miss)) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="fw-bold text-uppercase"><?= $m['username'] ?></td>
                                            <td><span class="badge bg-success">Aktif</span></td>
                                            <td class="text-danger small fw-bold">Belum Melakukan Absensi</td>
                                        </tr>
                                    <?php endwhile;
                                else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-success fw-bold">Semua pegawai aktif sudah absen hari ini!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'laporan') : ?>
                <?php
                $tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-01');
                $tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');
                $sql_lap = "SELECT a.*, ak.username FROM absensi a JOIN akun ak ON a.id_pegawai=ak.id_akun WHERE a.tanggal_absensi BETWEEN '$tgl_mulai' AND '$tgl_selesai' ORDER BY a.tanggal_absensi DESC";
                $res_lap = mysqli_query($conn, $sql_lap);
                ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 no-print">
                    <h5 class="fw-bold mb-4">Filter Periode Laporan</h5>
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="page" value="laporan">
                        <div class="col-md-4"><label class="form-label small fw-bold">Mulai Tanggal</label><input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>"></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">Sampai Tanggal</label><input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>"></div>
                        <div class="col-md-4 d-flex align-items-end gap-2"><button type="submit" class="btn btn-primary w-100 fw-bold">FILTER</button><button onclick="window.print()" class="btn btn-success w-100 fw-bold">CETAK PDF</button></div>
                    </form>
                </div>
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="text-center mb-4 d-none d-print-block">
                        <h3 class="fw-bold">LAPORAN ABSENSI PERPUSTAKAAN</h3>
                        <p>Periode: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> s/d <?= date('d/m/Y', strtotime($tgl_selesai)) ?></p>
                        <hr style="border: 2px solid #000;">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Masuk</th>
                                    <th>Ket</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($l = mysqli_fetch_assoc($res_lap)): $is_late = (strtotime($l['jam_masuk']) > strtotime('08:00:00')); ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="text-uppercase fw-bold"><?= $l['username'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($l['tanggal_absensi'])) ?></td>
                                        <td><?= $l['jam_masuk'] ?></td>
                                        <td><span class="badge bg-<?= $is_late ? 'danger' : 'success' ?>"><?= $is_late ? 'Terlambat' : 'Hadir' ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-toggled');
        });

        function updateClock() {
            var now = new Date();
            document.getElementById('live-clock').textContent = String(now.getHours()).padStart(2, '0') + ":" + String(now.getMinutes()).padStart(2, '0') + ":" + String(now.getSeconds()).padStart(2, '0');
        }
        setInterval(updateClock, 1000);
        updateClock();

        function updateAbsen(status) {
            Swal.fire({
                title: status === 'buka' ? 'Buka Akses Absen?' : 'Tutup Akses Absen?',
                text: status === 'buka' ? "Pegawai akan bisa melakukan absen hari ini." : "Seluruh akses absen pegawai akan dikunci.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: status === 'buka' ? '#2ecc71' : '#ff4757',
                confirmButtonText: 'Ya, Lanjutkan',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '?aksi_absen=' + status;
            });
        }

        function konfirmasiStatus(id, status) {
            Swal.fire({
                title: 'Ubah Status Akun?',
                text: "Status akan diubah menjadi " + (status === 'aktif' ? 'Nonaktif' : 'Aktif'),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3498db',
                confirmButtonText: 'Ya, Ubah',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '?page=pegawai&aksi=' + (status === 'aktif' ? 'nonaktif' : 'aktifkan') + '&id=' + id;
            });
        }

        function konfirmasiHapus(id) {
            Swal.fire({
                title: 'Hapus Akun?',
                text: "Data pegawai akan dihapus permanen dari database!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff4757',
                confirmButtonText: 'Ya, Hapus',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '?page=pegawai&aksi=hapus&id=' + id;
            });
        }

        function konfirmasiLogout() {
            Swal.fire({
                title: 'Keluar dari Sistem?',
                text: "Anda perlu login kembali untuk mengakses panel pengelola.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff4757',
                confirmButtonText: 'Ya, Keluar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '../logout.php';
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>