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
$jumlah_kursi = $_POST['jumlah_kursi'] ?? 0;

// Handle payment submission
if(isset($_POST['metode_pembayaran'])) {
    $metode_pembayaran = $_POST['metode_pembayaran'];
    
    // Generate booking ID
    $id_pemesanan = 'PM' . date('YmdHis');
    
    // Insert booking
    $sql_insert = "INSERT INTO pemesanan (id_pemesanan, id_penumpang, id_tiket, tanggal_pemesanan, total_bayar, metode_pembayaran) 
                   VALUES (?, ?, ?, CURDATE(), ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param('sssis', $id_pemesanan, $_SESSION['penumpang_id'], $id_tiket, $total_bayar, $metode_pembayaran);
    
    if($stmt_insert->execute()) {
        $_SESSION['success_message'] = 'Pemesanan berhasil! Kode booking: ' . $id_pemesanan;
        header('Location: penumpang/riwayat_pemesanan.php');
        exit();
    } else {
        $error = 'Gagal melakukan pemesanan. Silakan coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/global.css">
</head>
<body>
    <div class="container my-4">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <a href="konfirmasi_pemesanan.php" class="btn btn-light me-3" onclick="history.back(); return false;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0">Pembayaran</h5>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Booking Info -->
        <div class="alert alert-warning">
            <i class="bi bi-clock-history"></i> Selesaikan pembayaran sebelum <strong><?= date('d M Y H:i', strtotime('+2 hours')) ?></strong>
        </div>

        <!-- Price Summary -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Detail Perjalanan</h6>
                <div class="mb-2">
                    <span class="text-muted">Nomor Kursi:</span>
                    <span class="float-end fw-bold"><?= $jumlah_kursi ?> kursi</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Harga Tiket:</span>
                    <span class="float-end"><?= formatRupiah($total_bayar - 5000) ?></span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Biaya Layanan:</span>
                    <span class="float-end">Rp 5.000</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold fs-5 text-primary"><?= formatRupiah($total_bayar) ?></span>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="card mb-5">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Metode Pembayaran</h6>
                
                <form method="POST" action="">
                    <input type="hidden" name="id_keberangkatan" value="<?= $id_keberangkatan ?>">
                    <input type="hidden" name="id_tiket" value="<?= $id_tiket ?>">
                    <input type="hidden" name="selected_seats" value="<?= $selected_seats ?>">
                    <input type="hidden" name="total_bayar" value="<?= $total_bayar ?>">
                    <input type="hidden" name="jumlah_kursi" value="<?= $jumlah_kursi ?>">
                    
                    <!-- Virtual Account -->
                    <div class="payment-method-section mb-4">
                        <h6 class="text-muted mb-3">Virtual Account</h6>
                        
                        <div class="form-check payment-option mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="bca_va" value="transfer" required>
                            <label class="form-check-label w-100" for="bca_va">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-bank2 fs-4 me-3 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">BCA Virtual Account</div>
                                        <small class="text-muted">Transfer melalui ATM/Mobile Banking BCA</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        <div class="form-check payment-option mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="mandiri_va" value="transfer">
                            <label class="form-check-label w-100" for="mandiri_va">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-bank2 fs-4 me-3 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">Mandiri Virtual Account</div>
                                        <small class="text-muted">Transfer melalui ATM/Mobile Banking Mandiri</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="form-check payment-option mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="bri_va" value="transfer">
                            <label class="form-check-label w-100" for="bri_va">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-bank2 fs-4 me-3 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">BRI Virtual Account</div>
                                        <small class="text-muted">Transfer melalui ATM/Mobile Banking BRI</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- E-Wallet -->
                    <div class="payment-method-section mb-4">
                        <h6 class="text-muted mb-3">E-Wallet</h6>
                        
                        <div class="form-check payment-option mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="ewallet" value="e-wallet">
                            <label class="form-check-label w-100" for="ewallet">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-wallet2 fs-4 me-3 text-success"></i>
                                    <div>
                                        <div class="fw-bold">E-Wallet</div>
                                        <small class="text-muted">GoPay, OVO, DANA, ShopeePay</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Kartu Kredit/Debit -->
                    <div class="payment-method-section mb-4">
                        <h6 class="text-muted mb-3">Kartu Kredit/Debit</h6>
                        
                        <div class="form-check payment-option mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="kartu_kredit" value="kartu kredit">
                            <label class="form-check-label w-100" for="kartu_kredit">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card fs-4 me-3 text-warning"></i>
                                    <div>
                                        <div class="fw-bold">Kartu Kredit/Debit</div>
                                        <small class="text-muted">Visa, Mastercard, JCB</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Minimarket -->
                    <div class="payment-method-section mb-4">
                        <h6 class="text-muted mb-3">Minimarket</h6>
                        
                        <div class="form-check payment-option mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="minimarket" value="tunai">
                            <label class="form-check-label w-100" for="minimarket">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shop fs-4 me-3 text-danger"></i>
                                    <div>
                                        <div class="fw-bold">Minimarket</div>
                                        <small class="text-muted">Indomaret, Alfamart</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- QRIS -->
                    <div class="payment-method-section mb-5">
                        <h6 class="text-muted mb-3">QRIS</h6>
                        
                        <div class="form-check payment-option mb-3">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="qris" value="qris">
                            <label class="form-check-label w-100" for="qris">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-qr-code fs-4 me-3 text-info"></i>
                                    <div>
                                        <div class="fw-bold">QRIS</div>
                                        <small class="text-muted">Scan QR untuk bayar</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="fixed-bottom bg-white border-top p-3">
                        <div class="container">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div class="text-muted small">Total Bayar</div>
                                    <div class="fw-bold fs-5 text-primary"><?= formatRupiah($total_bayar) ?></div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-credit-card"></i> Bayar Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
