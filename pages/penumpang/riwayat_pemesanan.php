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

$stmt = $conn->prepare("SELECT 
                            pm.id_pemesanan,
                            pm.tanggal_pemesanan,
                            pm.total_bayar,
                            pm.metode_pembayaran,
                            t.tipe_kelas,
                            t.no_kursi,
                            t.harga,
                            j.kota_asal,
                            j.kota_tujuan,
                            k.tanggal_keberangkatan,
                            k.jumlah_penumpang,
                            d.nama_driver,
                            b.nama_bus
                        FROM pemesanan pm
                        INNER JOIN tiket t ON pm.id_tiket = t.id_tiket
                        LEFT JOIN keberangkatan k ON k.id_penumpang = pm.id_penumpang
                        LEFT JOIN jadwal j ON k.id_jadwal = j.id_jadwal
                        LEFT JOIN driver d ON k.id_driver = d.id_driver
                        LEFT JOIN pengendaraan p ON d.id_driver = p.id_driver
                        LEFT JOIN bus b ON p.id_bus = b.id_bus
                        WHERE pm.id_penumpang = ?
                        ORDER BY pm.tanggal_pemesanan DESC");
$stmt->bind_param("s", $id_penumpang);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - Sistem Pemesanan Tiket Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/riwayat.css">
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../jadwal.php">
                            <i class="bi bi-calendar3"></i> Jadwal Bus
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="riwayat_pemesanan.php">
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
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= $_SESSION['success_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Riwayat Pemesanan</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php if($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <div class="card shadow-sm mb-3 history-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title mb-0">
                                                <span class="badge bg-primary"><?= htmlspecialchars($row['id_pemesanan']) ?></span>
                                            </h5>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> <?= date('d M Y', strtotime($row['tanggal_pemesanan'])) ?>
                                            </small>
                                        </div>
                                        
                                        <?php if($row['nama_bus']): ?>
                                        <div class="history-info-item">
                                            <i class="bi bi-bus-front text-primary"></i>
                                            <strong><?= htmlspecialchars($row['nama_bus']) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if($row['kota_asal'] && $row['kota_tujuan']): ?>
                                        <div class="history-info-item">
                                            <i class="bi bi-geo-alt-fill text-primary"></i>
                                            <?= htmlspecialchars($row['kota_asal'] . ' → ' . $row['kota_tujuan']) ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if($row['tanggal_keberangkatan']): ?>
                                        <div class="history-info-item">
                                            <i class="bi bi-calendar-check text-primary"></i>
                                            Keberangkatan: <?= date('d M Y', strtotime($row['tanggal_keberangkatan'])) ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="history-info-item">
                                            <i class="bi bi-ticket-perforated text-primary"></i>
                                            <span class="badge bg-info"><?= htmlspecialchars($row['tipe_kelas']) ?></span>
                                            <span class="ms-2">Kursi: <?= $row['no_kursi'] ?></span>
                                        </div>
                                        
                                        <?php if($row['nama_driver']): ?>
                                        <div class="history-info-item">
                                            <i class="bi bi-person-circle text-primary"></i>
                                            Driver: <?= htmlspecialchars($row['nama_driver']) ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="history-info-item">
                                            <i class="bi bi-credit-card text-primary"></i>
                                            <?= htmlspecialchars(ucfirst($row['metode_pembayaran'])) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 history-payment-total">
                                        <div class="mb-3">
                                            <small class="text-muted">Total Pembayaran</small>
                                            <h4 class="mb-0 text-primary"><?= formatRupiah($row['total_bayar']) ?></h4>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary btn-sm" onclick="printTicket('<?= $row['id_pemesanan'] ?>')">
                                                <i class="bi bi-printer"></i> Cetak Tiket
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5 empty-state">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Belum ada riwayat pemesanan</p>
                            <a href="../jadwal.php" class="btn btn-primary">
                                <i class="bi bi-ticket-perforated"></i> Pesan Tiket Sekarang
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printTicket(bookingId) {
            window.open('cetak_tiket.php?id=' + bookingId, '_blank', 'width=900,height=700');
        }
    </script>
</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>
