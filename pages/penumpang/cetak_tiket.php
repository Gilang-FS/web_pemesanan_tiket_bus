<?php
session_start();
require_once '../../config/config.php';
require_once '../../helpers/functions.php';

if(!isset($_SESSION['penumpang_id']) || !isset($_GET['id'])) {
    header('Location: riwayat_pemesanan.php');
    exit();
}

$conn = getConnection();
$id_pemesanan = $_GET['id'];
$id_penumpang = $_SESSION['penumpang_id'];

// Ambil detail pemesanan
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
                            d.no_telephone as driver_phone,
                            b.nama_bus,
                            b.kapasitas,
                            pn.nama_penumpang,
                            pn.alamat,
                            pn.no_telephone as penumpang_phone,
                            pn.jenis_kelamin
                        FROM pemesanan pm
                        INNER JOIN tiket t ON pm.id_tiket = t.id_tiket
                        INNER JOIN penumpang pn ON pm.id_penumpang = pn.id_penumpang
                        LEFT JOIN keberangkatan k ON k.id_penumpang = pm.id_penumpang
                        LEFT JOIN jadwal j ON k.id_jadwal = j.id_jadwal
                        LEFT JOIN driver d ON k.id_driver = d.id_driver
                        LEFT JOIN pengendaraan p ON d.id_driver = p.id_driver
                        LEFT JOIN bus b ON p.id_bus = b.id_bus
                        WHERE pm.id_pemesanan = ? AND pm.id_penumpang = ?");
$stmt->bind_param("ss", $id_pemesanan, $id_penumpang);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    header('Location: riwayat_pemesanan.php');
    exit();
}

