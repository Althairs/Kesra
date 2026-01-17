<?php
// session_start();

include 'controller/login-controller.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login/signup</title>
    <link rel="shortcut icon" href="img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- SweetAlert CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <!-- login form -->
        <div class="form-box login">
            <form action="controller/login-controller.php" method="POST" id="loginForm">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="username" required
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                 <div class="input-box">
                    <select name="role" required>
                        <option value="">Pilih Jenis Pengguna</option>
                        <option value="user" <?php echo (isset($_POST['role']) && $_POST['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                    <i class="fa-solid fa-user-tag"></i>
                </div>
                <!-- <div class="forgot-link">
                    <a href="#">Lupa Kata Sandi?</a>
                </div> -->
                <button type="submit" name="login" class="btn">Masuk</button>
            </form>
        </div>
        <!-- register form -->
        <div class="form-box register">
            <form action="controller/login-controller.php" method="POST" id="registerForm">
                <h1>Daftar</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="username" required
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="email" required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="konfirmasi password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <button type="submit" name="register" class="btn">Daftar</button>
            </form>
        </div>
        <!-- toggle box -->
        <div class="toggle-box">
            <!-- toggle box kiri -->
            <div class="toggle-panel toggle-left">
                <h1>Hallo Selamat Datang!</h1>
                <p>Tidak Punya Akun?</p>
                <button class="btn register-btn">Daftar</button>
            </div>
            <!-- toggle box kanan -->
            <div class="toggle-panel toggle-right">
                <h1>Selamat Datang!</h1>
                <p>Sudah Punya Akun?</p>
                <button class="btn login-btn">Masuk</button>
            </div>
        </div>
    </div>
    <script src="js/login.js"></script>
    
    <!-- SweetAlert untuk menampilkan pesan -->
    <script>
        // Debug: cek jika session pesan ada
        <?php if (isset($_SESSION['pesan']) && !empty($_SESSION['pesan'])): ?>
            console.log('Pesan session ditemukan:', '<?php echo $_SESSION['pesan']; ?>');
            
            Swal.fire({
                icon: '<?php echo $_SESSION['type-pesan'] === 'success' ? 'success' : 'error'; ?>',
                title: '<?php echo $_SESSION['type-pesan'] === 'success' ? 'Berhasil' : 'Error'; ?>',
                text: '<?php echo $_SESSION['pesan']; ?>',
                confirmButtonColor: '#10B981',
                confirmButtonText: 'OK'
            }).then((result) => {
                // Clear session setelah alert ditutup
                <?php 
                unset($_SESSION['pesan']);
                unset($_SESSION['type-pesan']);
                ?>
            });
        <?php else: ?>
            console.log('Tidak ada pesan session');
        <?php endif; ?>

        // Validasi form dengan SweetAlert
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const username = this.querySelector('input[name="username"]').value;
            const password = this.querySelector('input[name="password"]').value;
            const role = this.querySelector('select[name="role"]').value;
            
            if (!username || !password || !role) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Harap isi semua field!',
                    confirmButtonColor: '#10B981'
                });
            }
        });

        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            const password = this.querySelector('input[name="password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Konfirmasi password tidak sesuai!',
                    confirmButtonColor: '#EF4444'
                });
            }
        });
    </script>
</body>
</html>