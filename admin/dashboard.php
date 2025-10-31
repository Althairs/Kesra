<?php
session_start();
// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-admin.css">
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
                    <h1>Dashboard Admin</h1>
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
                <div class="welcome-card">
                    <h2>Selamat Datang di Dashboard Admin</h2>
                    <p>Anda login sebagai <strong><?php echo $_SESSION['username']; ?></strong> dengan hak akses
                        Administrator</p>
                    <div class="welcome-stats">
                        <div class="stat-card">
                            <i class="fas fa-file-alt"></i>
                            <div class="stat-info">
                                <h3>12</h3>
                                <p>Total Proposal</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-clock"></i>
                            <div class="stat-info">
                                <h3>5</h3>
                                <p>Menunggu Review</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-users"></i>
                            <div class="stat-info">
                                <h3>3</h3>
                                <p>Total Admin</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-newspaper"></i>
                            <div class="stat-info">
                                <h3>8</h3>
                                <p>Berita Diposting</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="quick-actions">
                    <h3>Aksi Cepat</h3>
                    <div class="action-grid">
                        <a href="tambah-admin.php" class="action-card">
                            <i class="fas fa-user-plus"></i>
                            <span>Tambah Admin</span>
                        </a>
                        <a href="lihat-pengajuan-proposal.php" class="action-card">
                            <i class="fas fa-file-alt"></i>
                            <span>Lihat Proposal</span>
                        </a>
                        <a href="buat-berita.php" class="action-card">
                            <i class="fas fa-newspaper"></i>
                            <span>Buat Berita</span>
                        </a>
                        <a href="profile-admin.php" class="action-card">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profil</span>
                        </a>
                    </div>
                </div> -->

                <div class="recent-activity">
                    <h3>Aktivitas Terbaru</h3>
                    <div class="activity-list">
                        <div class="activity-item">
                            <i class="fas fa-user-plus"></i>
                            <div class="activity-content">
                                <p>Admin baru "budi" ditambahkan</p>
                                <span>30 Oktober 2024, 10:30</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-file-alt"></i>
                            <div class="activity-content">
                                <p>Proposal dari Masjid Al-Ikhlas disetujui</p>
                                <span>29 Oktober 2024, 15:45</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <i class="fas fa-newspaper"></i>
                            <div class="activity-content">
                                <p>Berita "Program Bantuan 2024" diposting</p>
                                <span>28 Oktober 2024, 09:15</span>
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