<?php
session_start();
// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Data admin (dalam aplikasi nyata, ini akan diambil dari database)
$admin_data = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'email' => $_SESSION['email'] ?? 'admin@kesra.go.id',
    'role' => $_SESSION['role'],
    'nama_lengkap' => 'Administrator Sistem',
    'telepon' => '+62 812-3456-7890',
    'alamat' => 'Gedung Kantor Bagian Kesra',
    'tanggal_bergabung' => '2024-01-01',
    'last_login' => date('Y-m-d H:i:s')
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Admin - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-admin.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'komponen/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="content-header">
                <div class="header-left">
                    <button class="mobile-toggle" id="mobileToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Profile Admin</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span>Halo, <strong><?php echo $_SESSION['username']; ?></strong></span>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="profile-info">
                            <h3><?php echo $admin_data['nama_lengkap']; ?></h3>
                            <p><?php echo $admin_data['email']; ?></p>
                            <span class="badge badge-admin"><?php echo ucfirst($admin_data['role']); ?></span>
                        </div>
                    </div>

                    <div class="profile-details">
                        <div class="detail-item">
                            <label>Username</label>
                            <span><?php echo $admin_data['username']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <span><?php echo $admin_data['email']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Nomor Telepon</label>
                            <span><?php echo $admin_data['telepon']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Alamat</label>
                            <span><?php echo $admin_data['alamat']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Tanggal Bergabung</label>
                            <span><?php echo date('d F Y', strtotime($admin_data['tanggal_bergabung'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Login Terakhir</label>
                            <span><?php echo date('d F Y H:i', strtotime($admin_data['last_login'])); ?></span>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button class="btn btn-primary" onclick="editProfile()">
                            <i class="fas fa-edit"></i> Edit Profil
                        </button>
                        <button class="btn btn-outline" onclick="changePassword()">
                            <i class="fas fa-key"></i> Ubah Password
                        </button>
                    </div>
                </div>

                <!-- Statistik Admin -->
                <div class="welcome-stats" style="margin-top: 2rem;">
                    <div class="stat-card">
                        <i class="fas fa-file-alt"></i>
                        <div class="stat-info">
                            <h3>24</h3>
                            <p>Proposal Dikelola</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-newspaper"></i>
                        <div class="stat-info">
                            <h3>12</h3>
                            <p>Berita Diposting</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <div class="stat-info">
                            <h3>5</h3>
                            <p>Admin Dikelola</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-calendar-check"></i>
                        <div class="stat-info">
                            <h3>156</h3>
                            <p>Hari Aktif</p>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas Terbaru -->
                <div class="recent-activity" style="margin-top: 2rem;">
                    <h3>Aktivitas Terbaru</h3>
                    <div class="activity-list">
                        <div class="activity-item">
                            <i class="fas fa-file-alt"></i>
                            <div class="activity-content">
                                <p>Mereview proposal dari Masjid Al-Ikhlas</p>
                                <span>Hari ini, 10:30</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-newspaper"></i>
                            <div class="activity-content">
                                <p>Memosting berita "Program Bantuan 2024"</p>
                                <span>Kemarin, 15:45</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-user-plus"></i>
                            <div class="activity-content">
                                <p>Menambahkan admin baru "budi"</p>
                                <span>2 hari yang lalu, 09:15</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-check-circle"></i>
                            <div class="activity-content">
                                <p>Menyetujui proposal Masjid Nurul Huda</p>
                                <span>3 hari yang lalu, 14:20</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-admin.js"></script>
</body>
</html>