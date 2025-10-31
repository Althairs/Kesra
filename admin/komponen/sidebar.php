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
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../controller/login-controller.php?logout=true" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>