<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

check_admin_login();

$message = '';
$error = '';

if ($_POST) {
    $username = clean_input($_POST['username']);
    $nama = clean_input($_POST['nama']);
    $password_lama = clean_input($_POST['password_lama']);
    $password_baru = clean_input($_POST['password_baru']);
    $konfirmasi_password = clean_input($_POST['konfirmasi_password']);
    
    // Validasi
    if (empty($username) || empty($nama)) {
        $error = 'Username dan nama harus diisi!';
    } else {
        // Cek password lama jika ingin ganti password
        if (!empty($password_baru)) {
            if (empty($password_lama)) {
                $error = 'Password lama harus diisi!';
            } elseif ($password_baru !== $konfirmasi_password) {
                $error = 'Password baru dan konfirmasi tidak sama!';
            } elseif (strlen($password_baru) < 6) {
                $error = 'Password baru minimal 6 karakter!';
            } else {
                // Verifikasi password lama
                $stmt = $pdo->prepare("SELECT password FROM tbl_admin WHERE admin_id = ?");
                $stmt->execute([$_SESSION['admin_id']]);
                $current_password = $stmt->fetchColumn();
                
                if ($password_lama !== $current_password) {
                    $error = 'Password lama salah!';
                }
            }
        }
        
        if (empty($error)) {
            try {
                if (!empty($password_baru)) {
                    // Update dengan password baru
                    $stmt = $pdo->prepare("UPDATE tbl_admin SET username = ?, nama = ?, password = ? WHERE admin_id = ?");
                    $stmt->execute([$username, $nama, $password_baru, $_SESSION['admin_id']]);
                } else {
                    // Update tanpa password
                    $stmt = $pdo->prepare("UPDATE tbl_admin SET username = ?, nama = ? WHERE admin_id = ?");
                    $stmt->execute([$username, $nama, $_SESSION['admin_id']]);
                }
                
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_nama'] = $nama;
                $message = 'Profil berhasil diupdate!';
            } catch(PDOException $e) {
                $error = 'Gagal mengupdate profil: ' . $e->getMessage();
            }
        }
    }
}

// Get current admin data
$stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE admin_id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - SIPEMAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3">
            <h5 class="text-white"><i class="fas fa-user-shield"></i> Admin Panel</h5>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link" href="pengaduan.php">
                <i class="fas fa-clipboard-list"></i> Data Pengaduan
            </a>
            <a class="nav-link" href="laporan.php">
                <i class="fas fa-file-alt"></i> Laporan
            </a>
            <a class="nav-link active" href="profil.php">
                <i class="fas fa-user"></i> Profil
            </a>
            <a class="nav-link" href="../logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Content -->
    <div class="content-with-sidebar">
        <?php include '../includes/header.php'; ?>

        <div class="container-fluid mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-user"></i> Profil Admin</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> <?= $message ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-user-circle"></i> Username
                                        </label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?= $admin_data['username'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nama" class="form-label">
                                            <i class="fas fa-user"></i> Nama Lengkap
                                        </label>
                                        <input type="text" class="form-control" id="nama" name="nama" 
                                               value="<?= $admin_data['nama'] ?>" required>
                                    </div>
                                </div>

                                <hr>
                                <h6><i class="fas fa-key"></i> Ganti Password (Opsional)</h6>
                                <p class="text-muted">Kosongkan jika tidak ingin mengganti password</p>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="password_lama" class="form-label">
                                            <i class="fas fa-lock"></i> Password Lama
                                        </label>
                                        <input type="password" class="form-control" id="password_lama" name="password_lama">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="password_baru" class="form-label">
                                            <i class="fas fa-lock"></i> Password Baru
                                        </label>
                                        <input type="password" class="form-control" id="password_baru" name="password_baru">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="konfirmasi_password" class="form-label">
                                            <i class="fas fa-lock"></i> Konfirmasi Password
                                        </label>
                                        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password">
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>