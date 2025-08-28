<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_POST) {
    $nik = clean_input($_POST['nik']);
    $nama = clean_input($_POST['nama']);
    $alamat = clean_input($_POST['alamat']);
    $telp = clean_input($_POST['telp']);
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);
    
    // Validasi
    if (empty($nik) || empty($nama) || empty($alamat) || empty($telp) || 
        empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif (strlen($nik) != 16) {
        $error = 'NIK harus 16 digit!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek NIK sudah ada
        $stmt = $pdo->prepare("SELECT NIK FROM tbl_masyarakat WHERE NIK = ?");
        $stmt->execute([$nik]);
        if ($stmt->fetch()) {
            $error = 'NIK sudah terdaftar!';
        } else {
            // Cek username sudah ada
            $stmt = $pdo->prepare("SELECT username FROM tbl_masyarakat WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username sudah digunakan!';
            } else {
                // Insert data
                try {
                    $stmt = $pdo->prepare("INSERT INTO tbl_masyarakat (NIK, nama, alamat, username, telp, password) 
                                          VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nik, $nama, $alamat, $username, $telp, $password]);
                    $success = 'Pendaftaran berhasil! Silakan login.';
                } catch(PDOException $e) {
                    $error = 'Pendaftaran gagal: ' . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SIPEMAS Desa Palandan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="register-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2><i class="fas fa-user-plus"></i> Daftar SIPEMAS</h2>
                <p>Buat akun baru untuk mengajukan pengaduan</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                    <br><a href="login.php" class="btn btn-success btn-sm mt-2">Login Sekarang</a>
                </div>
            <?php endif; ?>

            <form method="POST" id="registerForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nik" class="form-label">
                            <i class="fas fa-id-card"></i> NIK
                        </label>
                        <input type="text" class="form-control" id="nik" name="nik" 
                               placeholder="16 digit NIK" maxlength="16" required
                               value="<?= isset($_POST['nik']) ? $_POST['nik'] : '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">
                            <i class="fas fa-user"></i> Nama Lengkap
                        </label>
                        <input type="text" class="form-control" id="nama" name="nama" 
                               placeholder="Nama lengkap" required
                               value="<?= isset($_POST['nama']) ? $_POST['nama'] : '' ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Alamat
                    </label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" 
                              placeholder="Alamat lengkap" required><?= isset($_POST['alamat']) ? $_POST['alamat'] : '' ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telp" class="form-label">
                            <i class="fas fa-phone"></i> No. Telepon
                        </label>
                        <input type="tel" class="form-control" id="telp" name="telp" 
                               placeholder="08xxxxxxxxxx" required
                               value="<?= isset($_POST['telp']) ? $_POST['telp'] : '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user-circle"></i> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Username" required
                               value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock"></i> Konfirmasi Password
                        </label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Ulangi password" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success" id="registerBtn">
                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p>Sudah punya akun? 
                    <a href="login.php" class="text-decoration-none">
                        <strong>Login di sini</strong>
                    </a>
                </p>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        // NIK only numbers
        document.getElementById('nik').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });

        // Phone format
        document.getElementById('telp').addEventListener('input', function() {
            formatPhone(this);
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            if (!validateForm('registerForm')) {
                e.preventDefault();
            } else {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Password dan konfirmasi password tidak sama!');
                    return;
                }
                
                showLoading('registerBtn');
            }
        });
    </script>
</body>
</html>