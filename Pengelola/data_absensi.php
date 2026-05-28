<?php
include '../koneksi.php';

$sql = "SELECT absensi.*, akun.nama 
        FROM absensi 
        JOIN akun ON absensi.id_pegawai = akun.id_akun 
        ORDER BY absensi.tanggal_absensi DESC, absensi.jam_masuk DESC";
$query = mysqli_query($conn, $sql);
?>

<div class="content-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark"><i class="bi bi-journal-check me-2 text-primary"></i> Data Absensi Pegawai</h4>
        <span class="badge bg-primary px-3 py-2"><?= date('d F Y') ?></span>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Pegawai</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th> <th class="text-center">Bukti Foto</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0) : ?>
                            <?php while($row = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-2 me-2 text-primary fw-bold" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                            <?= substr($row['nama'], 0, 1) ?>
                                        </div>
                                        <span class="fw-bold"><?= $row['nama'] ?></span>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_absensi'])) ?></td>
                                <td><span class="badge bg-success opacity-75"><?= $row['jam_masuk'] ?></span></td>
                                <td>
                                    <?php if ($row['jam_pulang']) : ?>
                                        <span class="badge bg-danger opacity-75"><?= $row['jam_pulang'] ?></span>
                                    <?php else : ?>
                                        <span class="text-muted small"><i>Belum Pulang</i></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <img src="../img/absensi/<?= $row['foto_absensi'] ?>" class="rounded shadow-sm" style="width: 45px; height: 45px; object-fit: cover;" onerror="this.src='https://placehold.co/45x45?text=NA'">
                                </td>
                                <td class="text-center">
                                    <?php if ($row['jam_pulang']) : ?>
                                        <span class="badge rounded-pill bg-primary" style="font-size: 0.75rem;">Selesai</span>
                                    <?php else : ?>
                                        <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.75rem;">Sedang Bekerja</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada data absensi yang masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>