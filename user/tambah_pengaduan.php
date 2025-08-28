<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

check_user_login();

$message = '';
$error = '';

if ($_POST) {
    $nama_pengaduan = clean_input($_POST['nama_pengaduan']);
    $kategori_pengaduan = clean_input($_POST['kategori_pengaduan']);
    $detail_pengaduan = clean_input($_POST['detail_pengaduan']);
    $telp = clean_input($_POST['telp']);
    
    // Validasi
    if (empty($nama_pengaduan) || empty($kategori_pengaduan) || empty($detail_pengaduan) || empty($telp)) {
        $error = 'Semua field harus diisi!';
    } else {
        $foto = null;
        
        // Handle file upload dengan path yang benar
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            // Path upload disesuaikan karena script berada di subfolder user/
            $upload_path = '../assets/uploads/';
            
            // Buat folder jika belum ada
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }
            
            $foto = upload_file($_FILES['foto'], $upload_path);
            if (!$foto) {
                $error = 'Gagal mengupload foto! Pastikan folder assets/uploads/ ada dan memiliki permission write.';
            }
        }
        
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO tbl_pengaduan 
                                      (tgl_pengaduan, nama_pengaduan, NIK, kategori_pengaduan, detail_pengaduan, telp, foto) 
                                      VALUES (CURDATE(), ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nama_pengaduan, $_SESSION['user_nik'], $kategori_pengaduan, $detail_pengaduan, $telp, $foto]);
                
                $message = 'Pengaduan berhasil diajukan! Silakan tunggu verifikasi dari admin.';
                
                // Reset form
                $_POST = array();
            } catch(PDOException $e) {
                $error = 'Gagal mengajukan pengaduan: ' . $e->getMessage();
            }
        }
    }
}

