# Changelog

## [1.0.0] - 2025-01-03

### Added
- ✨ Landing page dengan hero section dan informasi lengkap
- 🔐 Sistem autentikasi (registrasi & login) dengan password hashing
- 📅 Halaman jadwal dengan filter pencarian (kota asal, tujuan, tanggal)
- 🚌 Halaman detail bus dengan informasi fasilitas dan ulasan pengguna
- 💺 Fitur pemilihan kursi interaktif dengan visualisasi layout bus
- 📋 Halaman konfirmasi pemesanan dengan rincian lengkap
- 💳 Sistem pembayaran dengan 5 metode (Transfer, E-Wallet, Kartu Kredit, QRIS, Tunai)
- 📊 Dashboard penumpang dengan statistik pemesanan
- 📜 Halaman riwayat pemesanan dengan detail lengkap
- 🎨 Responsive design dengan Bootstrap 5
- 🔒 Keamanan: Prepared statements, password hashing, session management

### Database
- Struktur database lengkap dengan 8 tabel
- Foreign key constraints dengan ON UPDATE CASCADE dan ON DELETE RESTRICT
- Sample data untuk testing (10 penumpang, 10 driver, 10 bus, 10 jadwal, 10 tiket)
- Relasi antar tabel sesuai ERD

### Security
- Password hashing menggunakan bcrypt
- Prepared statements untuk semua query
- Input sanitization dan validation
- Session-based authentication
- XSS protection

### UI/UX
- Modern dan clean interface
- Mobile-friendly responsive design
- Smooth transitions dan hover effects
- Interactive seat selection
- Color-coded seat status (Available, Selected, Taken)
- Professional payment method selection
- User-friendly navigation

### Features
- Multi-class ticket system (7 kelas: Ekonomi sampai Double Decker)
- Real-time seat availability
- Automatic booking ID generation
- Payment method selection
- Booking history with print option
- Search and filter schedules
- Bus facilities display
- Customer reviews section

---

## Future Updates (Planned)

### [1.1.0] - Upcoming
- [ ] Print ticket functionality
- [ ] Email notification after booking
- [ ] SMS notification
- [ ] Online payment gateway integration
- [ ] QR code for ticket verification
- [ ] Bus tracking real-time
- [ ] Rating and review system for completed trips
- [ ] Promo code and discount system
- [ ] Loyalty points program

### [1.2.0] - Future
- [ ] Admin dashboard
- [ ] Bus seat management
- [ ] Driver schedule management
- [ ] Revenue reports
- [ ] Customer analytics
- [ ] Mobile app (Android & iOS)
- [ ] Push notifications
- [ ] Multi-language support

---

**Legend:**
- ✨ New Feature
- 🔐 Security
- 🐛 Bug Fix
- 📝 Documentation
- 🎨 UI/UX Improvement
- ⚡ Performance
