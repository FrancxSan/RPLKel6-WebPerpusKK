<?php
include '../koneksi.php';

$tgl_mulai = $_GET['mulai'] ?? date('Y-m-01');


$tgl_selesai = $_GET['selesai'] ?? date('Y-m-d');

$sql = "SELECT a.*, ak.username FROM absensi a 
        JOIN akun ak ON a.id_pegawai = ak.id_akun 
        WHERE a.tanggal_absensi BETWEEN '$tgl_mulai' AND '$tgl_selesai'
        ORDER BY a.tanggal_absensi DESC";
$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan Absensi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>LAPORAN ABSENSI PEGAWAI PERPUSTAKAAN</h2>
        <p>Periode: <?= date('d F Y', strtotime($tgl_mulai)) ?> s/d <?= date('d F Y', strtotime($tgl_selesai)) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($row = mysqli_fetch_assoc($query)) : 
                $telat = (strtotime($row['jam_masuk']) > strtotime('08:00:00')) ? 'Terlambat' : 'Tepat Waktu';
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_absensi'])) ?></td>
                <td><?= $row['jam_masuk'] ?></td>
                <td><?= $row['jam_pulang'] ?? '-' ?></td>
                <td><?= $telat ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
        <br><br><br>
        <p>(......................................)</p>
        <p>Kepala Perpustakaan</p>
    </div>

</body>
</html>