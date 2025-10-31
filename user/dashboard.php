<?php
session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-user.css">
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
                    <h1>Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span>Halo, <strong><?php echo $_SESSION['username']; ?></strong></span>
                        <div class="user-role">User</div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <div class="welcome-card">
                    <h2>Selamat Datang di Dashboard User</h2>
                    <p>Anda login sebagai <strong><?php echo $_SESSION['username']; ?></strong></p>
                    <div class="welcome-stats">
                        <div class="stat-card">
                            <i class="fas fa-file-alt"></i>
                            <div class="stat-info">
                                <h3>0</h3>
                                <p>Proposal Diajukan</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-clock"></i>
                            <div class="stat-info">
                                <h3>0</h3>
                                <p>Menunggu Review</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-check-circle"></i>
                            <div class="stat-info">
                                <h3>0</h3>
                                <p>Proposal Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="quick-actions">
                    <h3>Aksi Cepat</h3>
                    <div class="action-grid">
                        <a href="ajukan-proposal.php" class="action-card">
                            <i class="fas fa-plus-circle"></i>
                            <span>Ajukan Proposal Baru</span>
                        </a>
                        <a href="status-proposal.php" class="action-card">
                            <i class="fas fa-history"></i>
                            <span>Lihat Status Proposal</span>
                        </a>
                        <a href="profil.php" class="action-card">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profil</span>
                        </a>
                    </div>
                </div> -->

                <div class="recent-activity">
                    <h3>Aktivitas Terbaru</h3>
                    <div class="activity-list">
                        <div class="activity-item">
                            <i class="fas fa-info-circle"></i>
                            <div class="activity-content">
                                <p>Belum ada aktivitas terbaru</p>
                                <span>-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-user.js"></script>
</body>
</html>