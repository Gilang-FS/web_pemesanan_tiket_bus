<?php
session_start();
require_once '../config/config.php';
require_once '../helpers/functions.php';

// Check if user is logged in
if(!isset($_SESSION['penumpang_id'])) {
    header('Location: auth/login.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: jadwal.php');
    exit();
}

$conn = getConnection();

$id_keberangkatan = $_POST['id_keberangkatan'] ?? '';
$id_tiket = $_POST['id_tiket'] ?? '';
$selected_seats = $_POST['selected_seats'] ?? '';
$total_bayar = $_POST['total_bayar'] ?? 0;

if(empty($id_keberangkatan) || empty($id_tiket) || empty($selected_seats)) {
    header('Location: jadwal.php');
    exit();
}

// Get booking details
$sql = "SELECT 
            k.*,
            j.kota_asal,
            j.kota_tujuan,
            b.nama_bus,
            d.nama_driver,
            t.tipe_kelas,
            t.harga,
            p.nama_penumpang,
            p.no_telephone
        FROM keberangkatan k
        INNER JOIN jadwal j ON k.id_jadwal = j.id_jadwal
        INNER JOIN driver d ON k.id_driver = d.id_driver
        INNER JOIN pengendaraan pg ON d.id_driver = pg.id_driver
        INNER JOIN bus b ON pg.id_bus = b.id_bus
        INNER JOIN tiket t ON t.id_tiket = ?
        INNER JOIN penumpang p ON p.id_penumpang = ?
        WHERE k.id_keberangkatan = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $id_tiket, $_SESSION['penumpang_id'], $id_keberangkatan);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if(!$booking) {
    header('Location: jadwal.php');
    exit();
}

$seats_array = explode(',', $selected_seats);
$jumlah_kursi = count($seats_array);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body>
    <div class="container my-4">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="pilih_kursi.php?id_keberangkatan=<?= $id_keberangkatan ?>&id_tiket=<?= $id_tiket ?>" class="btn btn-light me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0">Konfirmasi Pesanan</h5>
        </div>

        <!-- Booking Date -->
        <div class="alert alert-success">
            <i class="bi bi-calendar-check"></i> Tanggal Pemesanan: <?= date('d M Y') ?>
        </div>

        <!-- Journey Info -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Rute Perjalanan</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-fill">
                        <div class="text-muted small">Keberangkatan</div>
                        <div class="fw-bold"><?= htmlspecialchars($booking['kota_asal']) ?></div>
                        <div class="small text-muted"><?= date('d M Y', strtotime($booking['tanggal_keberangkatan'])) ?></div>
                    </div>
                    <div class="px-3">
                        <i class="bi bi-arrow-right text-primary fs-4"></i>
                    </div>
                    <div class="flex-fill text-end">
                        <div class="text-muted small">Tujuan</div>
                        <div class="fw-bold"><?= htmlspecialchars($booking['kota_tujuan']) ?></div>
                    </div>
                </div>
                <hr>
                <div class="d-flex align-items-center">
                    <i class="bi bi-bus-front me-2 text-primary"></i>
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars($booking['nama_bus']) ?></div>
                        <div class="small text-muted"><?= ucfirst(htmlspecialchars($booking['tipe_kelas'])) ?> Class</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Passenger Info -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Detail Penumpang</h6>
                <div class="mb-2">
                    <span class="text-muted">Nama Penumpang:</span>
                    <span class="fw-bold float-end"><?= htmlspecialchars($booking['nama_penumpang']) ?></span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Nomor Kursi:</span>
                    <span class="fw-bold float-end"><?= $jumlah_kursi ?> kursi</span>
                </div>
                <div>
                    <span class="text-muted">KTP:</span>
                    <span class="fw-bold float-end"><?= htmlspecialchars($_SESSION['penumpang_id']) ?></span>
                </div>
            </div>
        </div>

        <!-- Price Breakdown -->
        <div class="card mb-5">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Rincian Harga</h6>
                <div class="mb-2">
                    <span class="text-muted">Tiket x<?= $jumlah_kursi ?>:</span>
                    <span class="float-end"><?= formatRupiah($booking['harga'] * $jumlah_kursi) ?></span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Biaya Layanan:</span>
                    <span class="float-end">Rp 5.000</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Asuransi Perjalanan:</span>
                    <span class="float-end text-success">Gratis</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Total Harga:</span>
                    <span class="fw-bold fs-5 text-primary"><?= formatRupiah($total_bayar + 5000) ?></span>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="fixed-bottom bg-white border-top p-3">
            <div class="container">
                <form action="pembayaran.php" method="POST">
                    <input type="hidden" name="id_keberangkatan" value="<?= $id_keberangkatan ?>">
                    <input type="hidden" name="id_tiket" value="<?= $id_tiket ?>">
                    <input type="hidden" name="selected_seats" value="<?= $selected_seats ?>">
                    <input type="hidden" name="total_bayar" value="<?= $total_bayar + 5000 ?>">
                    <input type="hidden" name="jumlah_kursi" value="<?= $jumlah_kursi ?>">
                    <button type="submit" class="btn btn-primary w-100">
                        Lanjut Pembayaran <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
