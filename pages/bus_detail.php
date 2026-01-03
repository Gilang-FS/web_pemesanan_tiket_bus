<?php
session_start();
require_once '../config/config.php';
require_once '../helpers/functions.php';

$conn = getConnection();

$id_keberangkatan = $_GET['id_keberangkatan'] ?? '';
$id_tiket = $_GET['id_tiket'] ?? '';

if(empty($id_keberangkatan) || empty($id_tiket)) {
    header('Location: jadwal.php');
    exit();
}

// Get bus detail information
$sql = "SELECT 
            k.*,
            j.kota_asal,
            j.kota_tujuan,
            b.id_bus,
            b.nama_bus,
            b.kapasitas,
            d.nama_driver,
            t.id_tiket,
            t.no_kursi,
            t.harga,
            t.tipe_kelas
        FROM keberangkatan k
        INNER JOIN jadwal j ON k.id_jadwal = j.id_jadwal
        INNER JOIN driver d ON k.id_driver = d.id_driver
        INNER JOIN pengendaraan p ON d.id_driver = p.id_driver
        INNER JOIN bus b ON p.id_bus = b.id_bus
        INNER JOIN tiket t ON t.id_tiket = ?
        WHERE k.id_keberangkatan = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $id_tiket, $id_keberangkatan);
$stmt->execute();
$result = $stmt->get_result();
$bus_detail = $result->fetch_assoc();

if(!$bus_detail) {
    header('Location: jadwal.php');
    exit();
}

// Get booked seats for this bus
$sql_booked = "SELECT DISTINCT t.no_kursi 
               FROM pemesanan pm
               INNER JOIN tiket t ON pm.id_tiket = t.id_tiket
               INNER JOIN keberangkatan k ON k.id_penumpang = pm.id_penumpang
               WHERE k.id_keberangkatan = ?";
$stmt_booked = $conn->prepare($sql_booked);
$stmt_booked->bind_param('s', $id_keberangkatan);
$stmt_booked->execute();
$result_booked = $stmt_booked->get_result();

$booked_seats = [];
while($row = $result_booked->fetch_assoc()) {
    $booked_seats[] = $row['no_kursi'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Bus - <?= htmlspecialchars($bus_detail['nama_bus']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/bus-detail.css">
</head>
<body>
    <div class="container my-4">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="jadwal.php" class="btn btn-light me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0"><?= htmlspecialchars($bus_detail['nama_bus']) ?></h4>
        </div>

        <!-- Bus Image -->
        <div class="card mb-4">
            <img src="/placeholder.svg?height=300&width=800" class="card-img-top" alt="Bus">
            <div class="card-body">
                <h5 class="card-title">Mulai Perjalananmu</h5>
                <p class="text-muted mb-0">Nikmati perjalanan nyaman dan aman bersama <?= htmlspecialchars($bus_detail['nama_bus']) ?></p>
            </div>
        </div>

        <!-- Route Info -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Rute Perjalanan</h6>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Dari</div>
                        <div class="fw-bold fs-5"><?= htmlspecialchars($bus_detail['kota_asal']) ?></div>
                    </div>
                    <div>
                        <i class="bi bi-arrow-right text-primary fs-4"></i>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Ke</div>
                        <div class="fw-bold fs-5"><?= htmlspecialchars($bus_detail['kota_tujuan']) ?></div>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <div class="row">
                        <div class="col-6">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            <span><?= date('d M Y', strtotime($bus_detail['tanggal_keberangkatan'])) ?></span>
                        </div>
                        <div class="col-6">
                            <i class="bi bi-person text-primary me-2"></i>
                            <span>Driver: <?= htmlspecialchars($bus_detail['nama_driver']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facilities -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Fasilitas Bus</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="facility-item">
                            <i class="bi bi-wifi text-primary"></i>
                            <span>Wi-Fi Gratis</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="facility-item">
                            <i class="bi bi-outlet text-primary"></i>
                            <span>Stop Kontak</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="facility-item">
                            <i class="bi bi-phone-vibrate text-primary"></i>
                            <span>Kursi Reclining</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="facility-item">
                            <i class="bi bi-snow text-primary"></i>
                            <span>AC/Heater</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="facility-item">
                            <i class="bi bi-lightbulb text-primary"></i>
                            <span>Lampu Baca</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="facility-item">
                            <i class="bi bi-usb-symbol text-primary"></i>
                            <span>Tempat Charger</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Ulasan (2)</h6>
                
                <div class="review-item mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-2">BS</div>
                        <div>
                            <div class="fw-bold">Budi Santoso</div>
                            <div class="rating">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Layanan sangat memuaskan, AC nyaman, kursinya nyaman.</p>
                </div>

                <div class="review-item">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-2">SA</div>
                        <div>
                            <div class="fw-bold">Siti Aminah</div>
                            <div class="rating">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Perjalanan nyaman, driver ramah, hanya saja keberangkatan agak telat.</p>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="fixed-bottom bg-white border-top p-3">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="text-muted small">Harga</div>
                        <div class="fw-bold fs-5 text-primary"><?= formatRupiah($bus_detail['harga']) ?></div>
                    </div>
                    <?php if(isset($_SESSION['penumpang_id'])): ?>
                        <a href="pilih_kursi.php?id_keberangkatan=<?= $id_keberangkatan ?>&id_tiket=<?= $id_tiket ?>" 
                           class="btn btn-primary">
                            Pilih Kursi <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="auth/login.php" class="btn btn-primary">
                            Login untuk Melanjutkan <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
