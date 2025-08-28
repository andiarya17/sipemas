<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Cek jika user sudah login, redirect ke dashboard
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    redirect('user/dashboard.php');
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    redirect('admin/dashboard.php');
}

$stats = get_pengaduan_stats();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPEMAS - Sistem Informasi Pelayanan Pengaduan Masyarakat Desa Palandan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/img/luwuutara.png" width="40" height="40" class="me-2"> SIPEMAS Desa Palandan
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#profil-desa">Profil Desa</a></li>
                    <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" style="padding-top: 100px;">
        <div class="container">
            <div class="welcome-hero">
                <h1><i class="fas fa-comments"></i> Selamat Datang di SIPEMAS</h1>
                <p class="lead">Sistem Informasi Pelayanan Pengaduan Masyarakat<br>Desa Palandan, Kecamatan Baebunta, Kabupaten Luwu Utara</p>
                <div class="mt-4">
                    <a href="login.php" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </a>
                    <a href="register.php" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card">
                        <h3><?= $stats['total'] ?></h3>
                        <p><i class="fas fa-clipboard-list"></i> Total Pengaduan</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card" style="background: linear-gradient(135deg, #FF9800 0%, #FFB74D 100%);">
                        <h3><?= $stats['pending'] ?></h3>
                        <p><i class="fas fa-clock"></i> Menunggu Verifikasi</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card" style="background: linear-gradient(135deg, #2196F3 0%, #64B5F6 100%);">
                        <h3><?= $stats['process'] ?></h3>
                        <p><i class="fas fa-cog"></i> Sedang Diproses</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card" style="background: linear-gradient(135deg, #4CAF50 0%, #81C784 100%);">
                        <h3><?= $stats['done'] ?></h3>
                        <p><i class="fas fa-check-circle"></i> Selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang -->
    <section id="tentang" class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-10 mx-auto">
                    <div class="card">
                        <div class="card-header text-center">
                            <h2><i class="fas fa-info-circle"></i> Tentang SIPEMAS</h2>
                        </div>
                        <div class="card-body">
                            <p class="lead text-center">
                                Sistem Informasi Pelayanan Pengaduan Masyarakat (SIPEMAS) adalah platform digital 
                                yang memudahkan masyarakat Desa Palandan untuk menyampaikan keluhan, saran, dan pengaduan kepada pemerintah desa.
                            </p>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-eye text-success"></i> Visi</h5>
                                    <p>Mewujudkan pelayanan publik yang transparan, responsif, dan berkualitas 
                                    di Desa Palandan melalui pemanfaatan teknologi informasi.</p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-bullseye text-success"></i> Misi</h5>
                                    <ul>
                                        <li>Meningkatkan aksesibilitas layanan pengaduan</li>
                                        <li>Mempercepat proses penanganan pengaduan</li>
                                        <li>Meningkatkan transparansi pemerintahan desa</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Profil Desa -->
<section id="profil-desa" class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h2><i class="fas fa-map-marked-alt"></i> Profil Desa Palandan</h2>
                    </div>
                    <div class="card-body">
                        <p class="lead">
                            Desa Palandan merupakan salah satu desa yang terletak di Kecamatan Baebunta,
                            Kabupaten Luwu Utara, Provinsi Sulawesi Selatan. Desa ini dikenal dengan potensi
                            alamnya yang indah, tanah yang subur, dan masyarakat yang ramah serta gotong royong.
                        </p>

                        <div class="row mt-4">
                            <div class="col-md-3">
                                <h6><i class="fas fa-map-pin text-primary"></i> Lokasi Geografis</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Provinsi:</strong> Sulawesi Selatan</li>
                                    <li><strong>Kabupaten:</strong> Luwu Utara</li>
                                    <li><strong>Kecamatan:</strong> Baebunta</li>
                                    <li><strong>Kode Pos:</strong> 92965</li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h6><i class="fas fa-expand-arrows-alt text-primary"></i> Wilayah</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Jumlah Dusun:</strong> 4 Dusun</li>
                                    <li><strong>Jumlah RT:</strong> 4 RT</li>
                                    <li><strong>Jumlah RW:</strong> -</li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h6><i class="fas fa-users text-success"></i> Demografi</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Jumlah Penduduk:</strong> ± 400 jiwa</li>
                                    <li><strong>Jumlah KK:</strong> ± 238 KK</li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h6><i class="fas fa-graduation-cap text-warning"></i> Pendidikan</h6>
                                <ul class="list-unstyled">
                                    <li>SD/MI: 1 unit</li>
                                    <li>SMP/MTs: -</li>
                                    <li>SMA/MA: -</li>
                                    <li>PAUD/TK: 1 unit</li>
                                    <li>Perpustakaan: -</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Visi Misi Desa -->
<div class="row mb-5">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header text-center">
                <h3><i class="fas fa-flag"></i> Visi dan Misi Desa Palandan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-eye text-primary"></i> Visi Desa</h5>
                        <div class="bg-light p-3 rounded">
                            <p class="text-center fw-bold" style="font-size: 1.1rem;">
                                "Menuju Perubahan Untuk Palandan Bangkit, Maju Dan Sejahtera"
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-bullseye text-success"></i> Misi Desa</h5>
                        <ol>
                            <li>Melaksanakan tata kelola pemerintahan Desa yang profesional, efektif, dan akuntabel untuk peningkatan kualitas pelayanan masyarakat.</li>
                            <li>Memberikan pelayanan dasar yang berkeadilan sosial dengan mengedepankan musyawarah dan mufakat untuk penguatan ekonomi produktif dan berdaya saing.</li>
                            <li>Mewujudkan sarana dan prasarana desa yang memadai untuk menciptakan iklim kondusif.</li>
                            <li>Pemberdayaan kelembagaan dengan mengedepankan peran pemuda serta seluruh elemen masyarakat.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Struktur Pemerintahan -->
<div class="row mb-5">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header text-center">
                <h3><i class="fas fa-users-cog"></i> Struktur Pemerintahan Desa</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="bg-primary text-white p-3 rounded">
                            <i class="fas fa-user-tie fa-2x mb-2"></i>
                            <h6>Kepala Desa</h6>
                            <p class="mb-0">H. Suhadi, SH</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="bg-success text-white p-3 rounded">
                            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                            <h6>Sekretaris Desa</h6>
                            <p class="mb-0">Faisal</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="bg-warning text-white p-3 rounded">
                            <i class="fas fa-calculator fa-2x mb-2"></i>
                            <h6>Bendahara Desa</h6>
                            <p class="mb-0">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Layanan -->
    <section id="layanan" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5"><i class="fas fa-cogs"></i> Layanan Kami</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-edit fa-3x text-success mb-3"></i>
                            <h5>Pengaduan Online</h5>
                            <p>Sampaikan keluhan dan saran Anda secara online dengan mudah dan cepat.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                            <h5>Tracking Status</h5>
                            <p>Pantau perkembangan pengaduan Anda secara real-time melalui dashboard.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-reply fa-3x text-success mb-3"></i>
                            <h5>Tanggapan Cepat</h5>
                            <p>Dapatkan tanggapan dan solusi dari pemerintah desa dengan cepat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class=""></i> SIPEMAS Desa Palandan</h5>
                    <p>Jl. Poros Baebunta-Masamba<br>
                    Desa Palandan, Kec. Baebunta<br>
                    Kab. Luwu Utara, Sulawesi Selatan 91971</p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-phone"></i> Kontak</h5>
                    <p>WhatsApp: +62 813-4567-8901</p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-clock"></i> Jam Pelayanan</h5>
                    <p>Senin - Jumat: 08:00 - 16:00 WITA<br>
                    Sabtu - Minggu: Tutup</p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2025 SIPEMAS Desa Palandan. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>

    <!-- Smooth scrolling -->
    <script>
        // Smooth scrolling untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Highlight active navbar item
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link[href^="#"]');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 60) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>