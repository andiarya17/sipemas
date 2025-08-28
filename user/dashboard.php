<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

check_user_login();

// Get user pengaduan
$pengaduan_user = get_pengaduan_by_nik($_SESSION['user_nik']);

// Get user stats
$stats_user = array();
$stats_user['total'] = count($pengaduan_user);
$stats_user['pending'] = count(array_filter($pengaduan_user, function($p) { return $p['status'] == 'pending'; }));
$stats_user['process'] = count(array_filter($pengaduan_user, function($p) { return $p['status'] == 'process'; }));
$stats_user['done'] = count(array_filter($pengaduan_user, function($p) { return $p['status'] == 'done'; }));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIPEMAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .stats-card {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        a.text-decoration-none:hover .stats-card h3,
        a.text-decoration-none:hover .stats-card p {
            color: inherit;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3">
            <h5 class="text-white"><i class="fas fa-user"></i> Panel Masyarakat</h5>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link" href="pengaduan.php">
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
            <!-- Welcome -->
            <div class="welcome-hero mb-4">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <p>Selamat datang, <?= $_SESSION['user_nama'] ?></p>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="pengaduan.php" class="text-decoration-none">
                        <div class="stats-card">
                            <h3><?= $stats_user['total'] ?></h3>
                            <p><i class="fas fa-clipboard-list"></i> Total Pengaduan</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="pengaduan.php?status=pending" class="text-decoration-none">
                        <div class="stats-card" style="background: linear-gradient(135deg, #FF9800 0%, #FFB74D 100%);">
                            <h3><?= $stats_user['pending'] ?></h3>
                            <p><i class="fas fa-clock"></i> Menunggu Verifikasi</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="pengaduan.php?status=process" class="text-decoration-none">
                        <div class="stats-card" style="background: linear-gradient(135deg, #2196F3 0%, #64B5F6 100%);">
                            <h3><?= $stats_user['process'] ?></h3>
                            <p><i class="fas fa-cog"></i> Sedang Diproses</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="pengaduan.php?status=done" class="text-decoration-none">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4CAF50 0%, #81C784 100%);">
                            <h3><?= $stats_user['done'] ?></h3>
                            <p><i class="fas fa-check-circle"></i> Selesai</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                            <h5>Buat Pengaduan Baru</h5>
                            <p class="text-muted">Sampaikan keluhan atau saran Anda kepada pemerintah desa</p>
                            <a href="tambah_pengaduan.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Tambah Pengaduan
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-list fa-3x text-primary mb-3"></i>
                            <h5>Lihat Pengaduan Saya</h5>
                            <p class="text-muted">Monitor status dan perkembangan pengaduan yang telah Anda ajukan</p>
                            <a href="pengaduan.php" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Lihat Pengaduan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengaduan Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history"></i> Pengaduan Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php if ($pengaduan_user): ?>
                        <?php $latest_pengaduan = array_slice($pengaduan_user, 0, 5); ?>
                        <?php foreach ($latest_pengaduan as $pengaduan): ?>
                            <div class="pengaduan-card card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title">
                                                <i class="fas fa-calendar"></i> 
                                                <?= date('d/m/Y', strtotime($pengaduan['tgl_pengaduan'])) ?>
                                            </h6>
                                            <p class="card-text">
                                                <strong>Kategori:</strong> <?= $pengaduan['kategori_pengaduan'] ?><br>
                                                <strong>Detail:</strong> <?= substr($pengaduan['detail_pengaduan'], 0, 100) ?>...
                                            </p>
                                        </div>
                                        <div class="text-end">
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
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="text-center mt-3">
                            <a href="pengaduan.php" class="btn btn-success">
                                <i class="fas fa-list"></i> Lihat Semua Pengaduan
                            </a>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>