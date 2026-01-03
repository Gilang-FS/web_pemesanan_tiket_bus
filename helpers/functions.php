<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Generate unique booking code
 */
function generateKodeBooking() {
    $prefix = 'BUS';
    $timestamp = date('YmdHis');
    $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    return $prefix . $timestamp . $random;
}

/**
 * Check if user is logged in
 */
function cekLogin() {
    if (!isset($_SESSION['penumpang_id'])) {
        $redirect_path = (strpos($_SERVER['REQUEST_URI'], '/penumpang/') !== false) 
            ? '../auth/login.php' 
            : 'auth/login.php';
        header('Location: ' . $redirect_path);
        exit();
    }
}

/**
 * Get logged in user data
 */
function getPenumpangData($conn, $id_penumpang) {
    $stmt = $conn->prepare("SELECT id_penumpang, nama_penumpang, alamat, no_telephone, jenis_kelamin FROM penumpang WHERE id_penumpang = ?");
    $stmt->bind_param("s", $id_penumpang);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data;
}

/**
 * Format currency to IDR
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Format date to Indonesian format
 */
function formatTanggalIndo($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Redirect to a URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}
?>
