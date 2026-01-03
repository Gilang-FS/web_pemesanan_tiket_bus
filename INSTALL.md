# Panduan Instalasi Sistem Pemesanan Tiket Bus

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx)
- phpMyAdmin (opsional, untuk manajemen database)

## Langkah-Langkah Instalasi

### 1. Persiapan

Download atau clone repository ini ke komputer Anda:
```bash
git clone <repository-url>
```

Atau extract file ZIP ke folder htdocs/www di web server Anda.

### 2. Setup Database

**Opsi A: Menggunakan phpMyAdmin**

1. Buka phpMyAdmin di browser: `http://localhost/phpmyadmin`
2. Klik tab "Import"
3. Klik "Choose File" dan pilih file `database/penjualantiketbus.sql`
4. Klik "Go" untuk mengimport database

**Opsi B: Menggunakan Command Line**

```bash
# Login ke MySQL
mysql -u root -p

# Jalankan perintah berikut di MySQL prompt
source /path/to/database/penjualantiketbus.sql;

# Atau langsung dari terminal
mysql -u root -p < database/penjualantiketbus.sql
```

### 3. Konfigurasi Database

Edit file `config/config.php` dan sesuaikan dengan pengaturan database Anda:

```php
<?php
define('DB_HOST', 'localhost');     // Host database
define('DB_USER', 'root');          // Username database
define('DB_PASS', '');              // Password database (kosongkan jika tidak ada)
define('DB_NAME', 'penjualantiketbus'); // Nama database
?>
```

### 4. Pengaturan Web Server

**XAMPP (Windows/Mac/Linux):**
1. Copy folder project ke `C:\xampp\htdocs\` (Windows) atau `/Applications/XAMPP/htdocs/` (Mac)
2. Start Apache dan MySQL dari XAMPP Control Panel
3. Akses aplikasi di browser: `http://localhost/penjualantiketbus`

**WAMP (Windows):**
1. Copy folder project ke `C:\wamp64\www\`
2. Start All Services dari WAMP
3. Akses aplikasi di browser: `http://localhost/penjualantiketbus`

**Laragon (Windows):**
1. Copy folder project ke `C:\laragon\www\`
2. Start All dari Laragon
3. Akses aplikasi di browser: `http://penjualantiketbus.test`

### 5. Testing Aplikasi

#### A. Akses Halaman Utama
Buka browser dan akses: `http://localhost/penjualantiketbus`

Anda akan melihat landing page dengan menu:
- Jadwal
- Fitur
- Mengapa Kami
- Kontak

#### B. Registrasi Akun Baru

1. Klik tombol "Daftar" di navbar
2. Isi form registrasi:
   - Email/No. Telepon: `081234567890`
   - Nama Lengkap: `John Doe`
   - Alamat: `Jakarta`
   - Jenis Kelamin: Pilih `Laki-laki` atau `Perempuan`
   - Kata Sandi: `password123`
   - Konfirmasi Kata Sandi: `password123`
3. Klik "Masuk" (tombol submit)
4. Anda akan diarahkan ke halaman login

#### C. Login

1. Klik tombol "Masuk" di navbar
2. Masukkan kredensial:
   - Email/No. Telepon: `081234567890`
   - Kata Sandi: `password123`
3. Klik "Masuk"
4. Anda akan diarahkan ke dashboard penumpang

**Akun Testing yang Sudah Tersedia:**

Database sudah berisi beberapa akun testing dengan password: `password123`
- No. Telepon: `081234567890` (Andi)
- No. Telepon: `081298765432` (Sinta)
- No. Telepon: `081377788899` (Rudi)

#### D. Melihat Jadwal dan Memesan Tiket

1. Klik menu "Jadwal" di navbar
2. Pilih rute yang diinginkan (contoh: Jakarta → Bandung)
3. Klik "Lihat Detail & Pilih Kursi"
4. Lihat detail bus, fasilitas, dan ulasan
5. Klik "Pilih Kursi"
6. Pilih kursi yang tersedia
7. Klik "Lanjut ke Pembayaran"
8. Review konfirmasi pemesanan
9. Klik "Lanjut Pembayaran"
10. Pilih metode pembayaran (Transfer/E-Wallet/Kartu Kredit/QRIS/Tunai)
11. Klik "Bayar Sekarang"
12. Pemesanan berhasil!

#### E. Melihat Riwayat Pemesanan

1. Klik menu "Riwayat" di navbar
2. Lihat semua pemesanan yang telah dilakukan
3. Klik "Cetak Tiket" untuk mencetak (fitur dalam pengembangan)

