<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

check_admin_login();

// Filter
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Query pengaduan berdasarkan filter
$where_clause = "WHERE MONTH(p.tgl_pengaduan) = ? AND YEAR(p.tgl_pengaduan) = ?";
$params = [$bulan, $tahun];

if ($status !== 'all') {
    $where_clause .= " AND p.status = ?";
    $params[] = $status;
}

$stmt = $pdo->prepare("SELECT p.*, m.nama FROM tbl_pengaduan p 
                      JOIN tbl_masyarakat m ON p.NIK = m.NIK 
                      $where_clause 
                      ORDER BY p.tgl_pengaduan DESC");
$stmt->execute($params);
$laporan_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistik untuk laporan
$stats_stmt = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'process' THEN 1 ELSE 0 END) as process,
    SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as done
    FROM tbl_pengaduan 
    WHERE MONTH(tgl_pengaduan) = ? AND YEAR(tgl_pengaduan) = ?");
$stats_stmt->execute([$bulan, $tahun]);
$stats_laporan = $stats_stmt->fetch(PDO::FETCH_ASSOC);

$bulan_names = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengaduan <?= $bulan_names[$bulan] ?> <?= $tahun ?> - SIPEMAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        /* Print Styles */
        @media print {
            /* Hide unnecessary elements */
            .sidebar, .navbar, .btn, .card-header .btn, .alert, 
            .stats-card, .screen-text, .no-print {
                display: none !important;
            }
            
            /* Show print-only elements */
            .print-header, .print-footer, .print-text {
                display: block !important;
            }
            
            /* Layout adjustments */
            .content-with-sidebar {
                margin-left: 0 !important;
                padding: 10px !important;
            }
            
            .container-fluid {
                padding: 0 !important;
            }
            
            .card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
            }
            
            .card-header {
                display: none !important;
            }
            
            .card-body {
                padding: 0 !important;
            }
            
            /* Table styling for print */
            .table {
                font-size: 10px !important;
                border-collapse: collapse !important;
                width: 100% !important;
                margin: 0 !important;
            }
            
            .table th, .table td {
                border: 1px solid #000 !important;
                padding: 3px !important;
                vertical-align: top !important;
                word-wrap: break-word !important;
                line-height: 1.2 !important;
            }
            
            .table thead th {
                background-color: #f0f0f0 !important;
                font-weight: bold !important;
                text-align: center !important;
                color: #000 !important;
                font-size: 9px !important;
            }
            
            .table tbody td {
                color: #000 !important;
                font-size: 9px !important;
            }
            
            /* Status styling for print */
            .print-status {
                padding: 1px 4px !important;
                border: 1px solid #000 !important;
                border-radius: 2px !important;
                font-size: 8px !important;
                font-weight: bold !important;
                background: #f0f0f0 !important;
                color: #000 !important;
                display: inline-block !important;
            }
            
            /* Print header styling */
            .print-header h3, .print-header h4 {
                margin: 0 !important;
                color: #000 !important;
                font-weight: bold !important;
            }
            
            .print-header h3 {
                font-size: 16px !important;
                text-transform: uppercase !important;
            }
            
            .print-header h4 {
                font-size: 14px !important;
                text-transform: uppercase !important;
            }
            
            .print-header p {
                margin: 2px 0 !important;
                font-size: 11px !important;
                color: #000 !important;
            }
            
            /* Statistics box for print */
            .print-stats {
                margin: 15px 0 !important;
            }
            
            .print-stats .border {
                border: 1px solid #000 !important;
                padding: 8px !important;
                text-align: center !important;
            }
            
            .print-stats strong {
                font-size: 14px !important;
                display: block !important;
                margin-bottom: 2px !important;
            }
            
            .print-stats small {
                font-size: 9px !important;
                color: #000 !important;
            }
            
            /* Print footer styling */
            .print-footer {
                margin-top: 30px !important;
                page-break-inside: avoid !important;
            }
            
            .print-footer p {
                margin: 2px 0 !important;
                color: #000 !important;
                font-size: 10px !important;
            }
            
            .print-footer strong {
                font-weight: bold !important;
            }
            
            .print-footer small {
                font-size: 8px !important;
                color: #666 !important;
            }
            
            /* Signature boxes */
            .signature-box {
                text-align: center !important;
                min-height: 80px !important;
            }
            
            .signature-line {
                border-bottom: 1px solid #000 !important;
                margin: 50px 20px 5px 20px !important;
                display: inline-block !important;
                width: 150px !important;
            }
            
            /* Page break controls */
            .page-break {
                page-break-before: always !important;
            }
            
            .no-break {
                page-break-inside: avoid !important;
            }
            
            /* Ensure table doesn't break awkwardly */
            .table tbody tr {
                page-break-inside: avoid !important;
            }
            
            /* Full text display for print */
            .print-full-text .print-text {
                display: block !important;
                font-size: 8px !important;
                line-height: 1.1 !important;
                max-height: none !important;
                overflow: visible !important;
            }
            
            /* Print margins */
            @page {
                margin: 1.5cm 1cm;
                size: A4;
            }
            
            body {
                margin: 0 !important;
                padding: 0 !important;
                font-family: 'Times New Roman', serif !important;
                color: #000 !important;
                background: white !important;
                -webkit-print-color-adjust: exact !important;
            }
            
            /* Column widths for print */
            .col-no { width: 5% !important; }
            .col-tanggal { width: 8% !important; }
            .col-nik { width: 12% !important; }
            .col-nama { width: 15% !important; }
            .col-kategori { width: 12% !important; }
            .col-detail { width: 38% !important; }
            .col-status { width: 10% !important; }
        }
        
        /* Screen-only styles */
        @media screen {
            .print-header, .print-footer, .print-text {
                display: none !important;
            }
            
            .screen-text {
                display: inline !important;
            }
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
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link" href="pengaduan.php">
                <i class="fas fa-clipboard-list"></i> Data Pengaduan
            </a>
            <a class="nav-link active" href="laporan.php">
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
            <!-- Filter -->
            <div class="card mb-4 no-print">
                <div class="card-header">
                    <h5><i class="fas fa-filter"></i> Filter Laporan</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Bulan:</label>
                            <select name="bulan" class="form-control">
                                <?php foreach ($bulan_names as $num => $name): ?>
                                    <option value="<?= $num ?>" <?= $bulan == $num ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tahun:</label>
                            <select name="tahun" class="form-control">
                                <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                                    <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status:</label>
                            <select name="status" class="form-control">
                                <option value="all" <?= $status == 'all' ? 'selected' : '' ?>>Semua Status</option>
                                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="process" <?= $status == 'process' ? 'selected' : '' ?>>Diproses</option>
                                <option value="done" <?= $status == 'done' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <button type="button" onclick="window.print()" class="btn btn-success">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistik Laporan -->
            <div class="row mb-4 no-print">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card">
                        <h3><?= $stats_laporan['total'] ?></h3>
                        <p><i class="fas fa-clipboard-list"></i> Total Pengaduan</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #FF9800 0%, #FFB74D 100%);">
                        <h3><?= $stats_laporan['pending'] ?></h3>
                        <p><i class="fas fa-clock"></i> Menunggu</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #2196F3 0%, #64B5F6 100%);">
                        <h3><?= $stats_laporan['process'] ?></h3>
                        <p><i class="fas fa-cog"></i> Diproses</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #4CAF50 0%, #81C784 100%);">
                        <h3><?= $stats_laporan['done'] ?></h3>
                        <p><i class="fas fa-check-circle"></i> Selesai</p>
                    </div>
                </div>
            </div>

            <!-- Laporan -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-alt"></i> 
                        Laporan Pengaduan <?= $bulan_names[$bulan] ?> <?= $tahun ?>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Header Laporan untuk Print -->
                    <div class="print-header d-none">
                        <div class="text-center mb-4">
                            <h3 class="mb-1">LAPORAN PENGADUAN MASYARAKAT</h3>
                            <h4 class="mb-1">DESA PALANDAN</h4>
                            <p class="mb-0">Kecamatan Baebunta, Kabupaten Luwu Utara</p>
                            <p class="mb-0">Provinsi Sulawesi Selatan</p>
                            <p class="mt-2 mb-0"><strong>Periode: <?= $bulan_names[$bulan] ?> <?= $tahun ?></strong></p>
                            <?php if ($status !== 'all'): ?>
                                <p class="mb-0"><small>Filter Status: <?= ucfirst($status) ?></small></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Ringkasan Statistik untuk Print -->
                        <div class="row print-stats mb-4">
                            <div class="col-3">
                                <div class="border">
                                    <strong><?= $stats_laporan['total'] ?></strong>
                                    <small>Total Pengaduan</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border">
                                    <strong><?= $stats_laporan['pending'] ?></strong>
                                    <small>Menunggu Verifikasi</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border">
                                    <strong><?= $stats_laporan['process'] ?></strong>
                                    <small>Sedang Diproses</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border">
                                    <strong><?= $stats_laporan['done'] ?></strong>
                                    <small>Selesai Ditangani</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($laporan_data): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="col-no text-center">No</th>
                                        <th class="col-tanggal text-center">Tanggal</th>
                                        <th class="col-nik text-center">NIK</th>
                                        <th class="col-nama text-center">Nama Pelapor</th>
                                        <th class="col-kategori text-center">Kategori</th>
                                        <th class="col-detail text-center">Detail Pengaduan</th>
                                        <th class="col-status text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($laporan_data as $index => $row): ?>
                                        <tr class="no-break">
                                            <td class="text-center"><?= $index + 1 ?></td>
                                            <td class="text-center"><?= date('d/m/Y', strtotime($row['tgl_pengaduan'])) ?></td>
                                            <td><?= $row['NIK'] ?></td>
                                            <td><?= $row['nama'] ?></td>
                                            <td><?= $row['kategori_pengaduan'] ?></td>
                                            <td class="print-full-text">
                                                <span class="screen-text"><?= substr($row['detail_pengaduan'], 0, 50) ?>...</span>
                                                <span class="print-text d-none"><?= $row['detail_pengaduan'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $status_class = 'status-' . $row['status'];
                                                $status_text = '';
                                                switch($row['status']) {
                                                    case 'pending': $status_text = 'Menunggu'; break;
                                                    case 'process': $status_text = 'Diproses'; break;
                                                    case 'done': $status_text = 'Selesai'; break;
                                                }
                                                ?>
                                                <span class="badge-status <?= $status_class ?> print-status"><?= $status_text ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer untuk Print -->
                        <div class="print-footer d-none no-break">
                            <div class="row mt-5">
                                <div class="col-6">
                                    <div class="signature-box">
                                        <p class="mb-0">Mengetahui,</p>
                                        <p class="mb-0"><strong>Kepala Desa Palandan</strong></p>
                                        <div class="signature-line"></div>
                                        <p class="mb-0"><strong><u>H. Abdul Rahman, S.Sos</u></strong></p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="signature-box">
                                        <p class="mb-0">Palandan, <?= date('d') ?> <?= $bulan_names[date('m')] ?> <?= date('Y') ?></p>
                                        <p class="mb-0"><strong>Sekretaris Desa</strong></p>
                                        <div class="signature-line"></div>
                                        <p class="mb-0"><strong><u>Muhammad Ilham, S.AP</u></strong></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 text-center">
                                <hr style="border-top: 1px solid #000;">
                                <small>
                                    <em>Laporan ini dicetak secara otomatis dari Sistem Informasi Pelayanan Pengaduan Masyarakat (SIPEMAS)</em><br>
                                    <em>Desa Palandan, Kec. Baebunta, Kab. Luwu Utara - Tanggal Cetak: <?= date('d/m/Y H:i') ?> WITA</em><br>
                                    <em>Total <?= count($laporan_data) ?> pengaduan ditampilkan dalam laporan ini</em>
                                </small>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data untuk periode yang dipilih</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    
    <script>
        // Fungsi untuk mempersiapkan print
        function preparePrint() {
            // Show print elements
            document.querySelectorAll('.print-header, .print-footer, .print-text').forEach(el => {
                el.classList.remove('d-none');
            });
            
            // Hide screen elements
            document.querySelectorAll('.screen-text, .no-print').forEach(el => {
                el.style.display = 'none';
            });
            
            setTimeout(() => {
                window.print();
                
                // Restore after print
                setTimeout(() => {
                    document.querySelectorAll('.print-header, .print-footer, .print-text').forEach(el => {
                        el.classList.add('d-none');
                    });
                    
                    document.querySelectorAll('.screen-text, .no-print').forEach(el => {
                        el.style.display = '';
                    });
                }, 100);
            }, 100);
        }
        
        // Override default print button
        document.querySelector('button[onclick="window.print()"]').onclick = function(e) {
            e.preventDefault();
            preparePrint();
        };
        
        // Handle Ctrl+P
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                preparePrint();
            }
        });
    </script>
</body>
</html>