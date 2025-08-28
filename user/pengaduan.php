<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

check_user_login();

// Get user pengaduan
$pengaduan_user = get_pengaduan_by_nik($_SESSION['user_nik']);

// Get detail pengaduan if requested
$detail_pengaduan = null;
$tanggapan_list = [];
if (isset($_GET['detail'])) {
    foreach ($pengaduan_user as $pengaduan) {
        if ($pengaduan['id_pengaduan'] == $_GET['detail']) {
            $detail_pengaduan = $pengaduan;
            break;
        }
    }
    
    if ($detail_pengaduan) {
        // Get tanggapan
        $stmt = $pdo->prepare("SELECT t.*, a.nama as admin_nama FROM tbl_tanggapan t 
                              JOIN tbl_admin a ON t.admin_id = a.admin_id 
                              WHERE t.id_pengaduan = ? ORDER BY t.created_at DESC");
        $stmt->execute([$_GET['detail']]);
        $tanggapan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Saya - SIPEMAS</title>
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
            <a class="nav-link active" href="pengaduan.php">
                <i class="fas fa-clipboard-list"></i> Pengaduan Saya
            </a>
            <a class="nav-link" href="tambah_pengaduan.php">
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
            <?php if ($detail_pengaduan): ?>
                <!-- Detail Pengaduan -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-info-circle"></i> Detail Pengaduan #<?= $detail_pengaduan['id_pengaduan'] ?></h5>
                        <a href="pengaduan.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Tanggal Pengaduan:</strong></td>
                                        <td><?= date('d/m/Y', strtotime($detail_pengaduan['tgl_pengaduan'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Pengaduan:</strong></td>
                                        <td><?= $detail_pengaduan['nama_pengaduan'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kategori:</strong></td>
                                        <td><?= $detail_pengaduan['kategori_pengaduan'] ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <?php
                                            $status_class = 'status-' . $detail_pengaduan['status'];
                                            $status_text = '';
                                            switch($detail_pengaduan['status']) {
                                                case 'pending': $status_text = 'Menunggu Verifikasi'; break;
                                                case 'process': $status_text = 'Sedang Diproses'; break;
                                                case 'done': $status_text = 'Selesai'; break;
                                            }
                                            ?>
                                            <span class="badge-status <?= $status_class ?>"><?= $status_text ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Telepon:</strong></td>
                                        <td><?= $detail_pengaduan['telp'] ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6><strong>Detail Pengaduan:</strong></h6>
                            <p class="bg-light p-3 rounded"><?= nl2br($detail_pengaduan['detail_pengaduan']) ?></p>
                        </div>

                        <?php if ($detail_pengaduan['foto']): ?>
                            <div class="mt-3">
                                <h6><strong>Foto Pendukung:</strong></h6>
                                <img src="../assets/uploads/<?= $detail_pengaduan['foto'] ?>" 
                                     class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tanggapan -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-comments"></i> Tanggapan dari Pemerintah Desa</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($tanggapan_list): ?>
                            <?php foreach ($tanggapan_list as $tanggapan): ?>
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><i class="fas fa-user-shield text-success"></i> <?= $tanggapan['admin_nama'] ?></strong>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($tanggapan['created_at'])) ?>
                                        </small>
                                    </div>
                                    <p class="mb-0"><?= nl2br($tanggapan['tanggapan']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-hourglass-half fa-3x text-muted mb-3"></i>
                                <p class="text-muted">
                                    <?php if ($detail_pengaduan['status'] == 'pending'): ?>
                                        Pengaduan Anda sedang menunggu verifikasi dari admin
                                    <?php elseif ($detail_pengaduan['status'] == 'process'): ?>
                                        Pengaduan Anda sedang diproses, silakan tunggu tanggapan
                                    <?php else: ?>
                                        Belum ada tanggapan
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- List Pengaduan -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-clipboard-list"></i> Pengaduan Saya</h5>
                        <a href="tambah_pengaduan.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Tambah Pengaduan
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($pengaduan_user): ?>
                            <div class="row">
                                <?php foreach ($pengaduan_user as $pengaduan): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="pengaduan-card card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title">
                                                        <i class="fas fa-calendar"></i> 
                                                        <?= date('d/m/Y', strtotime($pengaduan['tgl_pengaduan'])) ?>
                                                    </h6>
                                                    <?php
                                                    $status_class = 'status-' . $pengaduan['status'];
                                                    $status_text = '';
                                                    switch($pengaduan['status']) {
                                                        case 'pending': $status_text = 'Menunggu'; break;
                                                        case 'process': $status_text = 'Diproses'; break;
                                                        case 'done': $status_text = 'Selesai'; break;
                                                    }
                                                    ?>
                                                    <span class="badge-status <?= $status_class ?>"><?= $status_text ?></span>
                                                </div>
                                                
                                                <p class="card-text">
                                                    <strong>Kategori:</strong> <?= $pengaduan['kategori_pengaduan'] ?><br>
                                                    <strong>Detail:</strong> <?= substr($pengaduan['detail_pengaduan'], 0, 100) ?>...
                                                </p>
                                                
                                                <a href="?detail=<?= $pengaduan['id_pengaduan'] ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Anda belum memiliki pengaduan</p>
                                <a href="tambah_pengaduan.php" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Buat Pengaduan Pertama
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>