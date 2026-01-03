-- Database: penjualantiketbus
CREATE DATABASE IF NOT EXISTS penjualantiketbus;
USE penjualantiketbus;

-- Tabel penumpang
CREATE TABLE penumpang (
    id_penumpang VARCHAR(20) PRIMARY KEY,
    nama_penumpang VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_telephone VARCHAR(20) NOT NULL,
    jenis_kelamin ENUM('L','P') NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel driver
CREATE TABLE driver (
    id_driver VARCHAR(20) PRIMARY KEY,
    nama_driver VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_telephone VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel bus
CREATE TABLE bus (
    id_bus VARCHAR(20) PRIMARY KEY,
    nama_bus VARCHAR(100) NOT NULL,
    kapasitas INT NOT NULL,
    status ENUM('aktif','tidak aktif','dalam perawatan') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel jadwal
CREATE TABLE jadwal (
    id_jadwal VARCHAR(20) PRIMARY KEY,
    kota_asal VARCHAR(100) NOT NULL,
    kota_tujuan VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel tiket
CREATE TABLE tiket (
    id_tiket VARCHAR(20) PRIMARY KEY,
    no_kursi INT NOT NULL,
    harga INT NOT NULL,
    tipe_kelas ENUM('ekonomi','bisnis','executive','super executive','sleeper','vip','double decker') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel pemesanan
CREATE TABLE pemesanan (
    id_pemesanan VARCHAR(20) PRIMARY KEY,
    id_penumpang VARCHAR(20) NOT NULL,
    id_tiket VARCHAR(20) NOT NULL,
    tanggal_pemesanan DATE NOT NULL,
    total_bayar INT NOT NULL,
    metode_pembayaran ENUM('tunai','e-wallet','transfer','kartu kredit','qris') NOT NULL,
    FOREIGN KEY (id_penumpang) REFERENCES penumpang(id_penumpang) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id_tiket) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel keberangkatan
CREATE TABLE keberangkatan (
    id_keberangkatan VARCHAR(20) PRIMARY KEY,
    id_penumpang VARCHAR(20) NOT NULL,
    id_driver VARCHAR(20) NOT NULL,
    id_jadwal VARCHAR(20) NOT NULL,
    jumlah_penumpang INT NOT NULL,
    tanggal_keberangkatan DATE NOT NULL,
    FOREIGN KEY (id_penumpang) REFERENCES penumpang(id_penumpang) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_driver) REFERENCES driver(id_driver) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_jadwal) REFERENCES jadwal(id_jadwal) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel pengendaraan
CREATE TABLE pengendaraan (
    id_bus VARCHAR(20) NOT NULL,
    id_driver VARCHAR(20) NOT NULL,
    PRIMARY KEY (id_bus, id_driver),
    FOREIGN KEY (id_bus) REFERENCES bus(id_bus) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_driver) REFERENCES driver(id_driver) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data untuk penumpang (password: password123)
INSERT INTO penumpang (id_penumpang, nama_penumpang, alamat, no_telephone, jenis_kelamin, password) VALUES
('P001', 'Andi', 'Jakarta', '081234567890', 'L', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P002', 'Sinta', 'Bandung', '081298765432', 'P', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P003', 'Rudi', 'Jambi', '081377788899', 'L', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P004', 'Nina', 'Yogyakarta', '082111223344', 'P', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P005', 'Bima', 'Semarang', '083311445566', 'L', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P006', 'Lina', 'Jakarta', '081255566677', 'P', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P007', 'Aldi', 'Malang', '085233344455', 'L', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P008', 'Tina', 'Surabaya', '081233344455', 'P', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P009', 'Rama', 'Depok', '082233344455', 'L', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P010', 'Dewi', 'Bogor', '083822233344', 'P', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P011', 'Yanto', 'Temanggung', '081222333888', 'L', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('P2026010215050538', 'Gilang Prayudha A', 'Tamanan', '08123456789', 'L', '$2y$10$SAdiRaS7wMlJ8c7KrfHVGOy9zPqcaZXIWPfR6M37vCkr1234567'),
('P2026010215323523', 'dino', 'tamanan', '08123345', 'L', '$2y$10$EsPMDRSm1r0eTrWhyC3h6ucN3TkYWuNxaZ5678901234567890AB');

-- Insert sample data untuk driver
INSERT INTO driver (id_driver, nama_driver, alamat, no_telephone) VALUES
('D001', 'Budi', 'Jakarta', '081111111111'),
('D002', 'Anton', 'Bogor', '081122223333'),
('D003', 'Tono', 'Bandung', '081133344455'),
('D004', 'Rudi', 'Depok', '081144455566'),
('D005', 'Eko', 'Surabaya', '081155566677'),
('D006', 'Wawan', 'Semarang', '081166677788'),
('D007', 'Dani', 'Malang', '081177788899'),
('D008', 'Hadi', 'Jakarta', '081188899900'),
('D009', 'Rama', 'Bandung', '081199900011'),
('D010', 'Andra', 'Yogyakarta', '081200011122');

-- Insert sample data untuk bus
INSERT INTO bus (id_bus, nama_bus, kapasitas, status) VALUES
('B001', 'GunungJaya', 40, 'aktif'),
('B002', 'LintasSejahtera', 35, 'aktif'),
('B003', 'HarapanIndah', 30, 'dalam perawatan'),
('B004', 'RimbaMas', 50, 'tidak aktif'),
('B005', 'SinarUtama', 40, 'aktif'),
('B006', 'MegaTrans', 45, 'aktif'),
('B007', 'PutraJaya', 35, 'tidak aktif'),
('B008', 'Baruna', 30, 'aktif'),
('B009', 'GunungMas', 32, 'dalam perawatan'),
('B010', 'Safari', 38, 'aktif');

-- Insert sample data untuk jadwal
INSERT INTO jadwal (id_jadwal, kota_asal, kota_tujuan) VALUES
('J001', 'Jakarta', 'Bandung'),
('J002', 'Bandung', 'Jakarta'),
('J003', 'Surabaya', 'Yogyakarta'),
('J004', 'Yogyakarta', 'Jakarta'),
('J005', 'Semarang', 'Surabaya'),
('J006', 'Malang', 'Jakarta'),
('J007', 'Jakarta', 'Malang'),
('J008', 'Bogor', 'Bandung'),
('J009', 'Depok', 'Jakarta'),
('J010', 'Jakarta', 'Semarang');

-- Insert sample data untuk tiket
INSERT INTO tiket (id_tiket, no_kursi, harga, tipe_kelas) VALUES
('T001', 1, 150000, 'ekonomi'),
('T002', 2, 175000, 'bisnis'),
('T003', 3, 200000, 'executive'),
('T004', 4, 225000, 'super executive'),
('T005', 5, 250000, 'sleeper'),
('T006', 6, 275000, 'vip'),
('T007', 7, 300000, 'double decker'),
('T008', 8, 160000, 'ekonomi'),
('T009', 9, 190000, 'bisnis'),
('T010', 10, 280000, 'vip');

-- Insert sample data untuk pemesanan
INSERT INTO pemesanan (id_pemesanan, id_penumpang, id_tiket, tanggal_pemesanan, total_bayar, metode_pembayaran) VALUES
('PM001', 'P001', 'T001', '2025-11-01', 150000, 'transfer'),
('PM002', 'P002', 'T002', '2025-11-02', 150000, 'qris'),
('PM003', 'P003', 'T003', '2025-11-03', 200000, 'tunai'),
('PM004', 'P004', 'T004', '2025-11-03', 250000, 'kartu kredit'),
('PM005', 'P005', 'T005', '2025-11-04', 150000, 'e-wallet'),
('PM006', 'P006', 'T006', '2025-11-05', 200000, 'transfer'),
('PM007', 'P007', 'T007', '2025-11-05', 250000, 'qris'),
('PM008', 'P008', 'T008', '2025-11-06', 150000, 'e-wallet'),
('PM009', 'P009', 'T009', '2025-11-06', 200000, 'kartu kredit'),
('PM010', 'P010', 'T010', '2025-11-07', 250000, 'tunai');

-- Insert sample data untuk keberangkatan
INSERT INTO keberangkatan (id_keberangkatan, id_penumpang, id_driver, id_jadwal, jumlah_penumpang, tanggal_keberangkatan) VALUES
('K001', 'P001', 'D001', 'J001', 32, '2025-11-05'),
('K002', 'P002', 'D002', 'J002', 27, '2025-11-05'),
('K003', 'P003', 'D003', 'J003', 41, '2025-11-06'),
('K004', 'P004', 'D004', 'J004', 38, '2025-11-06'),
('K005', 'P005', 'D005', 'J005', 25, '2025-11-06'),
('K006', 'P006', 'D006', 'J006', 44, '2025-11-07'),
('K007', 'P007', 'D007', 'J007', 36, '2025-11-07'),
('K008', 'P008', 'D008', 'J008', 29, '2025-11-07'),
('K009', 'P009', 'D009', 'J009', 40, '2025-11-08'),
('K010', 'P010', 'D010', 'J010', 33, '2025-11-08');

-- Insert sample data untuk pengendaraan
INSERT INTO pengendaraan (id_bus, id_driver) VALUES
('B001', 'D001'),
('B002', 'D002'),
('B003', 'D003'),
('B004', 'D004'),
('B005', 'D005'),
('B006', 'D006'),
('B007', 'D007'),
('B008', 'D008'),
('B009', 'D009'),
('B010', 'D010'),
('B001', 'D001'),
('B002', 'D002'),
('B003', 'D003'),
('B004', 'D004'),
('B005', 'D005'),
('B006', 'D006'),
('B007', 'D007'),
('B008', 'D008'),
('B009', 'D009'),
('B010', 'D010');
