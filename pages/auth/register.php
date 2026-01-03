<?php
session_start();
require_once '../../config/config.php';
require_once '../../helpers/functions.php';

if(isset($_POST['register'])) {
    $conn = getConnection();
    $nama_penumpang = sanitizeInput($_POST['nama_penumpang']);
    $alamat = sanitizeInput($_POST['alamat']);
    $no_telephone = sanitizeInput($_POST['no_telephone']);
    $jenis_kelamin = sanitizeInput($_POST['jenis_kelamin']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak sama!";
    } else {
        // Check if phone number already exists
        $stmt = $conn->prepare("SELECT id_penumpang FROM penumpang WHERE no_telephone = ?");
        $stmt->bind_param("s", $no_telephone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $error = "No. Telepon sudah terdaftar!";
        } else {
            $id_penumpang = 'P' . date('YmdHis');
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO penumpang (id_penumpang, nama_penumpang, alamat, no_telephone, jenis_kelamin, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $id_penumpang, $nama_penumpang, $alamat, $no_telephone, $jenis_kelamin, $hashed_password);
            
            if($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan login.";
                // Auto redirect to login after 2 seconds
                header("refresh:2;url=login.php");
            } else {
                $error = "Registrasi gagal! Silakan coba lagi.";
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Pemesanan Tiket Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body class="bg-light">
    <div class="container auth-container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6">
                <div class="card shadow-sm auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <!-- <img src="/placeholder.svg?height=200&width=400" alt="Bus" class="img-fluid mb-3" style="max-height: 150px;"> -->
                            <h3 class="mt-3">Mulai Perjalananmu</h3>
                            <p class="text-muted">Masuk untuk memesan tiket dan mulai perjalanan</p>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-circle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($success)): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="bi bi-check-circle"></i> <?= $success ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="auth-form">
                            <div class="mb-3">
                                <label for="no_telephone" class="form-label">
                                    <i class="bi bi-envelope"></i> Email/No. Telepon
                                </label>
                                <input type="text" class="form-control" id="no_telephone" name="no_telephone" 
                                       placeholder="contoh@email.com" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nama_penumpang" class="form-label">
                                    <i class="bi bi-person"></i> Nama Lengkap
                                </label>
                                <input type="text" class="form-control" id="nama_penumpang" name="nama_penumpang" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat" class="form-label">
                                    <i class="bi bi-geo-alt"></i> Alamat
                                </label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">
                                    <i class="bi bi-gender-ambiguous"></i> Jenis Kelamin
                                </label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Kata Sandi
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Minimal 8 karakter</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="bi bi-lock-fill"></i> Konfirmasi Kata Sandi
                                </label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <button type="submit" name="register" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="bi bi-person-plus"></i> Masuk
                            </button>
                            
                            <div class="text-center mb-3">
                                <span class="text-muted">atau masuk dengan</span>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-google"></i> Google
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-facebook"></i> Facebook
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p class="text-muted">Belum punya akun? <a href="login.php" class="auth-link text-primary fw-bold">Daftar Sekarang</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
