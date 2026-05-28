<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengelola") {
    header("location:../login.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT a.username, p.* FROM akun a JOIN pegawai p ON a.id_akun = p.id_akun WHERE a.id_akun = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("location:admin_dashboard.php?page=pegawai");
    exit;
}

if (isset($_POST['update_pegawai'])) {
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_pegawai']);
    $pangkat = mysqli_real_escape_string($conn, $_POST['pangkat']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $tgl_lahir = mysqli_real_escape_string($conn, $_POST['tgl_lahir']);
    $jk = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $status_kep = mysqli_real_escape_string($conn, $_POST['status_kepegawaian']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    mysqli_query($conn, "UPDATE akun SET username='$username' WHERE id_akun='$id'");

    $update_p = "UPDATE pegawai SET 
                nip='$nip', nama_pegawai='$nama', pangkat_gol_ruang='$pangkat', 
                jabatan='$jabatan', tanggal_lahir='$tgl_lahir', jenis_kelamin='$jk', 
                alamat='$alamat', status_kepegawaian='$status_kep' 
                WHERE id_akun='$id'";

    if (mysqli_query($conn, $update_p)) {
        header("location:admin_dashboard.php?page=pegawai&pesan=update_berhasil");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pegawai | Administrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --my-red: #e74c3c; --my-green: #2ecc71; --my-blue: #3498db; }
        body { 
            background: linear-gradient(135deg, var(--my-red), var(--my-green), var(--my-blue));
            background-size: 400% 400%; animation: gradientBG 15s ease infinite;
            min-height: 100vh; font-family: 'Inter', sans-serif;
            display: flex; align-items: center; justify-content: center; padding: 40px 0;
        }
        @keyframes gradientBG { 0% {background-position: 0% 50%;} 50% {background-position: 100% 50%;} 100% {background-position: 0% 50%;} }
        .card { 
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px);
            border-radius: 25px; border: 1px solid rgba(255, 255, 255, 0.3); 
            box-shadow: 0 25px 50px rgba(0,0,0,0.2); overflow: hidden; max-width: 850px; width: 95%;
        }
        .card-header-custom { background: linear-gradient(45deg, var(--my-blue), var(--my-green)); padding: 25px; text-align: center; color: white; }
        .btn-custom { padding: 12px 25px; border-radius: 12px; font-weight: 700; text-transform: uppercase; font-size: 0.8rem; transition: 0.3s; border: none; color: white; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-custom:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.2); color: white; }
        .btn-red { background: var(--my-red); } .btn-green { background: var(--my-green); }
        .form-label { font-weight: 700; color: #333; font-size: 0.8rem; }
        .form-control, .form-select { border-radius: 12px; border: 1.5px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.9); padding: 10px 15px; }
    </style>
</head>
<body>

<div class="card shadow-lg">
    <div class="card-header-custom">
        <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i> EDIT DATA PEGAWAI</h4>
        <small class="opacity-75">Perbarui informasi profil dan username akun</small>
    </div>
    
    <div class="card-body p-4 p-md-5">
        <form action="" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control" value="<?= $data['nip'] ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NAMA LENGKAP</label>
                    <input type="text" name="nama_pegawai" class="form-control" value="<?= $data['nama_pegawai'] ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">PANGKAT / GOL. RUANG</label>
                    <input type="text" name="pangkat" class="form-control" value="<?= $data['pangkat_gol_ruang'] ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">JABATAN</label>
                    <input type="text" name="jabatan" class="form-control" value="<?= $data['jabatan'] ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">TANGGAL LAHIR</label>
                    <input type="date" name="tgl_lahir" class="form-control" value="<?= $data['tanggal_lahir'] ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">JENIS KELAMIN</label>
                    <select name="jenis_kelamin" class="form-select">
                        <option value="laki-laki" <?= $data['jenis_kelamin'] == 'laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="perempuan" <?= $data['jenis_kelamin'] == 'perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">STATUS KEPEGAWAIAN</label>
                    <input type="text" name="status_kepegawaian" class="form-control" value="<?= $data['status_kepegawaian'] ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">USERNAME LOGIN</label>
                    <input type="text" name="username" class="form-control" value="<?= $data['username'] ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">ALAMAT DOMISILI</label>
                    <textarea name="alamat" class="form-control" rows="2"><?= $data['alamat'] ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                <a href="admin_dashboard.php?page=pegawai" class="btn-custom btn-red"><i class="bi bi-arrow-left"></i> Kembali</a>
                <button type="submit" name="update_pegawai" class="btn-custom btn-green">Simpan Perubahan <i class="bi bi-check-lg"></i></button>
            </div>
        </form>
    </div>
</div>

</body>
</html>