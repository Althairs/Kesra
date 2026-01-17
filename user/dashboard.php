<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Ambil statistik proposal user
$user_id = $_SESSION['user_id'];
$stats = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

try {
    // Total proposals
    $query = "SELECT COUNT(*) as total FROM proposals WHERE id_user = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $stats['total'] = $row['total'];
    mysqli_stmt_close($stmt);

    // Status counts
    $statuses = ['pending', 'approved', 'rejected'];
    foreach ($statuses as $status) {
        $query = "SELECT COUNT(*) as count FROM proposals WHERE id_user = ? AND status = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $status);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $stats[$status] = $row['count'];
        mysqli_stmt_close($stmt);
    }

} catch (Exception $e) {
    error_log("Error fetching dashboard stats: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-user.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'komponen/sidebar.php'; ?>

        <main class="main-content">
            <header class="content-header">
                <div class="header-left">
                    <button class="mobile-toggle" id="mobileToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Dashboard</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        <div class="user-role">User</div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <?php include 'komponen/alert-messages.php'; ?>

                <div class="welcome-card">
                    <h2>Selamat Datang di Dashboard User</h2>
                    <p>Anda login sebagai <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                    <div class="welcome-stats">
                        <div class="stat-card">
                            <i class="fas fa-file-alt"></i>
                            <div class="stat-info">
                                <h3><?php echo $stats['total']; ?></h3>
                                <p>Total Proposal</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-clock"></i>
                            <div class="stat-info">
                                <h3><?php echo $stats['pending']; ?></h3>
                                <p>Menunggu Review</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-check-circle"></i>
                            <div class="stat-info">
                                <h3><?php echo $stats['approved']; ?></h3>
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
                        <?php
                        try {
                            $query = "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
                            $stmt = mysqli_prepare($koneksi, $query);
                            mysqli_stmt_bind_param($stmt, "i", $user_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            
                            if (mysqli_num_rows($result) > 0) {
                                while ($activity = mysqli_fetch_assoc($result)) {
                                    $icon = [
                                        'submit_proposal' => 'fas fa-paper-plane',
                                        'edit_proposal' => 'fas fa-edit',
                                        'delete_proposal' => 'fas fa-trash',
                                        'login' => 'fas fa-sign-in-alt',
                                        'logout' => 'fas fa-sign-out-alt'
                                    ][$activity['action']] ?? 'fas fa-info-circle';
                                    
                                    $description = $activity['description'] ?: [
                                        'submit_proposal' => 'Mengajukan proposal baru',
                                        'edit_proposal' => 'Mengedit proposal',
                                        'delete_proposal' => 'Menghapus proposal'
                                    ][$activity['action']] ?? 'Aktivitas sistem';
                                    ?>
                                    <div class="activity-item">
                                        <i class="<?php echo $icon; ?>"></i>
                                        <div class="activity-content">
                                            <p><?php echo htmlspecialchars($description); ?></p>
                                            <span><?php echo date('d F Y H:i', strtotime($activity['created_at'])); ?></span>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="empty-state">
                                    <i class="fas fa-info-circle"></i>
                                    <p>Belum ada aktivitas terbaru</p>
                                </div>';
                            }
                            mysqli_stmt_close($stmt);
                        } catch (Exception $e) {
                            error_log("Error fetching activities: " . $e->getMessage());
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-user.js"></script>
</body>
</html>