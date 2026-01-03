<?php
session_start();
require_once '../../config/config.php';
require_once '../../helpers/functions.php';

if(!isset($_SESSION['penumpang_id'])) {
    $_SESSION['redirect_after_login'] = '../../pages/penumpang/pesan_tiket.php?id_jadwal=' . ($_GET['id_jadwal'] ?? '');
    header('Location: ../auth/login.php');
    exit();
}

$conn = getConnection();
$id_penumpang = $_SESSION['penumpang_id'];
$id_jadwal = isset($_GET['id_jadwal']) ? sanitizeInput($_GET['id_jadwal']) : null;

if(!$id_jadwal) {
    header('Location: ../jadwal.php');
    exit();
}

$stmt = $conn->prepare("SELECT j.*, b.nama_bus, b.kapasitas 
                        FROM jadwal j 
                        INNER JOIN bus b ON j.id_bus = b.id_bus 
                        WHERE j.id_jadwal = ? AND b.status = 'aktif'");
$stmt->bind_param("s", $id_jadwal);
$stmt->execute();
$result_jadwal = $stmt->get_result();

if($result_jadwal->num_rows === 0) {
    header('Location: ../jadwal.php');
    exit();
}

$jadwal = $result_jadwal->fetch_assoc();
$stmt->close();

$result_tiket = $conn->query("SELECT * FROM tiket ORDER BY harga ASC");

$result_driver = $conn->query("SELECT * FROM driver ORDER BY nama_driver ASC");

$penumpang = getPenumpangData($conn, $id_penumpang);

if(isset($_POST['pesan'])) {
    $id_tiket = sanitizeInput($_POST['id_tiket']);
    $id_driver = sanitizeInput($_POST['id_driver']);
    $tanggal_pemesanan = sanitizeInput($_POST['tanggal_pemesanan']);
    $jumlah_penumpang = intval($_POST['jumlah_penumpang']);
    $metode_pembayaran = sanitizeInput($_POST['metode_pembayaran']);
    $total_bayar = intval($_POST['total_bayar']);
    
    // Generate booking ID
    $stmt = $conn->prepare("SELECT id_pemesanan FROM pemesanan ORDER BY id_pemesanan DESC LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $last_id = $result->fetch_assoc()['id_pemesanan'];
        $num = intval(substr($last_id, 2)) + 1;
        $id_pemesanan = 'PM' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $id_pemesanan = 'PM001';
    }
    $stmt->close();
    
    // Insert booking
    $stmt = $conn->prepare("INSERT INTO pemesanan (id_pemesanan, id_penumpang, id_tiket, tanggal_pemesanan, total_bayar, metode_pembayaran) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $id_pemesanan, $id_penumpang, $id_tiket, $tanggal_pemesanan, $total_bayar, $metode_pembayaran);
    
    if($stmt->execute()) {
        // Insert into keberangkatan table
        $stmt2 = $conn->prepare("INSERT INTO keberangkatan (id_pemesanan, id_driver, id_jadwal, jumlah_penumpang, tanggal_keberangkatan) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("sssis", $id_pemesanan, $id_driver, $id_jadwal, $jumlah_penumpang, $tanggal_pemesanan);
        
        if($stmt2->execute()) {
            $kode_booking = $id_pemesanan;
            $success = "Pemesanan berhasil! Silakan lakukan pembayaran.";
        } else {
            $error = "Terjadi kesalahan saat menyimpan data keberangkatan.";
        }
        $stmt2->close();
    } else {
        $error = "Pemesanan gagal! Silakan coba lagi.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Tiket - Sistem Pemesanan Tiket Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/booking.css">
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

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm booking-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Form Pemesanan Tiket</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="booking-info-box mb-4">
                            <h6 class="mb-3">Informasi Rute</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Nama Bus</small>
                                    <strong><?= htmlspecialchars($jadwal['nama_bus']) ?></strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Rute</small>
                                    <strong><?= htmlspecialchars($jadwal['kota_asal'] . ' → ' . $jadwal['kota_tujuan']) ?></strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Kapasitas</small>
                                    <strong><?= $jadwal['kapasitas'] ?> kursi</strong>
                                </div>
                            </div>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-circle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="bi bi-check-circle"></i> <?= $success ?>
                                <br>
                                <strong>Kode Booking: <?= $kode_booking ?></strong>
                                <br>
                                <a href="riwayat_pemesanan.php" class="btn btn-sm btn-success mt-2">
                                    <i class="bi bi-eye"></i> Lihat Detail Pemesanan
                                </a>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="bookingForm" class="booking-form">
                            <input type="hidden" name="id_jadwal" value="<?= $id_jadwal ?>">
                            <input type="hidden" name="id_penumpang" value="<?= $id_penumpang ?>">

                            <div class="mb-3">
                                <label for="id_tiket" class="form-label">Pilih Kelas Tiket</label>
                                <select class="form-select" id="id_tiket" name="id_tiket" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php 
                                    mysqli_data_seek($result_tiket, 0);
                                    while($tiket = mysqli_fetch_assoc($result_tiket)): 
                                    ?>
                                        <option value="<?= $tiket['id_tiket'] ?>" data-price="<?= $tiket['harga'] ?>">
                                            <?= htmlspecialchars($tiket['tipe_kelas']) ?> - Rp <?= number_format($tiket['harga'], 0, ',', '.') ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="id_driver" class="form-label">Pilih Driver</label>
                                <select class="form-select" id="id_driver" name="id_driver" required>
                                    <option value="">-- Pilih Driver --</option>
                                    <?php while($driver = mysqli_fetch_assoc($result_driver)): ?>
                                        <option value="<?= $driver['id_driver'] ?>">
                                            <?= htmlspecialchars($driver['nama_driver']) ?> - <?= htmlspecialchars($driver['alamat']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_pemesanan" class="form-label">Tanggal Keberangkatan</label>
                                <input type="date" class="form-control" id="tanggal_pemesanan" name="tanggal_pemesanan" 
                                       min="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah_penumpang" class="form-label">Jumlah Penumpang</label>
                                <input type="number" class="form-control" id="jumlah_penumpang" name="jumlah_penumpang" 
                                       min="1" max="<?= $jadwal['kapasitas'] ?>" value="1" required>
                                <small class="text-muted">Maksimal <?= $jadwal['kapasitas'] ?> penumpang</small>
                            </div>

                            <div class="mb-3">
                                <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="qris">QRIS</option>
                                    <option value="tunai">Tunai</option>
                                    <option value="kartu kredit">Kartu Kredit</option>
                                    <option value="e-wallet">E-Wallet</option>
                                </select>
                            </div>

                            <div class="price-summary mb-3">
                                <div class="price-row">
                                    <span>Harga per Tiket:</span>
                                    <strong id="price-per-ticket">Rp 0</strong>
                                </div>
                                <div class="price-row">
                                    <span>Jumlah Penumpang:</span>
                                    <strong id="passenger-count">1</strong>
                                </div>
                                <hr>
                                <div class="total-row">
                                    <strong>Total Pembayaran:</strong>
                                    <strong class="text-primary fs-5" id="total-price">Rp 0</strong>
                                </div>
                                <input type="hidden" name="total_bayar" id="total_bayar" value="0">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="pesan" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Konfirmasi Pemesanan
                                </button>
                                <a href="../jadwal.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ticketSelect = document.getElementById('id_tiket');
        const passengerInput = document.getElementById('jumlah_penumpang');
        const pricePerTicket = document.getElementById('price-per-ticket');
        const passengerCount = document.getElementById('passenger-count');
        const totalPrice = document.getElementById('total-price');
        const totalBayarInput = document.getElementById('total_bayar');

        function calculateTotal() {
            const selectedOption = ticketSelect.options[ticketSelect.selectedIndex];
            const price = selectedOption ? parseFloat(selectedOption.dataset.price) || 0 : 0;
            const passengers = parseInt(passengerInput.value) || 1;
            const total = price * passengers;

            pricePerTicket.textContent = 'Rp ' + price.toLocaleString('id-ID');
            passengerCount.textContent = passengers;
            totalPrice.textContent = 'Rp ' + total.toLocaleString('id-ID');
            totalBayarInput.value = total;
        }

        ticketSelect.addEventListener('change', calculateTotal);
        passengerInput.addEventListener('input', calculateTotal);
    </script>
</body>
</html>
<?php $conn->close(); ?>
