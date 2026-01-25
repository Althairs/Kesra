<!-- File: komponen/sidebar.php -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../img/kesra.png" alt="Kesra Logo">
            <h2>Bagian Kesra</h2>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item">
                <a href="dashboard.php"
                    class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="ajukan-proposal.php"
                    class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'ajukan-proposal.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Ajukan Proposal</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="status-proposal.php"
                    class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'status-proposal.php' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i>
                    <span>Status Proposal</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="profile.php"
                    class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    <span>Profil Saya</span>
                </a>
            </li>
            <!-- Tambahkan link notifikasi sebelum logout -->
            <li class="nav-item">
                <a href="notifications.php"
                    class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">
                    <i class="fas fa-bell"></i>
                    <span>Notifikasi</span>
                    <?php
                    require_once __DIR__ . '/../../koneksi.php';
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                        $stmt = $koneksi->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $data = $result->fetch_assoc();
                        $unread_count = $data['unread_count'] ?? 0;
                        $stmt->close();

                        if ($unread_count > 0): ?>
                            <span class="notification-badge"><?php echo $unread_count > 99 ? '99+' : $unread_count; ?></span>
                        <?php endif;
                    } ?>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="../controller/login-controller.php?logout=true" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

<!-- Mobile Overlay -->
<div class="mobile-overlay" id="mobileOverlay"></div>