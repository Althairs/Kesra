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
    <title>Lihat Pengajuan Proposal - Bagian Kesra</title>
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
                    <h1>Pengajuan Proposal</h1>
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
                <div class="section-header">
                    <h2>Semua Pengajuan Proposal</h2>
                    <p>Kelola dan review semua pengajuan proposal bantuan masjid</p>
                </div>

                <!-- Filter Status -->
                <div class="status-filter">
                    <button class="filter-btn active" data-filter="all">Semua</button>
                    <button class="filter-btn" data-filter="pending">Menunggu</button>
                    <button class="filter-btn" data-filter="review">Dalam Review</button>
                    <button class="filter-btn" data-filter="approved">Disetujui</button>
                    <button class="filter-btn" data-filter="rejected">Ditolak</button>
                </div>

                <!-- Tabel Proposal -->
                <div class="data-table">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Masjid</th>
                                    <th>Pengaju</th>
                                    <th>Jenis Bantuan</th>
                                    <th>Jumlah Diajukan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh JavaScript -->
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <i class="fas fa-file-alt"></i>
                                        <h3>Memuat Data...</h3>
                                        <p>Sedang memuat daftar pengajuan proposal</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Statistik Cepat -->
                <div class="welcome-stats" style="margin-top: 2rem;">
                    <div class="stat-card">
                        <i class="fas fa-file-alt"></i>
                        <div class="stat-info">
                            <h3 id="totalProposals">0</h3>
                            <p>Total Proposal</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clock"></i>
                        <div class="stat-info">
                            <h3 id="pendingProposals">0</h3>
                            <p>Menunggu Review</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-check-circle"></i>
                        <div class="stat-info">
                            <h3 id="approvedProposals">0</h3>
                            <p>Disetujui</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-times-circle"></i>
                        <div class="stat-info">
                            <h3 id="rejectedProposals">0</h3>
                            <p>Ditolak</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load proposal data
            filterProposals('all');
            updateStats();
        });

        function updateStats() {
            const proposals = JSON.parse(localStorage.getItem('adminProposals') || '[]');
            document.getElementById('totalProposals').textContent = proposals.length;
            document.getElementById('pendingProposals').textContent = proposals.filter(p => p.status === 'pending').length;
            document.getElementById('approvedProposals').textContent = proposals.filter(p => p.status === 'approved').length;
            document.getElementById('rejectedProposals').textContent = proposals.filter(p => p.status === 'rejected').length;
        }
    </script>
</body>
</html>