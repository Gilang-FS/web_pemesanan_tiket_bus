<?php
session_start();
require_once '../../config/config.php';
require_once '../../helpers/functions.php';

if(isset($_POST['login'])) {
    $conn = getConnection();
    $no_telephone = sanitizeInput($_POST['no_telephone']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id_penumpang, nama_penumpang, password FROM penumpang WHERE no_telephone = ?");
    $stmt->bind_param("s", $no_telephone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])) {
            $_SESSION['penumpang_id'] = $user['id_penumpang'];
            $_SESSION['nama_penumpang'] = $user['nama_penumpang'];
            
            // Redirect to jadwal if coming from there, otherwise dashboard
            if(isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: ../../" . $redirect);
            } else {
                header("Location: ../penumpang/dashboard.php");
            }
            exit();
        } else {
            $error = "No. Telepon atau password salah!";
        }
    } else {
        $error = "No. Telepon atau password salah!";
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Pemesanan Tiket Bus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body class="bg-light">
    <div class="container auth-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-sm auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <!-- <img src="/placeholder.svg?height=200&width=400" alt="Bus" class="img-fluid mb-3" style="max-height: 150px; border-radius: 1rem;"> -->
                            <h3 class="mt-3 fw-bold">Mulai Perjalananmu</h3>
                            <p class="text-muted">Masuk untuk memesan tiket dan mulai perjalanan</p>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-circle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="auth-form">
                            <div class="mb-3">
                                <label for="no_telephone" class="form-label">
                                    <i class="bi bi-envelope"></i> Email/Nomor Telepon
                                </label>
                                <input type="text" class="form-control" id="no_telephone" name="no_telephone" 
                                       placeholder="contoh@email.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Kata Sandi
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="••••••••" required>
                                <div class="text-end mt-2">
                                    <a href="#" class="text-primary small">Lupa Kata Sandi?</a>
                                </div>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
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
                            <p class="text-muted">Belum punya akun? <a href="register.php" class="text-primary fw-bold">Daftar Sekarang</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
