<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil data statistik dari database
$total_proposals = 0;
$pending_proposals = 0;
$total_admins = 0;
$total_news = 0;
$recent_activities = [];

try {
    // Total proposals
    $query1 = "SELECT COUNT(*) as total FROM proposals";
    $result1 = mysqli_query($koneksi, $query1);
    $total_proposals = mysqli_fetch_assoc($result1)['total'];

    // Pending proposals
    $query2 = "SELECT COUNT(*) as total FROM proposals WHERE status = 'pending'";
    $result2 = mysqli_query($koneksi, $query2);
    $pending_proposals = mysqli_fetch_assoc($result2)['total'];

    // Total admins
    $query3 = "SELECT COUNT(*) as total FROM users WHERE role = 'admin'";
    $result3 = mysqli_query($koneksi, $query3);
    $total_admins = mysqli_fetch_assoc($result3)['total'];

    // Total news
    $query4 = "SELECT COUNT(*) as total FROM berita WHERE status_berita = 'publish'";
    $result4 = mysqli_query($koneksi, $query4);
    $total_news = mysqli_fetch_assoc($result4)['total'];

    // Recent activities
    $query5 = "SELECT al.*, u.username 
               FROM activity_logs al 
               JOIN users u ON al.user_id = u.id 
               ORDER BY al.created_at DESC 
               LIMIT 5";
    $result5 = mysqli_query($koneksi, $query5);
    while ($row = mysqli_fetch_assoc($result5)) {
        $recent_activities[] = $row;
    }

} catch (Exception $e) {
    error_log("Error fetching dashboard data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-admin.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .loading-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }
        .loading-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
        .refresh-btn {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .refresh-btn:hover {
            background: #f3f4f6;
            color: #374151;
        }
    </style>
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
                        <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Welcome Card dengan Statistik Dinamis -->
                <div class="welcome-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h2>Selamat Datang di Dashboard Admin</h2>
                            <p>Anda login sebagai <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> dengan hak akses Administrator</p>
                        </div>
                        <button class="refresh-btn" onclick="refreshDashboard()" title="Refresh Data">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="welcome-stats" id="statsContainer">
                        <div class="stat-card">
                            <i class="fas fa-file-alt"></i>
                            <div class="stat-info">
                                <h3 id="totalProposals"><?php echo $total_proposals; ?></h3>
                                <p>Total Proposal</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-clock"></i>
                            <div class="stat-info">
                                <h3 id="pendingProposals"><?php echo $pending_proposals; ?></h3>
                                <p>Menunggu Review</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-users"></i>
                            <div class="stat-info">
                                <h3 id="totalAdmins"><?php echo $total_admins; ?></h3>
                                <p>Total Admin</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-newspaper"></i>
                            <div class="stat-info">
                                <h3 id="totalNews"><?php echo $total_news; ?></h3>
                                <p>Berita Diposting</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                

                <!-- Recent Activities Dinamis -->
                <div class="recent-activity">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3>Aktivitas Terbaru</h3>
                        <button class="refresh-btn" onclick="loadRecentActivities()" title="Refresh Aktivitas">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="activity-list" id="activitiesContainer">
                        <?php if (!empty($recent_activities)): ?>
                            <?php foreach ($recent_activities as $activity): ?>
                                <div class="activity-item">
                                    <i class="fas fa-<?php echo getActivityIcon($activity['action']); ?>"></i>
                                    <div class="activity-content">
                                        <p><?php echo htmlspecialchars(formatActivityDescription($activity)); ?></p>
                                        <span><?php echo date('d F Y, H:i', strtotime($activity['created_at'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>Tidak Ada Aktivitas</h3>
                                <p>Belum ada aktivitas yang tercatat</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-admin.js"></script>
    <script>
        // Fungsi untuk refresh dashboard
        function refreshDashboard() {
            loadStats();
            loadRecentActivities();
        }

        // Fungsi untuk memuat statistik
        function loadStats() {
            const statsContainer = document.getElementById('statsContainer');
            const originalContent = statsContainer.innerHTML;
            
            statsContainer.innerHTML = `
                <div class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Memuat statistik...</p>
                </div>
            `;

            fetch('../controller/admin-dashboard-controller.php?action=get_stats')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateStats(data.data);
                        showAlert('Data statistik berhasil diperbarui', 'success');
                    } else {
                        throw new Error(data.error || 'Failed to load stats');
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                    statsContainer.innerHTML = originalContent;
                    showAlert('Gagal memuat statistik: ' + error.message, 'error');
                });
        }

        // Fungsi untuk update statistik
        function updateStats(stats) {
            document.getElementById('totalProposals').textContent = stats.total_proposals;
            document.getElementById('pendingProposals').textContent = stats.pending_proposals;
            document.getElementById('totalAdmins').textContent = stats.total_admins;
            document.getElementById('totalNews').textContent = stats.total_news;
        }

        // Fungsi untuk memuat aktivitas terbaru
        function loadRecentActivities() {
            const activitiesContainer = document.getElementById('activitiesContainer');
            const originalContent = activitiesContainer.innerHTML;
            
            activitiesContainer.innerHTML = `
                <div class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Memuat aktivitas...</p>
                </div>
            `;

            fetch('../controller/admin-dashboard-controller.php?action=get_activities')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        renderActivities(data.data);
                        showAlert('Aktivitas berhasil diperbarui', 'success');
                    } else {
                        throw new Error(data.error || 'Failed to load activities');
                    }
                })
                .catch(error => {
                    console.error('Error loading activities:', error);
                    activitiesContainer.innerHTML = originalContent;
                    showAlert('Gagal memuat aktivitas: ' + error.message, 'error');
                });
        }

        // Fungsi untuk render aktivitas
        function renderActivities(activities) {
            const container = document.getElementById('activitiesContainer');
            
            if (activities.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Tidak Ada Aktivitas</h3>
                        <p>Belum ada aktivitas yang tercatat</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = activities.map(activity => `
                <div class="activity-item">
                    <i class="fas fa-${getActivityIcon(activity.action)}"></i>
                    <div class="activity-content">
                        <p>${activity.description}</p>
                        <span>${formatDate(activity.created_at)}</span>
                    </div>
                </div>
            `).join('');
        }

        // Helper functions
        function getActivityIcon(action) {
            const iconMap = {
                'submit_proposal': 'file-alt',
                'edit_proposal': 'edit',
                'delete_proposal': 'trash',
                'update_proposal_status': 'sync-alt',
                'login': 'sign-in-alt',
                'logout': 'sign-out-alt',
                'create_news': 'newspaper',
                'edit_news': 'edit',
                'delete_news': 'trash',
                'add_admin': 'user-plus',
                'edit_admin': 'user-edit',
                'delete_admin': 'user-minus'
            };
            return iconMap[action] || 'circle';
        }

        function formatDate(dateString) {
            const options = { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        // Auto refresh setiap 5 menit
        setInterval(() => {
            loadStats();
        }, 300000); // 5 menit

        console.log('Dashboard admin loaded successfully');
    </script>
</body>

</html>

<?php
// Helper functions untuk PHP
function getActivityIcon($action) {
    $iconMap = [
        'submit_proposal' => 'file-alt',
        'edit_proposal' => 'edit',
        'delete_proposal' => 'trash',
        'update_proposal_status' => 'sync-alt',
        'login' => 'sign-in-alt',
        'logout' => 'sign-out-alt',
        'create_news' => 'newspaper',
        'edit_news' => 'edit',
        'delete_news' => 'trash',
        'add_admin' => 'user-plus',
        'edit_admin' => 'user-edit',
        'delete_admin' => 'user-minus'
    ];
    return $iconMap[$action] ?? 'circle';
}

function formatActivityDescription($activity) {
    $actionMap = [
        'submit_proposal' => 'Mengajukan proposal',
        'edit_proposal' => 'Mengedit proposal',
        'delete_proposal' => 'Menghapus proposal',
        'update_proposal_status' => 'Memperbarui status proposal',
        'login' => 'Login ke sistem',
        'logout' => 'Logout dari sistem',
        'create_news' => 'Membuat berita',
        'edit_news' => 'Mengedit berita',
        'delete_news' => 'Menghapus berita',
        'add_admin' => 'Menambahkan admin',
        'edit_admin' => 'Mengedit admin',
        'delete_admin' => 'Menghapus admin'
    ];
    
    $baseDescription = $actionMap[$activity['action']] ?? $activity['action'];
    
    if (!empty($activity['description'])) {
        return $baseDescription . ' - ' . $activity['description'];
    }
    
    return $baseDescription . ' oleh ' . $activity['username'];
}
?>