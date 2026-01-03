<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pemesanan Tiket Bus Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-bus-front-fill"></i> GOBUS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Added Jadwal link to navbar -->
                    <li class="nav-item">
                        <a class="nav-link" href="pages/jadwal.php">Jadwal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#why-us">Mengapa Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Kontak</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-light btn-sm" href="pages/auth/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-light btn-sm" href="pages/auth/register.php">
                            <i class="bi bi-person-plus"></i> Daftar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">Pesan Tiket Bus Online dengan Mudah</h1>
                    <p class="lead text-white mb-4">Sistem pemesanan tiket bus terpercaya dengan berbagai pilihan rute dan kelas. Pesan sekarang dan nikmati perjalanan Anda!</p>
                    <div class="d-flex gap-3">
                        <!-- Changed button to redirect to schedule page -->
                        <a href="pages/jadwal.php" class="btn btn-light btn-lg">
                            <i class="bi bi-calendar-check"></i> Lihat Jadwal
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-info-circle"></i> Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <i class="bi bi-bus-front display-1 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Fitur Unggulan</h2>
                <p class="text-muted">Kemudahan dalam setiap perjalanan Anda</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4>Pemesanan Cepat</h4>
                        <p class="text-muted">Pesan tiket dalam hitungan menit dengan proses yang mudah dan cepat</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4>Aman & Terpercaya</h4>
                        <p class="text-muted">Transaksi Anda dijamin aman dengan sistem keamanan terbaik</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <h4>Metode Pembayaran Lengkap</h4>
                        <p class="text-muted">Transfer bank, QRIS, tunai, e-wallet, dan kartu kredit</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4>Banyak Rute</h4>
                        <p class="text-muted">Tersedia berbagai rute ke seluruh kota di Indonesia</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-star"></i>
                        </div>
                        <h4>Pilihan Kelas Premium</h4>
                        <p class="text-muted">Dari ekonomi hingga VIP, pilih sesuai kebutuhan Anda</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h4>Customer Support 24/7</h4>
                        <p class="text-muted">Tim kami siap membantu Anda kapan saja</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Us Section -->
    <section id="why-us" class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Mengapa Memilih Kami?</h2>
                <p class="text-muted">Alasan kenapa ribuan penumpang memilih layanan kami</p>
            </div>
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="stats-card">
                        <div class="stat-item">
                            <h3 class="text-primary fw-bold">10+</h3>
                            <p class="text-muted">Armada Bus</p>
                        </div>
                        <div class="stat-item">
                            <h3 class="text-primary fw-bold">50+</h3>
                            <p class="text-muted">Rute Tersedia</p>
                        </div>
                        <div class="stat-item">
                            <h3 class="text-primary fw-bold">1000+</h3>
                            <p class="text-muted">Penumpang Puas</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="why-list">
                        <div class="why-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div>
                                <h5>Harga Kompetitif</h5>
                                <p class="text-muted mb-0">Dapatkan harga terbaik untuk perjalanan Anda</p>
                            </div>
                        </div>
                        <div class="why-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div>
                                <h5>Bus Terawat</h5>
                                <p class="text-muted mb-0">Armada bus yang selalu dalam kondisi prima</p>
                            </div>
                        </div>
                        <div class="why-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div>
                                <h5>Driver Profesional</h5>
                                <p class="text-muted mb-0">Pengemudi berpengalaman dan terlatih</p>
                            </div>
                        </div>
                        <div class="why-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div>
                                <h5>Tepat Waktu</h5>
                                <p class="text-muted mb-0">Keberangkatan dan kedatangan selalu on time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Hubungi Kami</h2>
                <p class="text-muted">Ada pertanyaan? Jangan ragu untuk menghubungi kami</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="contact-card text-center">
                                <i class="bi bi-telephone-fill"></i>
                                <h5 class="mt-3">Telepon</h5>
                                <br><p class="text-muted"><a href="https://wa.me/6285799796044" target="_blank">+62 8579 9796 044</a></p>
                                <p class="text-muted"><a href="https://wa.me/628953330291307" target="_blank">+62 8953 3302 91307</a></p>
                                <p class="text-muted"><a href="https://wa.me/6282180775405" target="_blank">+62 821 8077 5405</a></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="contact-card text-center">
                                <i class="bi bi-envelope-fill"></i>
                                <h5 class="mt-3">Email</h5>
                                <br><p class="text-muted"><a href="mailto:2400018271@webmail.uad.ac.id" target="_blank">admin 1</a></p>
                                <p class="text-muted"><a href="mailto:2438018220@webmail.uad.ac.id" target="_blank">admin 2</a></p>
                                <p class="text-muted"><a href="mailto:2400018275@webmail.uad.ac.id" target="_blank">admin 3</a></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="contact-card text-center">
                                <i class="bi bi-geo-alt-fill"></i>
                                <h5 class="mt-3">Alamat</h5>
                                <br><p> </p>
                                <p class="text-muted">Universitas Ahmad Dahlan, Yogyakrtya Indonesisa</p>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-bus-front-fill"></i> BusTicket
                    </h5>
                    <p class="text-muted">Sistem pemesanan tiket bus online terpercaya dan terlengkap di Indonesia.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Link Cepat</h6>
                    <ul class="list-unstyled">
                        <li><a href="pages/jadwal.php" class="text-muted text-decoration-none">Jadwal</a></li>
                        <li><a href="#features" class="text-muted text-decoration-none">Fitur</a></li>
                        <li><a href="#why-us" class="text-muted text-decoration-none">Mengapa Kami</a></li>
                        <li><a href="#contact" class="text-muted text-decoration-none">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Akun</h6>
                    <ul class="list-unstyled">
                        <li><a href="pages/auth/login.php" class="text-muted text-decoration-none">Masuk</a></li>
                        <li><a href="pages/auth/register.php" class="text-muted text-decoration-none">Daftar</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="text-center">
                <p class="mb-0 text-muted">&copy; 2025 BusTicket. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
