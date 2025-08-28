<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

check_admin_login();

$stats = get_pengaduan_stats();

// Pengaduan terbaru
$stmt = $pdo->query("SELECT p.*, m.nama FROM tbl_pengaduan p 
                    JOIN tbl_masyarakat m ON p.NIK = m.NIK 
                    ORDER BY p.created_at DESC LIMIT 5");
$pengaduan_terbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIPEMAS</title>
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
            <h5 class="text-white"><i class="fas fa-user-shield"></i> Admin Panel</h5>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link" href="pengaduan.php">
                <i class="fas fa-clipboard-list"></i> Data Pengaduan
            </a>
            <a class="nav-link" href="laporan.php">
                <i class="fas fa-file-alt"></i> Laporan
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
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h1>
                <p>Selamat datang, <?= $_SESSION['admin_nama'] ?></p>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="pengaduan.php" class="text-decoration-none">
                        <div class="stats-card">
                            <h3><?= $stats['total'] ?></h3>
                            <p><i class="fas fa-clipboard-list"></i> Total Pengaduan</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="pengaduan.php?status=pending" class="text-decoration-none">
                        <div class="stats-card" style="background: linear-gradient(135deg, #FF9800 0%, #FFB74D 100%);">
                            <h3><?= $stats['pending'] ?></h3>
                            <p><i class="fas fa-clock"></i> Menunggu Verifikasi</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="pengaduan.php?status=process" class="text-decoration-none">
                        <div class="stats-card" style="background: linear-gradient(135deg, #2196F3 0%, #64B5F6 100%);">
                            <h3><?= $stats['process'] ?></h3>
                            <p><i class="fas fa-cog"></i> Sedang Diproses</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <a href="pengaduan.php?status=done" class="text-decoration-none">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4CAF50 0%, #81C784 100%);">
                            <h3><?= $stats['done'] ?></h3>
                            <p><i class="fas fa-check-circle"></i> Selesai</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Pengaduan Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Pengaduan Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php if ($pengaduan_terbaru): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Kategori</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pengaduan_terbaru as $index => $row): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['tgl_pengaduan'])) ?></td>
                                            <td><?= $row['nama'] ?></td>
                                            <td><?= $row['kategori_pengaduan'] ?></td>
                                            <td>
                                                <?php
                                                $status_class = 'status-' . $row['status'];
                                                $status_text = '';
                                                switch($row['status']) {
                                                    case 'pending': $status_text = 'Menunggu'; break;
                                                    case 'process': $status_text = 'Diproses'; break;
                                                    case 'done': $status_text = 'Selesai'; break;
                                                }
                                                ?>
                                                <span class="badge-status <?= $status_class ?>"><?= $status_text ?></span>
                                            </td>
                                            <td>
                                                <a href="pengaduan.php?detail=<?= $row['id_pengaduan'] ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="pengaduan.php" class="btn btn-success">
                                <i class="fas fa-list"></i> Lihat Semua Pengaduan
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada pengaduan</p>
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