// Get user data for form defaults
$stmt = $pdo->prepare("SELECT * FROM tbl_masyarakat WHERE NIK = ?");
$stmt->execute([$_SESSION['user_nik']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengaduan - SIPEMAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3">
            <h5 class="text-white"><i class="fas fa-user"></i> Panel Masyarakat</h5>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link" href="pengaduan.php">
                <i class="fas fa-clipboard-list"></i> Pengaduan Saya
            </a>
            <a class="nav-link active" href="tambah_pengaduan.php">
                <i class="fas fa-plus-circle"></i> Tambah Pengaduan
            </a>
            <a class="nav-link" href="profil.php">
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
                            <h5><i class="fas fa-plus-circle"></i> Tambah Pengaduan Baru</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> <?= $message ?>
                                    <br><a href="pengaduan.php" class="btn btn-success btn-sm mt-2">Lihat Pengaduan Saya</a>
                                </div>
                            <?php endif; ?>

                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" enctype="multipart/form-data" id="pengaduanForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nama_pengaduan" class="form-label">
                                            <i class="fas fa-heading"></i> Nama/Judul Pengaduan
                                        </label>
                                        <input type="text" class="form-control" id="nama_pengaduan" name="nama_pengaduan" 
                                               placeholder="Masukkan judul pengaduan" required
                                               value="<?= isset($_POST['nama_pengaduan']) ? htmlspecialchars($_POST['nama_pengaduan']) : '' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="kategori_pengaduan" class="form-label">
                                            <i class="fas fa-tags"></i> Kategori Pengaduan
                                        </label>
                                        <select class="form-control" id="kategori_pengaduan" name="kategori_pengaduan" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="Infrastruktur" <?= (isset($_POST['kategori_pengaduan']) && $_POST['kategori_pengaduan'] == 'Infrastruktur') ? 'selected' : '' ?>>Infrastruktur</option>
                                            <option value="Pelayanan Publik" <?= (isset($_POST['kategori_pengaduan']) && $_POST['kategori_pengaduan'] == 'Pelayanan Publik') ? 'selected' : '' ?>>Pelayanan Publik</option>
                                            <option value="Keamanan" <?= (isset($_POST['kategori_pengaduan']) && $_POST['kategori_pengaduan'] == 'Keamanan') ? 'selected' : '' ?>>Keamanan</option>
                                            <option value="Kebersihan" <?= (isset($_POST['kategori_pengaduan']) && $_POST['kategori_pengaduan'] == 'Kebersihan') ? 'selected' : '' ?>>Kebersihan</option>
                                            <option value="Kesehatan" <?= (isset($_POST['kategori_pengaduan']) && $_POST['kategori_pengaduan'] == 'Kesehatan') ? 'selected' : '' ?>>Kesehatan</option>
                                            <option value="Pendidikan" <?= (isset($_POST['kategori_pengaduan']) && $_POST['kategori_pengaduan'] == 'Pendidikan') ? 'selected' : '' ?>>Pendidikan</option>
                                            <option value="Lainnya" <?= (isset($_POST['kategori_pengaduan']) && $_POST['kategori_pengaduan'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="telp" class="form-label">
                                        <i class="fas fa-phone"></i> No. Telepon
                                    </label>
                                    <input type="tel" class="form-control" id="telp" name="telp" 
                                           placeholder="08xxxxxxxxxx" required
                                           value="<?= isset($_POST['telp']) ? htmlspecialchars($_POST['telp']) : htmlspecialchars($user_data['telp']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="detail_pengaduan" class="form-label">
                                        <i class="fas fa-edit"></i> Detail Pengaduan
                                    </label>
                                    <textarea class="form-control" id="detail_pengaduan" name="detail_pengaduan" 
                                              rows="6" placeholder="Jelaskan detail pengaduan Anda dengan lengkap..." 
                                              required maxlength="1000" 
                                              onkeyup="updateCharCount(this, 'charCount', 1000)"><?= isset($_POST['detail_pengaduan']) ? htmlspecialchars($_POST['detail_pengaduan']) : '' ?></textarea>
                                    <small id="charCount" class="text-muted">1000 karakter tersisa</small>
                                </div>

                                <div class="mb-3">
                                    <label for="foto" class="form-label">
                                        <i class="fas fa-camera"></i> Foto Pendukung (Opsional)
                                    </label>
                                    <input type="file" class="form-control" id="foto" name="foto" 
                                           accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewImage(this)">
                                    <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB</small>
                                    <div class="mt-2">
                                        <img id="imagePreview" src="" alt="Preview" 
                                             style="display: none; max-width: 200px; max-height: 200px;" 
                                             class="img-thumbnail">
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Informasi:</strong> Pengaduan yang Anda kirim akan diverifikasi terlebih dahulu oleh admin. 
                                    Anda akan mendapat notifikasi status melalui dashboard Anda.
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="dashboard.php" class="btn btn-secondary me-md-2">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-success" id="submitBtn">
                                        <i class="fas fa-paper-plane"></i> Kirim Pengaduan
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
    <script>
        // Preview image function
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        // Character count function
        function updateCharCount(textarea, counterId, maxLength) {
            const remaining = maxLength - textarea.value.length;
            document.getElementById(counterId).textContent = remaining + ' karakter tersisa';
            if (remaining < 50) {
                document.getElementById(counterId).className = 'text-warning';
            } else if (remaining < 0) {
                document.getElementById(counterId).className = 'text-danger';
            } else {
                document.getElementById(counterId).className = 'text-muted';
            }
        }

        // Phone format function  
        function formatPhone(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = value;
            } else if (value.startsWith('62')) {
                value = '0' + value.substring(2);
            }
            input.value = value;
        }

        // Form validation function
        function validateForm(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            return isValid;
        }

        // Show loading function
        function showLoading(buttonId) {
            const button = document.getElementById(buttonId);
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            button.disabled = true;
        }

        // Event listeners
        document.getElementById('telp').addEventListener('input', function() {
            formatPhone(this);
        });

        document.getElementById('pengaduanForm').addEventListener('submit', function(e) {
            if (!validateForm('pengaduanForm')) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
            } else {
                showLoading('submitBtn');
            }
        });

        // Initialize character count
        updateCharCount(document.getElementById('detail_pengaduan'), 'charCount', 1000);
    </script>
</body>
</html>