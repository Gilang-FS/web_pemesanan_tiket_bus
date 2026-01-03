<?php
session_start();
require_once '../config/config.php';
require_once '../helpers/functions.php';

$conn = getConnection();

// Build search filter
$where_clause = "WHERE b.status = 'aktif'";
$params = [];
$types = "";

if(isset($_GET['kota_asal']) && !empty($_GET['kota_asal'])) {
    $where_clause .= " AND j.kota_asal LIKE ?";
    $params[] = '%' . $_GET['kota_asal'] . '%';
    $types .= 's';
}

if(isset($_GET['kota_tujuan']) && !empty($_GET['kota_tujuan'])) {
    $where_clause .= " AND j.kota_tujuan LIKE ?";
    $params[] = '%' . $_GET['kota_tujuan'] . '%';
    $types .= 's';
}

if(isset($_GET['tanggal']) && !empty($_GET['tanggal'])) {
    $where_clause .= " AND k.tanggal_keberangkatan = ?";
    $params[] = $_GET['tanggal'];
    $types .= 's';
}

// Get all schedules with bus, driver, and ticket information
$sql = "SELECT DISTINCT
            j.id_jadwal,
            j.kota_asal,
            j.kota_tujuan,
            b.id_bus,
            b.nama_bus,
            b.kapasitas,
            b.status,
            d.id_driver,
            d.nama_driver,
            t.id_tiket,
            t.tipe_kelas,
            t.harga,
            k.tanggal_keberangkatan,
            k.id_keberangkatan
        FROM keberangkatan k
        INNER JOIN jadwal j ON k.id_jadwal = j.id_jadwal
        INNER JOIN driver d ON k.id_driver = d.id_driver
        INNER JOIN pengendaraan p ON d.id_driver = p.id_driver
        INNER JOIN bus b ON p.id_bus = b.id_bus
        CROSS JOIN tiket t
        $where_clause
        ORDER BY k.tanggal_keberangkatan, j.kota_asal, j.kota_tujuan, t.harga";

$stmt = $conn->prepare($sql);
if(!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Bus - Sistem Pemesanan Tiket Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/jadwal.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="bi bi-bus-front-fill"></i> GOBUS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="jadwal.php">Jadwal</a>
                    </li>
                    <?php if(isset($_SESSION['penumpang_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="penumpang/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="penumpang/riwayat_pemesanan.php">Riwayat</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-light btn-sm" href="auth/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-2">
                            <a class="btn btn-light btn-sm" href="auth/login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-light btn-sm" href="auth/register.php">
                                <i class="bi bi-person-plus"></i> Daftar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="fw-bold">
                    <i class="bi bi-calendar-check text-primary"></i> Jadwal Keberangkatan Bus
                </h2>
                <p class="text-muted">Pilih jadwal dan kelas sesuai dengan kebutuhan perjalanan Anda</p>
            </div>
        </div>

        <!-- Search Filter -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Kota Asal</label>
                            <input type="text" class="form-control" name="kota_asal" 
                                   value="<?= isset($_GET['kota_asal']) ? htmlspecialchars($_GET['kota_asal']) : '' ?>"
                                   placeholder="Cari kota asal...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kota Tujuan</label>
                            <input type="text" class="form-control" name="kota_tujuan" 
                                   value="<?= isset($_GET['kota_tujuan']) ? htmlspecialchars($_GET['kota_tujuan']) : '' ?>"
                                   placeholder="Cari kota tujuan...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Keberangkatan</label>
                            <input type="date" class="form-control" name="tanggal" 
                                   value="<?= isset($_GET['tanggal']) ? htmlspecialchars($_GET['tanggal']) : '' ?>"
                                   min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Cari Jadwal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Schedules List -->
        <div class="row g-4">
            <?php if($result && $result->num_rows > 0): ?>
                <?php 
                $schedules = [];
                while($row = $result->fetch_assoc()) {
                    $key = $row['id_keberangkatan'] . '_' . $row['tipe_kelas'];
                    if(!isset($schedules[$key])) {
                        $schedules[$key] = $row;
                    }
                }
                
                foreach($schedules as $row): 
                ?>
                <div class="col-md-6">
                    <div class="card schedule-card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($row['nama_bus']) ?></h5>
                                    <span class="badge bg-info">
                                        <i class="bi bi-star-fill"></i> <?= ucfirst(htmlspecialchars($row['tipe_kelas'])) ?>
                                    </span>
                                    <span class="badge bg-secondary ms-1">
                                        <?= htmlspecialchars($row['nama_driver']) ?>
                                    </span>
                                </div>
                                <div class="text-end">
                                    <div class="text-primary fw-bold fs-5"><?= formatRupiah($row['harga']) ?></div>
                                    <small class="text-muted">per orang</small>
                                </div>
                            </div>
                            
                            <div class="route-info mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($row['kota_asal']) ?></div>
                                        <div class="text-muted small"><?= date('d M Y', strtotime($row['tanggal_keberangkatan'])) ?></div>
                                    </div>
                                    <div class="px-3">
                                        <i class="bi bi-arrow-right text-primary"></i>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold"><?= htmlspecialchars($row['kota_tujuan']) ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="schedule-info mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people-fill text-muted me-2"></i>
                                    <span class="text-muted"><?= $row['kapasitas'] ?> kursi</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-circle-fill text-success me-2" style="font-size: 0.6rem;"></i>
                                    <span class="text-success fw-bold">Tersedia</span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <a href="bus_detail.php?id_keberangkatan=<?= $row['id_keberangkatan'] ?>&id_tiket=<?= $row['id_tiket'] ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-ticket-perforated"></i> Lihat Detail & Pilih Kursi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> Tidak ada jadwal tersedia untuk pencarian Anda.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
