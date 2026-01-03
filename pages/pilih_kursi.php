<?php
session_start();
require_once '../config/config.php';
require_once '../helpers/functions.php';

// Check if user is logged in
if(!isset($_SESSION['penumpang_id'])) {
    header('Location: auth/login.php');
    exit();
}

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

// Get all available seats for this class
$sql_seats = "SELECT no_kursi FROM tiket WHERE tipe_kelas = ? ORDER BY no_kursi";
$stmt_seats = $conn->prepare($sql_seats);
$stmt_seats->bind_param('s', $bus_detail['tipe_kelas']);
$stmt_seats->execute();
$result_seats = $stmt_seats->get_result();

$available_seats = [];
while($row = $result_seats->fetch_assoc()) {
    $available_seats[] = $row['no_kursi'];
}

// Get booked seats
$sql_booked = "SELECT DISTINCT t.no_kursi 
               FROM pemesanan pm
               INNER JOIN tiket t ON pm.id_tiket = t.id_tiket
               INNER JOIN keberangkatan k ON k.id_penumpang = pm.id_penumpang
               WHERE k.id_keberangkatan = ? AND t.tipe_kelas = ?";
$stmt_booked = $conn->prepare($sql_booked);
$stmt_booked->bind_param('ss', $id_keberangkatan, $bus_detail['tipe_kelas']);
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
    <title>Pilih Kursi - <?= htmlspecialchars($bus_detail['nama_bus']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/seat-selection.css">
</head>
<body>
    <div class="container my-4">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="bus_detail.php?id_keberangkatan=<?= $id_keberangkatan ?>&id_tiket=<?= $id_tiket ?>" class="btn btn-light me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="mb-0">Pilih Kursi</h5>
                <small class="text-muted"><?= ucfirst(htmlspecialchars($bus_detail['tipe_kelas'])) ?> Class</small>
            </div>
        </div>

        <!-- Route Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Jakarta (Pulo Gebang) → Bandung (Leuwi Panjang)</div>
                        <div class="fw-bold"><?= htmlspecialchars($bus_detail['kota_asal']) ?> → <?= htmlspecialchars($bus_detail['kota_tujuan']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seat Selection Tabs -->
        <div class="d-flex gap-2 mb-4">
            <button class="btn btn-sm seat-filter-btn active" data-filter="all">Tersedia</button>
            <button class="btn btn-sm seat-filter-btn btn-outline-secondary" data-filter="booked">Dipilih</button>
            <button class="btn btn-sm seat-filter-btn btn-outline-secondary" data-filter="filled">Terisi</button>
        </div>

        <!-- Seat Map -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="seat-map">
                    <?php 
                    $rows = ['1', '2', '3', '4', '5', '6'];
                    $cols = ['A', 'B', 'C', 'D'];
                    
                    foreach($rows as $row): 
                    ?>
                        <div class="seat-row">
                            <?php foreach($cols as $col): 
                                $seat_number = $row . $col;
                                $seat_value = (ord($col) - ord('A') + 1) + (($row - 1) * 4);
                                
                                $is_booked = in_array($seat_value, $booked_seats);
                                $is_available = in_array($seat_value, $available_seats);
                                
                                if(!$is_available) continue;
                                
                                $class = 'seat-btn';
                                if($is_booked) {
                                    $class .= ' seat-taken';
                                    $disabled = 'disabled';
                                } else {
                                    $class .= ' seat-available';
                                    $disabled = '';
                                }
                            ?>
                                <button class="<?= $class ?>" 
                                        data-seat="<?= $seat_number ?>" 
                                        data-seat-value="<?= $seat_value ?>"
                                        <?= $disabled ?>>
                                    <?= $seat_number ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Legend -->
                <div class="seat-legend mt-4">
                    <div class="d-flex justify-content-center gap-4">
                        <div class="d-flex align-items-center">
                            <div class="legend-box seat-available"></div>
                            <span class="small">Tersedia</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-box seat-selected"></div>
                            <span class="small">Dipilih</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="legend-box seat-taken"></div>
                            <span class="small">Terisi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seat Info -->
        <div class="card mb-5">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Kursi yang dipilih:</div>
                        <div class="fw-bold" id="selected-seats-text">Belum ada kursi dipilih</div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Harga Total:</div>
                        <div class="fw-bold fs-5 text-primary" id="total-price">Rp 0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="fixed-bottom bg-white border-top p-3">
            <div class="container">
                <form action="konfirmasi_pemesanan.php" method="POST" id="booking-form">
                    <input type="hidden" name="id_keberangkatan" value="<?= $id_keberangkatan ?>">
                    <input type="hidden" name="id_tiket" value="<?= $id_tiket ?>">
                    <input type="hidden" name="selected_seats" id="selected-seats-input">
                    <input type="hidden" name="total_bayar" id="total-bayar-input">
                    <button type="submit" class="btn btn-primary w-100" id="continue-btn" disabled>
                        Lanjut ke Pembayaran <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const pricePerSeat = <?= $bus_detail['harga'] ?>;
        let selectedSeats = [];

        document.querySelectorAll('.seat-available').forEach(btn => {
            btn.addEventListener('click', function() {
                const seat = this.dataset.seat;
                const seatValue = this.dataset.seatValue;
                
                if(this.classList.contains('seat-selected')) {
                    this.classList.remove('seat-selected');
                    selectedSeats = selectedSeats.filter(s => s !== seatValue);
                } else {
                    this.classList.add('seat-selected');
                    selectedSeats.push(seatValue);
                }
                
                updateBookingInfo();
            });
        });

        function updateBookingInfo() {
            const totalPrice = selectedSeats.length * pricePerSeat;
            
            document.getElementById('selected-seats-text').textContent = 
                selectedSeats.length > 0 ? 
                `${selectedSeats.length} kursi (${document.querySelectorAll('.seat-selected').length > 0 ? Array.from(document.querySelectorAll('.seat-selected')).map(b => b.dataset.seat).join(', ') : ''})` : 
                'Belum ada kursi dipilih';
                
            document.getElementById('total-price').textContent = 
                new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalPrice);
                
            document.getElementById('selected-seats-input').value = selectedSeats.join(',');
            document.getElementById('total-bayar-input').value = totalPrice;
            document.getElementById('continue-btn').disabled = selectedSeats.length === 0;
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
