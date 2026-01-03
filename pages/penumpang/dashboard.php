<?php
session_start();
require_once '../../config/config.php';
require_once '../../helpers/functions.php';

if(!isset($_SESSION['penumpang_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$conn = getConnection();
$id_penumpang = $_SESSION['penumpang_id'];

$penumpang = getPenumpangData($conn, $id_penumpang);

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM pemesanan WHERE id_penumpang = ?");
$stmt->bind_param("s", $id_penumpang);
$stmt->execute();
$total_pemesanan = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare("SELECT SUM(total_bayar) as total FROM pemesanan WHERE id_penumpang = ?");
$stmt->bind_param("s", $id_penumpang);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$total_pembayaran = $result['total'] ?? 0;
$stmt->close();

$stmt = $conn->prepare("
    SELECT 
        p.*, 
        t.tipe_kelas, 
        j.kota_asal, 
        j.kota_tujuan
    FROM pemesanan p
    INNER JOIN tiket t ON p.id_tiket = t.id_tiket
    LEFT JOIN keberangkatan k ON k.id_penumpang = p.id_penumpang
    LEFT JOIN jadwal j ON j.id_jadwal = k.id_jadwal
    WHERE p.id_penumpang = ?
    ORDER BY p.tanggal_pemesanan DESC
    LIMIT 5
");
$stmt->bind_param("s", $id_penumpang); // VARCHAR(15) → "s"
$stmt->execute();
$result_pemesanan = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pemesanan Tiket Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-bus-front"></i> GOBUS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../jadwal.php">
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

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Dashboard</h2>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow-sm stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Total Pemesanan</h6>
                                <h2 class="card-title mb-0"><?= $total_pemesanan ?></h2>
                            </div>
                            <i class="bi bi-ticket-perforated stat-icon" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Total Pembayaran</h6>
                                <h2 class="card-title mb-0">Rp <?= number_format($total_pembayaran, 0, ',', '.') ?></h2>
                            </div>
                            <i class="bi bi-cash-stack stat-icon" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-info text-white shadow-sm stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-subtitle mb-2">Riwayat Perjalanan</h6>
                                <h2 class="card-title mb-0"><?= $total_pemesanan ?></h2>
                            </div>
                            <i class="bi bi-clock-history stat-icon" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm table-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Pemesanan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php if(mysqli_num_rows($result_pemesanan) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode Booking</th>
                                            <th>Tanggal</th>
                                            <th>Rute</th>
                                            <th>Kelas</th>
                                            <th>Total</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($result_pemesanan)): ?>
                                            <tr>
                                                <td><span class="badge bg-primary"><?= htmlspecialchars($row['id_pemesanan']) ?></span></td>
                                                <td><?= date('d M Y', strtotime($row['tanggal_pemesanan'])) ?></td>
                                                <td><?= htmlspecialchars($row['kota_asal'] . ' → ' . $row['kota_tujuan']) ?></td>
                                                <td><?= htmlspecialchars($row['tipe_kelas']) ?></td>
                                                <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                                <td>
                                                    <a href="riwayat_pemesanan.php" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 empty-state">
                                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">Belum ada pemesanan</p>
                                <a href="../jadwal.php" class="btn btn-primary">
                                    <i class="bi bi-ticket-perforated"></i> Pesan Tiket Sekarang
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>
