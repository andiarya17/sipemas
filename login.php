<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

// Cek jika user sudah login
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    redirect('user/dashboard.php');
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    redirect('admin/dashboard.php');
}

if ($_POST) {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        // Cek admin login
        $stmt = $pdo->prepare("SELECT * FROM tbl_admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && $password === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama'];
            redirect('admin/dashboard.php');
        } else {
            // Cek user login
            $stmt = $pdo->prepare("SELECT * FROM tbl_masyarakat WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && $password === $user['password']) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_nik'] = $user['NIK'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_nama'] = $user['nama'];
                redirect('user/dashboard.php');
            } else {
                $error = 'Username atau password salah!';
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
    <title>Login - SIPEMAS Desa Palandan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2><i class="fas fa-sign-in-alt"></i> Masuk ke SIPEMAS</h2>
                <p>Silakan masuk dengan akun Anda</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Masukkan username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Masukkan password" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p>Belum punya akun? 
                    <a href="register.php" class="text-decoration-none">
                        <strong>Daftar di sini</strong>
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
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (!validateForm('loginForm')) {
                e.preventDefault();
            } else {
                showLoading('loginBtn');
            }
        });
    </script>
</body>
</html>