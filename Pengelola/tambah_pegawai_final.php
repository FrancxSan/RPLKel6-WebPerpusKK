<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengelola") {
    header("location:../login.php");
    exit;
}

$step = $_GET['step'] ?? 1;

if (isset($_POST['simpan_pegawai'])) {
    // Data dari Step 1 (Profil) yang dikirim lewat hidden input di Step 2
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pegawai']);
    $pangkat = mysqli_real_escape_string($conn, $_POST['pangkat']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $tgl_lahir = mysqli_real_escape_string($conn, $_POST['tgl_lahir']);
    $jk = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $status_kep = mysqli_real_escape_string($conn, $_POST['status_kepegawaian']);
    
    // Data dari Step 2 (Akun)
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    // --- PENAMBAHAN QUERY DI SINI AGAR NAMA MASUK KE TABEL AKUN ---
    $query_akun = "INSERT INTO akun (nama, username, password, role, status_akun) VALUES ('$nama', '$username', '$password', 'pegawai', 'aktif')";
    
    if (mysqli_query($conn, $query_akun)) {
        $id_akun_baru = mysqli_insert_id($conn);
        
        // 2. Insert ke Tabel Pegawai (Menggunakan ID Akun yang baru saja dibuat)
        $query_pegawai = "INSERT INTO pegawai (id_akun, nip, nama_pegawai, pangkat_gol_ruang, jabatan, tanggal_lahir, jenis_kelamin, alamat, status_kepegawaian) 
                          VALUES ('$id_akun_baru', '$nip', '$nama', '$pangkat', '$jabatan', '$tgl_lahir', '$jk', '$alamat', '$status_kep')";
        
        if (mysqli_query($conn, $query_pegawai)) {
            // Berhasil, arahkan kembali ke tabel pegawai
            header("location:admin_dashboard.php?page=pegawai&pesan=tambah_berhasil");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pegawai | Registrasi Final</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --my-red: #e74c3c;
            --my-green: #2ecc71;
            --my-blue: #3498db;
        }

        body { 
            background: linear-gradient(135deg, var(--my-red), var(--my-green), var(--my-blue));
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            display: flex; align-items: center; justify-content: center;
            padding: 40px 0;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .card { 
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 25px; 
            border: 1px solid rgba(255, 255, 255, 0.3); 
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 850px;
            width: 95%;
        }

        .card-header-custom {
            background: linear-gradient(45deg, var(--my-blue), var(--my-green));
            padding: 30px;
            text-align: center;
            color: white;
        }

        .btn-custom {
            padding: 12px 25px; border-radius: 12px; font-weight: 700;
            text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;
            transition: all 0.3s; border: none; color: white;
            display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
            cursor: pointer;
        }
        .btn-custom:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.2); color: white; }

        .btn-red { background: var(--my-red); }
        .btn-blue { background: var(--my-blue); }
        .btn-green { background: var(--my-green); }
        .btn-gray { background: #636e72; }

        .form-label { font-weight: 700; color: #333; font-size: 0.8rem; }
        .form-control, .form-select {
            border-radius: 12px; border: 1.5px solid rgba(0,0,0,0.1); 
            background: rgba(255,255,255,0.9); padding: 12px;
        }
        .form-control:focus { border-color: var(--my-blue); box-shadow: none; background: #fff; }

        .stepper-item .step-counter {
            width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.3);
            margin: 0 auto 5px; display: flex; align-items: center; justify-content: center;
            font-weight: bold; border: 2px solid white;
        }
        .active .step-counter { background: white; color: var(--my-blue); }

        .info-nama-box {
            background: rgba(52, 152, 219, 0.1);
            border-left: 5px solid var(--my-blue);
            border-radius: 10px;
            padding: 15px;
        }
    </style>
</head>
<body>

<div class="card shadow-lg">
    <div class="card-header-custom shadow-sm">
        <h4 class="fw-bold mb-0">REGISTRASI PEGAWAI BARU</h4>
        <div class="d-flex justify-content-center gap-4 mt-3">
            <div class="stepper-item <?= $step == 1 ? 'active' : '' ?>">
                <div class="step-counter">1</div>
                <small class="fw-bold">PROFIL</small>
            </div>
            <div class="stepper-item <?= $step == 2 ? 'active' : '' ?>">
                <div class="step-counter">2</div>
                <small class="fw-bold">AKUN</small>
            </div>
        </div>
    </div>
    
    <div class="card-body p-4 p-md-5">
        <form action="?step=<?= $step ?>" method="POST" id="mainForm">
            <?php if ($step == 1) : ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" placeholder="Nomor Induk Pegawai" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NAMA LENGKAP</label>
                        <input type="text" name="nama_pegawai" class="form-control" placeholder="Nama & Gelar" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PANGKAT / GOL. RUANG</label>
                        <input type="text" name="pangkat" class="form-control" placeholder="Contoh: Pembina / IV.a">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">JABATAN</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Pustakawan">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TANGGAL LAHIR</label>
                        <input type="date" name="tgl_lahir" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">JENIS KELAMIN</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">STATUS KEPEGAWAIAN</label>
                        <input type="text" name="status_kepegawaian" class="form-control" placeholder="PNS / Honorer">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ALAMAT DOMISILI</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat Lengkap">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                    <a href="admin_dashboard.php?page=pegawai" class="btn-custom btn-red"><i class="bi bi-x-lg"></i> Batal</a>
                    <button type="button" onclick="nextStep()" class="btn-custom btn-blue">Lanjut ke Akun <i class="bi bi-arrow-right"></i></button>
                </div>

            <?php else : ?>
                <div class="info-nama-box mb-4 d-flex align-items-center">
                    <i class="bi bi-person-badge-fill fs-3 text-primary me-3"></i>
                    <div>
                        <p class="mb-0 text-muted small fw-bold">MEMBUAT AKUN LOGIN UNTUK:</p>
                        <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($_POST['nama_pegawai']) ?></h5>
                    </div>
                </div>

                <?php foreach($_POST as $key => $val): ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($val) ?>">
                <?php endforeach; ?>

                <div class="row justify-content-center py-2">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <label class="form-label">USERNAME LOGIN</label>
                            <input type="text" name="username" class="form-control" placeholder="Buat Username" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="Buat Password" required>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="button" onclick="window.history.back()" class="btn-custom btn-gray"><i class="bi bi-arrow-left"></i> Kembali</button>
                    <button type="submit" name="simpan_pegawai" class="btn-custom btn-green">SIMPAN SELESAI <i class="bi bi-check-lg"></i></button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
    function nextStep() {
        const form = document.getElementById('mainForm');
        // Validasi HTML5 dasar
        if(form.checkValidity()) {
            form.action = "?step=2";
            form.method = "POST";
            form.submit();
        } else {
            form.reportValidity();
        }
    }
</script>

</body>
</html>