## Struktur Database

### Tabel-Tabel Utama

1. **penumpang** - Menyimpan data penumpang/user
   - id_penumpang (VARCHAR, PRIMARY KEY)
   - nama_penumpang (VARCHAR)
   - alamat (TEXT)
   - no_telephone (VARCHAR)
   - jenis_kelamin (ENUM: 'L', 'P')
   - password (VARCHAR, hashed)

2. **bus** - Menyimpan data bus
   - id_bus (VARCHAR, PRIMARY KEY)
   - nama_bus (VARCHAR)
   - kapasitas (INT)
   - status (ENUM: 'aktif', 'tidak aktif', 'dalam perawatan')

3. **driver** - Menyimpan data driver
   - id_driver (VARCHAR, PRIMARY KEY)
   - nama_driver (VARCHAR)
   - alamat (TEXT)
   - no_telephone (VARCHAR)

4. **jadwal** - Menyimpan jadwal rute
   - id_jadwal (VARCHAR, PRIMARY KEY)
   - kota_asal (VARCHAR)
   - kota_tujuan (VARCHAR)

5. **tiket** - Menyimpan data tiket
   - id_tiket (VARCHAR, PRIMARY KEY)
   - no_kursi (INT)
   - harga (INT)
   - tipe_kelas (ENUM: 'ekonomi', 'bisnis', 'executive', 'super executive', 'sleeper', 'vip', 'double decker')

6. **pemesanan** - Menyimpan data pemesanan
   - id_pemesanan (VARCHAR, PRIMARY KEY)
   - id_penumpang (VARCHAR, FOREIGN KEY)
   - id_tiket (VARCHAR, FOREIGN KEY)
   - tanggal_pemesanan (DATE)
   - total_bayar (INT)
   - metode_pembayaran (ENUM: 'tunai', 'e-wallet', 'transfer', 'kartu kredit', 'qris')

7. **keberangkatan** - Menyimpan data keberangkatan
   - id_keberangkatan (VARCHAR, PRIMARY KEY)
   - id_penumpang (VARCHAR, FOREIGN KEY)
   - id_driver (VARCHAR, FOREIGN KEY)
   - id_jadwal (VARCHAR, FOREIGN KEY)
   - jumlah_penumpang (INT)
   - tanggal_keberangkatan (DATE)

8. **pengendaraan** - Relasi bus dan driver
   - id_bus (VARCHAR, FOREIGN KEY)
   - id_driver (VARCHAR, FOREIGN KEY)
   - PRIMARY KEY (id_bus, id_driver)

## Troubleshooting

### Error: "Access denied for user"
**Solusi:** Periksa kredensial database di `config/config.php`

### Error: "Table doesn't exist"
**Solusi:** Import ulang file SQL database

### Error: "Call to undefined function mysqli_connect()"
**Solusi:** Aktifkan extension mysqli di php.ini
```ini
extension=mysqli
```

### Error: "Cannot modify header information"
**Solusi:** Pastikan tidak ada output (echo/print/whitespace) sebelum session_start()

### Halaman Blank/White Screen
**Solusi:** 
1. Aktifkan error reporting di php.ini:
```ini
display_errors = On
error_reporting = E_ALL
```
2. Atau tambahkan di awal file PHP:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

### Error: "Failed to connect to MySQL"
**Solusi:**
1. Pastikan MySQL service sudah running
2. Periksa port MySQL (default: 3306)
3. Cek firewall

## Fitur yang Tersedia

✅ Landing Page dengan informasi lengkap
✅ Registrasi dan Login dengan password hashing
✅ Melihat jadwal perjalanan dengan filter pencarian
✅ Detail bus dengan fasilitas dan ulasan
✅ Pemilihan kursi interaktif
✅ Konfirmasi pemesanan dengan rincian harga
✅ Multiple metode pembayaran
✅ Dashboard penumpang
✅ Riwayat pemesanan
✅ Responsive design (mobile-friendly)

## Keamanan

- Password di-hash menggunakan `password_hash()` dengan algoritma bcrypt
- Semua query database menggunakan prepared statements
- Input validation dan sanitization
- Session-based authentication
- XSS protection dengan `htmlspecialchars()`
- SQL injection protection dengan prepared statements

## Dukungan

Jika mengalami masalah atau memiliki pertanyaan:
- Buka file README.md untuk dokumentasi lengkap
- Periksa file troubleshooting di atas
- Hubungi: info@busticket.com

---

**Selamat mencoba! Happy Coding! 🚀**