$tiket = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tiket - <?= htmlspecialchars($tiket['id_pemesanan']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/cetak-tiket.css">
</head>
<body>
    <div class="no-print text-end p-3">
        <button onclick="window.print()" class="btn btn-primary me-2">
            <i class="bi bi-printer"></i> Cetak Tiket
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Tutup
        </button>
    </div>

    <div class="ticket-container">
        <div class="ticket">
            <!-- Header Tiket -->
            <div class="ticket-header">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h2 class="mb-0"><i class="bi bi-bus-front"></i> Tiket Bus</h2>
                        <p class="mb-0 text-muted">E-Ticket Pemesanan</p>
                    </div>
                    <div class="col-4 text-end">
                        <div class="barcode">
                            <svg width="120" height="50">
                                <rect x="0" y="0" width="4" height="50" fill="#000"/>
                                <rect x="6" y="0" width="2" height="50" fill="#000"/>
                                <rect x="10" y="0" width="6" height="50" fill="#000"/>
                                <rect x="18" y="0" width="2" height="50" fill="#000"/>
                                <rect x="22" y="0" width="4" height="50" fill="#000"/>
                                <rect x="28" y="0" width="2" height="50" fill="#000"/>
                                <rect x="32" y="0" width="6" height="50" fill="#000"/>
                                <rect x="40" y="0" width="4" height="50" fill="#000"/>
                                <rect x="46" y="0" width="2" height="50" fill="#000"/>
                                <rect x="50" y="0" width="6" height="50" fill="#000"/>
                                <rect x="58" y="0" width="2" height="50" fill="#000"/>
                                <rect x="62" y="0" width="4" height="50" fill="#000"/>
                                <rect x="68" y="0" width="6" height="50" fill="#000"/>
                                <rect x="76" y="0" width="2" height="50" fill="#000"/>
                                <rect x="80" y="0" width="4" height="50" fill="#000"/>
                                <rect x="86" y="0" width="2" height="50" fill="#000"/>
                                <rect x="90" y="0" width="6" height="50" fill="#000"/>
                                <rect x="98" y="0" width="4" height="50" fill="#000"/>
                                <rect x="104" y="0" width="2" height="50" fill="#000"/>
                                <rect x="108" y="0" width="6" height="50" fill="#000"/>
                                <rect x="116" y="0" width="4" height="50" fill="#000"/>
                            </svg>
                        </div>
                        <small class="d-block mt-1"><?= htmlspecialchars($tiket['id_pemesanan']) ?></small>
                    </div>
                </div>
            </div>

            <div class="ticket-divider"></div>

            <!-- Informasi Perjalanan -->
            <div class="ticket-section">
                <h5 class="section-title">Informasi Perjalanan</h5>
                <div class="row">
                    <div class="col-5">
                        <div class="location-box">
                            <i class="bi bi-geo-fill text-primary"></i>
                            <div>
                                <small class="text-muted">Dari</small>
                                <h4 class="mb-0"><?= htmlspecialchars($tiket['kota_asal'] ?? '-') ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <i class="bi bi-arrow-right" style="font-size: 2rem; color: #0d6efd;"></i>
                    </div>
                    <div class="col-5">
                        <div class="location-box">
                            <i class="bi bi-geo-fill text-danger"></i>
                            <div>
                                <small class="text-muted">Ke</small>
                                <h4 class="mb-0"><?= htmlspecialchars($tiket['kota_tujuan'] ?? '-') ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-4">
                        <div class="info-item">
                            <i class="bi bi-calendar-event"></i>
                            <div>
                                <small>Tanggal Berangkat</small>
                                <strong><?= $tiket['tanggal_keberangkatan'] ? date('d M Y', strtotime($tiket['tanggal_keberangkatan'])) : '-' ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="info-item">
                            <i class="bi bi-bus-front-fill"></i>
                            <div>
                                <small>Nama Bus</small>
                                <strong><?= htmlspecialchars($tiket['nama_bus'] ?? '-') ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="info-item">
                            <i class="bi bi-star-fill"></i>
                            <div>
                                <small>Kelas</small>
                                <strong><?= htmlspecialchars($tiket['tipe_kelas']) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ticket-divider"></div>

            <!-- Informasi Penumpang -->
            <div class="ticket-section">
                <h5 class="section-title">Informasi Penumpang</h5>
                <div class="row">
                    <div class="col-6">
                        <div class="info-item">
                            <i class="bi bi-person-fill"></i>
                            <div>
                                <small>Nama Penumpang</small>
                                <strong><?= htmlspecialchars($tiket['nama_penumpang']) ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-item">
                            <i class="bi bi-telephone-fill"></i>
                            <div>
                                <small>No. Telepon</small>
                                <strong><?= htmlspecialchars($tiket['penumpang_phone']) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6">
                        <div class="info-item">
                            <i class="bi bi-gender-ambiguous"></i>
                            <div>
                                <small>Jenis Kelamin</small>
                                <strong><?= $tiket['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>
                                <small>Alamat</small>
                                <strong><?= htmlspecialchars($tiket['alamat']) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ticket-divider"></div>

            <!-- Detail Tiket -->
            <div class="ticket-section">
                <h5 class="section-title">Detail Tiket</h5>
                <div class="row">
                    <div class="col-4">
                        <div class="seat-info">
                            <i class="bi bi-chat-square-dots-fill"></i>
                            <div>
                                <small>No. Kursi</small>
                                <h3 class="text-primary mb-0"><?= htmlspecialchars($tiket['no_kursi']) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="info-item">
                            <i class="bi bi-person-badge-fill"></i>
                            <div>
                                <small>Driver</small>
                                <strong><?= htmlspecialchars($tiket['nama_driver'] ?? '-') ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="info-item">
                            <i class="bi bi-credit-card-fill"></i>
                            <div>
                                <small>Metode Pembayaran</small>
                                <strong><?= htmlspecialchars(ucfirst($tiket['metode_pembayaran'])) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ticket-divider"></div>

            <!-- Total Pembayaran -->
            <div class="ticket-section">
                <div class="row align-items-center">
                    <div class="col-6">
                        <small class="text-muted">Tanggal Pemesanan</small>
                        <p class="mb-0"><?= date('d M Y H:i', strtotime($tiket['tanggal_pemesanan'])) ?></p>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted">Total Pembayaran</small>
                        <h3 class="text-primary mb-0"><?= formatRupiah($tiket['total_bayar']) ?></h3>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="ticket-footer">
                <div class="row">
                    <div class="col-12 text-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Harap tunjukkan tiket ini kepada petugas saat keberangkatan. 
                            Simpan tiket ini dengan baik.
                        </small>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 text-center">
                        <small class="text-muted">
                            Terima kasih telah menggunakan layanan kami. Selamat Jalan!
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto print option (optional)
        // window.onload = function() { 
        //     window.print(); 
        // }
    </script>
</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>
