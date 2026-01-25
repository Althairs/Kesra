<?php
// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../img/kesra.png" alt="Logo Kesra" onerror="this.style.display='none'">
            <h2>Kesra Admin</h2>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'tambah-admin.php' ? 'active' : ''; ?>">
                <a href="tambah-admin.php" class="nav-link">
                    <i class="fas fa-user-plus"></i>
                    <span>Tambah Admin</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'lihat-pengajuan.php' ? 'active' : ''; ?>">
                <a href="lihat-pengajuan.php" class="nav-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Lihat Pengajuan Proposal</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'buat-berita.php' ? 'active' : ''; ?>">
                <a href="buat-berita.php" class="nav-link">
                    <i class="fas fa-newspaper"></i>
                    <span>Buat Berita</span>
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'profile-admin.php' ? 'active' : ''; ?>">
                <a href="profile-admin.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <!-- Tambahkan link notifikasi sebelum logout -->
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">
                <a href="notifications.php" class="nav-link">
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
            <span>Logout</span>
        </a>
    </div>
</aside>