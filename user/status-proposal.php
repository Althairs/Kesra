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
    <title>Status Proposal - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-user.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
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
                    <h1>Status Proposal</h1>
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
                <div class="section-header">
                    <h2>Status Proposal Anda</h2>
                    <p>Lacak status pengajuan proposal bantuan hibah masjid</p>
                </div>
                
                <div class="status-filter">
                    <button class="filter-btn active" data-filter="all">Semua</button>
                    <button class="filter-btn" data-filter="pending">Menunggu</button>
                    <button class="filter-btn" data-filter="review">Dalam Review</button>
                    <button class="filter-btn" data-filter="approved">Disetujui</button>
                    <button class="filter-btn" data-filter="rejected">Ditolak</button>
                </div>
                
                <div class="proposal-list">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Belum Ada Proposal</h3>
                        <p>Anda belum mengajukan proposal apapun. <a href="ajukan-proposal.php">Ajukan proposal pertama Anda</a></p>
                    </div>
                    
                    <!-- Contoh proposal (akan di-generate dynamically) -->
                    <!--
                    <div class="proposal-item">
                        <div class="proposal-header">
                            <h3>Renovasi Masjid Al-Ikhlas</h3>
                            <span class="status-badge status-pending">Menunggu</span>
                        </div>
                        <div class="proposal-details">
                            <p><strong>Jenis:</strong> Renovasi Bangunan</p>
                            <p><strong>Diajukan:</strong> Rp 10.000.000</p>
                            <p><strong>Tanggal:</strong> 15 Des 2024</p>
                        </div>
                        <div class="proposal-actions">
                            <button class="btn btn-outline btn-sm">Lihat Detail</button>
                        </div>
                    </div>
                    -->
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-user.js"></script>
</body>
</html>