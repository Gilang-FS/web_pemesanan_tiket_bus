<?php
session_start();
require_once '../../config/config.php';
require_once '../../helpers/functions.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Bus - Sistem Pemesanan Tiket Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Added external CSS files -->
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/jadwal.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-bus-front"></i> Tiket Bus
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="jadwal.php">
                            <i class="bi bi-calendar3"></i> Jadwal Bus
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="riwayat_pemesanan.php">
                            <i class="bi bi-clock-history"></i> Riwayat
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($penumpang['nama_penumpang']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Jadwal & Rute Bus</h2>
            </div>
        </div>

        <div class="row g-4">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm schedule-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-geo-alt-fill text-primary"></i> 
                                    <?= htmlspecialchars($row['kota_asal']) ?> → <?= htmlspecialchars($row['kota_tujuan']) ?>
                                </h5>
                                <span class="badge bg-success"><?= htmlspecialchars($row['nama_bus']) ?></span>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Kapasitas</small>
                                    <strong><i class="bi bi-people"></i> <?= $row['kapasitas'] ?> kursi</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Status</small>
                                    <strong>
                                        <?php if($row['status'] == 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Dalam Perawatan</span>
                                        <?php endif; ?>
                                    </strong>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">Pilihan Kelas & Harga:</h6>
                            <div class="row g-2 mb-3">
                                <?php
                                $query_tiket = "SELECT * FROM tiket WHERE no_kursi = ? ORDER BY harga ASC";
                                $stmt_tiket = mysqli_prepare($conn, $query_tiket);
                                $no_kursi = 1; // Get first seat prices as reference
                                mysqli_stmt_bind_param($stmt_tiket, "i", $no_kursi);
                                mysqli_stmt_execute($stmt_tiket);
                                $result_tiket = mysqli_stmt_get_result($stmt_tiket);
                                
                                while($tiket = mysqli_fetch_assoc($result_tiket)):
                                ?>
                                    <div class="col-6 col-md-4">
                                        <div class="price-box text-center">
                                            <small class="text-muted"><?= htmlspecialchars($tiket['tipe_kelas']) ?></small>
                                            <strong class="text-primary">Rp <?= number_format($tiket['harga'], 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <a href="pesan_tiket.php?id_jadwal=<?= $row['id_jadwal'] ?>" class="btn btn-primary w-100">
                                <i class="bi bi-ticket-perforated"></i> Pesan Tiket
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
