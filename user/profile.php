<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$user_data = [];

try {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
}

// Jika tidak ada data, gunakan data session
if (!$user_data) {
    $user_data = [
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'] ?? '',
        'nama_lengkap' => $_SESSION['username'],
        'telepon' => '',
        'alamat' => '',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// Tentukan form mana yang aktif
$active_form = $_GET['form'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-user.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/dashboard-user.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'komponen/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Baru yang Lebih Bagus -->
           

            <!-- Content Area -->
            <div class="content-area">
                <!-- Alert Messages -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_messages'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div style="flex: 1;">
                            <strong>Terjadi kesalahan:</strong>
                            <ul>
                                <?php foreach ($_SESSION['error_messages'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php unset($_SESSION['error_messages']); ?>
                <?php endif; ?>

                <div class="section-header">
                    <h2>Informasi Profil</h2>
                    <p>Detail informasi akun pribadi Anda</p>
                </div>

                <!-- Tampilan Profil (Default) -->
                <?php if ($active_form == ''): ?>
                <div class="profile-card">
                    <div class="profile-header-card">
                        <div class="profile-identity">
                            <div class="profile-avatar user-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="profile-info">
                                <h3><?php echo htmlspecialchars($user_data['nama_lengkap'] ?: $user_data['username']); ?></h3>
                                <p class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></p>
                                <div class="profile-role user-role">
                                    <i class="fas fa-user"></i>
                                    <span>User</span>
                                </div>
                            </div>
                        </div>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-label">Bergabung</span>
                                    <span class="stat-value"><?php echo date('d M Y', strtotime($user_data['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-details-grid">
                        <div class="detail-section">
                            <h4 class="section-title">
                                <i class="fas fa-id-card"></i>
                                Informasi Akun
                            </h4>
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="detail-content">
                                    <label>Username</label>
                                    <span><?php echo htmlspecialchars($user_data['username']); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="detail-content">
                                    <label>Email</label>
                                    <span><?php echo htmlspecialchars($user_data['email']); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <div class="detail-content">
                                    <label>Role</label>
                                    <span>User</span>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h4 class="section-title">
                                <i class="fas fa-address-book"></i>
                                Informasi Pribadi
                            </h4>
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-signature"></i>
                                </div>
                                <div class="detail-content">
                                    <label>Nama Lengkap</label>
                                    <span><?php echo htmlspecialchars($user_data['nama_lengkap'] ?: '-'); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="detail-content">
                                    <label>Nomor Telepon</label>
                                    <span><?php echo htmlspecialchars($user_data['telepon'] ?: '-'); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="detail-content">
                                    <label>Alamat</label>
                                    <span><?php echo htmlspecialchars($user_data['alamat'] ?: '-'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="?form=edit" class="btn btn-primary btn-edit">
                            <i class="fas fa-edit"></i> 
                            <span>Edit Profil</span>
                        </a>
                        <a href="?form=password" class="btn btn-secondary btn-password">
                            <i class="fas fa-key"></i>
                            <span>Ubah Password</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Form Edit Profile -->
                <?php if ($active_form == 'edit'): ?>
                <div class="profile-card">
                    <div class="form-header">
                        <div class="form-title">
                            <i class="fas fa-edit"></i>
                            <h3>Edit Profil</h3>
                        </div>
                        <a href="?" class="btn btn-outline btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Profil
                        </a>
                    </div>
                    
                    <form action="../controller/profile-controller.php" method="POST" class="profile-form">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-grid">
                            <div class="form-section">
                                <h4 class="section-title">Informasi Akun</h4>
                                <div class="form-group">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user"></i>
                                        Username
                                    </label>
                                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i>
                                        Email
                                    </label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required class="form-input">
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4 class="section-title">Informasi Pribadi</h4>
                                <div class="form-group">
                                    <label for="nama_lengkap" class="form-label">
                                        <i class="fas fa-signature"></i>
                                        Nama Lengkap
                                    </label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="telepon" class="form-label">
                                        <i class="fas fa-phone"></i>
                                        Nomor Telepon
                                    </label>
                                    <input type="text" id="telepon" name="telepon" value="<?php echo htmlspecialchars($user_data['telepon']); ?>" class="form-input">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="alamat" class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Alamat
                            </label>
                            <textarea id="alamat" name="alamat" rows="3" class="form-textarea"><?php echo htmlspecialchars($user_data['alamat']); ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <a href="?" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Form Change Password -->
                <?php if ($active_form == 'password'): ?>
                <div class="profile-card">
                    <div class="form-header">
                        <div class="form-title">
                            <i class="fas fa-key"></i>
                            <h3>Ubah Password</h3>
                        </div>
                        <a href="?" class="btn btn-outline btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Profil
                        </a>
                    </div>
                    
                    <form action="../controller/profile-controller.php" method="POST" class="profile-form">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-single">
                            <div class="form-group">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Password Saat Ini
                                </label>
                                <input type="password" id="current_password" name="current_password" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-key"></i>
                                    Password Baru
                                </label>
                                <input type="password" id="new_password" name="new_password" required minlength="6" class="form-input">
                                <small class="form-help">
                                    <i class="fas fa-info-circle"></i>
                                    Minimal 6 karakter
                                </small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-check-double"></i>
                                    Konfirmasi Password Baru
                                </label>
                                <input type="password" id="confirm_password" name="confirm_password" required class="form-input">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="?" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-user.js"></script>
    <script>
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordForm = document.querySelector('form[action*="change_password"]');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Password baru dan konfirmasi password tidak cocok'
                        });
                    }
                });
            }

            const profileForm = document.querySelector('form[action*="update_profile"]');
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    const username = document.getElementById('username').value;
                    const email = document.getElementById('email').value;
                    
                    if (!username || !email) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Field username dan email harus diisi'
                        });
                    }
                });
            }
        });
    </script>

    <style>
        /* Header Styles */
        .content-header {
            background: #10B981;
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            font-size: 2rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.75rem;
            border-radius: 50%;
            backdrop-filter: blur(10px);
        }

        .title-content h1 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .title-content p {
            margin: 0.25rem 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .header-user {
            text-align: right;
        }

        .user-welcome {
            margin-bottom: 0.5rem;
        }

        .welcome-text {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .username {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            font-weight: 500;
        }

        .user-role-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .badge-icon {
            color: #c6f6d5;
        }

        .mobile-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            backdrop-filter: blur(10px);
        }

        /* Profile Card Styles */
        .profile-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        .profile-header-card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .profile-identity {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .profile-avatar.user-avatar {
            position: relative;
            width: 100px;
            height: 100px;
            background: #10B981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }

        .profile-info h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
            color: #2d3748;
        }

        .profile-email {
            margin: 0 0 0.75rem 0;
            color: #718096;
            font-size: 1rem;
        }

        .profile-role {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .user-role {
            background: #bee3f8;
            color: #2c5282;
        }

        .profile-stats {
            display: flex;
            gap: 2rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #10B981;
            font-size: 1.1rem;
        }

        .stat-label {
            display: block;
            font-size: 0.8rem;
            color: #718096;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            display: block;
            font-weight: 600;
            color: #2d3748;
        }

        /* Profile Details Grid */
        .profile-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .detail-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0 0 1.5rem 0;
            color: #2d3748;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .section-title i {
            color: #10B981;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #10B981;
            font-size: 1rem;
        }

        .detail-content label {
            display: block;
            font-size: 0.8rem;
            color: #718096;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .detail-content span {
            display: block;
            font-weight: 600;
            color: #2d3748;
        }

        /* Profile Actions */
        .profile-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            padding-top: 2rem;
            border-top: 2px solid #f8f9fa;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: #10B981;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(66, 153, 225, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #2d3748;
            border: 2px solid #e9ecef;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: #10B981;
            border: 2px solid #10B981;
        }

        .btn-outline:hover {
            background: #10B981;
            color: white;
        }

        /* Form Styles */
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .form-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #2d3748;
        }

        .form-title i {
            font-size: 1.5rem;
            color: #10B981;
        }

        .form-title h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .form-single {
            max-width: 500px;
            margin: 0 auto 1.5rem;
        }

        .form-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
        }

        .form-section .section-title {
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #2d3748;
        }

        .form-label i {
            color: #10B981;
            width: 16px;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
            transform: translateY(-1px);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-help {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            color: #718096;
            font-size: 0.85rem;
        }

        .form-help i {
            color: #10B981;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1.5rem;
            border-top: 2px solid #f8f9fa;
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: #f0f9ff;
            border-color: #10b981;
            color: #065f46;
        }

        .alert-error {
            background: #fef2f2;
            border-color: #ef4444;
            color: #7f1d1d;
        }

        .alert i {
            font-size: 1.2rem;
            margin-top: 0.1rem;
        }

        .alert ul {
            margin: 0.5rem 0 0 0;
            padding-left: 1.5rem;
        }

        .alert li {
            margin-bottom: 0.25rem;
        }

        /* Section Header */
        .section-header {
            margin-bottom: 2rem;
        }

        .section-header h2 {
            margin: 0 0 0.5rem 0;
            color: #2d3748;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .section-header p {
            margin: 0;
            color: #718096;
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-main {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .header-user {
                text-align: center;
            }
            
            .profile-header-card {
                flex-direction: column;
                gap: 1.5rem;
            }
            
            .profile-stats {
                justify-content: center;
            }
            
            .profile-details-grid {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-actions {
                flex-direction: column;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .content-header {
                padding: 1rem;
            }
            
            .profile-card {
                padding: 1.5rem;
            }
        }
    </style>
</body>
</html>