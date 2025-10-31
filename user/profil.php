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
    <title>Profil Saya - Bagian Kesra</title>
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
                    <h1>Profil Saya</h1>
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
                    <h2>Profil Saya</h2>
                    <p>Kelola informasi akun Anda</p>
                </div>
                
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="profile-info">
                            <h3><?php echo $_SESSION['username']; ?></h3>
                            <p><?php echo $_SESSION['email']; ?></p>
                            <span class="badge badge-user">User</span>
                        </div>
                    </div>
                    
                    <div class="profile-details">
                        <div class="detail-item">
                            <label>Username</label>
                            <span><?php echo $_SESSION['username']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <span><?php echo $_SESSION['email']; ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Role</label>
                            <span>User</span>
                        </div>
                        <div class="detail-item">
                            <label>Bergabung Sejak</label>
                            <span>-</span>
                        </div>
                    </div>
                    
                    <div class="profile-actions">
                        <button class="btn btn-outline" onclick="editProfile()">Edit Profil</button>
                        <button class="btn btn-outline" onclick="changePassword()">Ubah Password</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-user.js"></script>
    <script>
        function editProfile() {
            alert('Fitur edit profil akan segera tersedia!');
        }
        
        function changePassword() {
            alert('Fitur ubah password akan segera tersedia!');
        }
    </script>
</body>
</html>