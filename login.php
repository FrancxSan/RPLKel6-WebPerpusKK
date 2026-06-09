<?php
session_start();
include 'koneksi.php';
include 'header.php';
?>

<div class="container-login">
    <div class="card-login">
        <div class="text-center mb-4">


            <h2 style="color: #2f2f31ff; font-weight: bold;">📚Selamat Datang</h2>
            <p class="text-dark">Perpustakaan Daerah Kotamobagu</p>
        </div>

        <?php if (isset($_GET['pesan'])) : ?>
            <div id="notif-area" style="position: absolute; top: 10px; left: 50%; transform: translateX(-50%); z-index: 9999; width: 100%; max-width: 400px; padding: 20px;" class="mb-4">
                <?php if ($_GET['pesan'] == 'logout_berhasil') : ?>
                    <div class="alert alert-success border-0 shadow-sm text-center py-2" style="background-color: #d1edda; color: #155724;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Logout Berhasil!</strong> Sesi Anda telah diakhiri.
                    </div>
                <?php elseif ($_GET['pesan'] == 'gagal') : ?>
                    <div style="background-color: 
                #f8d7da; color: #721c24; padding: 10px;
                 border-radius: 5px; margin-bottom: 15px; 
                 text-align: center; border: 1px solid #f84c4c;">
                        <storng>login Gagal!</strong>
                            Username atau Password Salah!
                    </div>
                <?php endif; ?>
            </div>

            <script>
                setTimeout(function() {
                    var el = document.getElementById('notif-area');
                    if (el) {
                        el.style.transition = "all 0.6s ease";
                        el.style.opacity = "0";
                        el.style.top = "0px";
                        setTimeout(function() {
                            el.style.display = 'none';
                        }, 600);
                    }
                }, 3000);
            </script>
        <?php endif; ?>

        <form method="POST" action="proses_login.php">
            <div class="mb-3">
                <label class="form-label text-dark">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-dark">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" name="login" class="btn btn-custom w-100 shadow-sm">MASUK KE SISTEM</button>
        </form>

        <div class="text-center mt-4">
            <small class="text-dark">© 2026 Perpustakaan Digital</small>
        </div>
    </div>
</div>

</body>

</html>