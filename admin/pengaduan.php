<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

check_admin_login();

$message = '';
$error = '';

// Handle status update
if (isset($_POST['update_status'])) {
    $id_pengaduan = $_POST['id_pengaduan'];
    $status = $_POST['status'];
    
    if (update_pengaduan_status($id_pengaduan, $status)) {
        $message = 'Status pengaduan berhasil diupdate!';
    } else {
        $error = 'Gagal mengupdate status pengaduan!';
    }
}

// Handle add tanggapan
if (isset($_POST['add_tanggapan'])) {
    $id_pengaduan = $_POST['id_pengaduan'];
    $tanggapan = clean_input($_POST['tanggapan']);
    
    if (add_tanggapan($id_pengaduan, $tanggapan, $_SESSION['admin_id'])) {
        $message = 'Tanggapan berhasil ditambahkan!';
        // Update status ke process jika masih pending
        $stmt = $pdo->prepare("UPDATE tbl_pengaduan SET status = 'process' WHERE id_pengaduan = ? AND status = 'pending'");
        $stmt->execute([$id_pengaduan]);
    } else {
        $error = 'Gagal menambahkan tanggapan!';
    }
}

// Get all pengaduan
$pengaduan_list = get_all_pengaduan();

// Get detail pengaduan if requested
$detail_pengaduan = null;
$tanggapan_list = [];
if (isset($_GET['detail'])) {
    $stmt = $pdo->prepare("SELECT p.*, m.nama, m.alamat, m.telp FROM tbl_pengaduan p 
                          JOIN tbl_masyarakat m ON p.NIK = m.NIK 
                          WHERE p.id_pengaduan = ?");
    $stmt->execute([$_GET['detail']]);
    $detail_pengaduan = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
    <title>Data Pengaduan - SIPEMAS</title>
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
            <a class="nav-link active" href="pengaduan.php">
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
                                        <td><strong>Nama Pengadu:</strong></td>
                                        <td><?= $detail_pengaduan['nama'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>NIK:</strong></td>
                                        <td><?= $detail_pengaduan['NIK'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Alamat:</strong></td>
                                        <td><?= $detail_pengaduan['alamat'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Telepon:</strong></td>
                                        <td><?= $detail_pengaduan['telp'] ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Tanggal Pengaduan:</strong></td>
                                        <td><?= date('d/m/Y', strtotime($detail_pengaduan['tgl_pengaduan'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kategori:</strong></td>
                                        <td><?= $detail_pengaduan['kategori_pengaduan'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <?php
                                            $status_class = 'status-' . $detail_pengaduan['status'];
                                            $status_text = '';
                                            switch($detail_pengaduan['status']) {
                                                case 'pending': $status_text = 'Menunggu'; break;
                                                case 'process': $status_text = 'Diproses'; break;
                                                case 'done': $status_text = 'Selesai'; break;
                                            }
                                            ?>
                                            <span class="badge-status <?= $status_class ?>"><?= $status_text ?></span>
                                        </td>
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

                        <!-- Update Status -->
                        <div class="mt-4">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id_pengaduan" value="<?= $detail_pengaduan['id_pengaduan'] ?>">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Update Status:</label>
                                        <select name="status" class="form-control" required>
                                            <option value="pending" <?= $detail_pengaduan['status'] == 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                            <option value="process" <?= $detail_pengaduan['status'] == 'process' ? 'selected' : '' ?>>Diproses</option>
                                            <option value="done" <?= $detail_pengaduan['status'] == 'done' ? 'selected' : '' ?>>Selesai</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="update_status" class="btn btn-warning">
                                            <i class="fas fa-sync"></i> Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tanggapan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-comments"></i> Tanggapan</h5>
                    </div>
                    <div class="card-body">
                        <!-- Form Add Tanggapan -->
                        <form method="POST" class="mb-4">
                            <input type="hidden" name="id_pengaduan" value="<?= $detail_pengaduan['id_pengaduan'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Tambah Tanggapan:</label>
                                <textarea name="tanggapan" class="form-control" rows="4" 
                                          placeholder="Tulis tanggapan..." required></textarea>
                            </div>
                            <button type="submit" name="add_tanggapan" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Kirim Tanggapan
                            </button>
                        </form>

                        <!-- List Tanggapan -->
                        <?php if ($tanggapan_list): ?>
                            <?php foreach ($tanggapan_list as $tanggapan): ?>
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><i class="fas fa-user-shield"></i> <?= $tanggapan['admin_nama'] ?></strong>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($tanggapan['created_at'])) ?>
                                        </small>
                                    </div>
                                    <p class="mb-0"><?= nl2br($tanggapan['tanggapan']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">Belum ada tanggapan</p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- List Pengaduan -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-clipboard-list"></i> Data Pengaduan</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($pengaduan_list): ?>
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
                                        <?php foreach ($pengaduan_list as $index => $row): ?>
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
                                                    <a href="?detail=<?= $row['id_pengaduan'] ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada pengaduan</p>
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