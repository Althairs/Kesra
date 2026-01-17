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
                <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="ajukan-proposal.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'ajukan-proposal.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Ajukan Proposal</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="status-proposal.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'status-proposal.php' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i>
                    <span>Status Proposal</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    <span>Profil Saya</span>
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