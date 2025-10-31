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
    <title>Ajukan Proposal - Bagian Kesra</title>
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
                    <h1>Ajukan Proposal</h1>
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
                    <h2>Ajukan Proposal Bantuan</h2>
                    <p>Isi form berikut untuk mengajukan proposal bantuan hibah masjid</p>
                </div>
                
                <form class="proposal-form" action="controller/proposal-controller.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama-masjid">Nama Masjid *</label>
                        <input type="text" id="nama-masjid" name="nama_masjid" required placeholder="Masukkan nama masjid">
                    </div>
                    
                    <div class="form-group">
                        <label for="alamat-masjid">Alamat Masjid *</label>
                        <textarea id="alamat-masjid" name="alamat_masjid" required placeholder="Masukkan alamat lengkap masjid" rows="3"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="jenis-bantuan">Jenis Bantuan *</label>
                            <select id="jenis-bantuan" name="jenis_bantuan" required>
                                <option value="">Pilih Jenis Bantuan</option>
                                <option value="renovasi">Renovasi Bangunan</option>
                                <option value="sarana">Sarana Ibadah</option>
                                <option value="pendidikan">Pendidikan Agama</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="jumlah-diajukan">Jumlah yang Diajukan (Rp) *</label>
                            <input type="number" id="jumlah-diajukan" name="jumlah_diajukan" required placeholder="Contoh: 5000000" min="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Proposal *</label>
                        <textarea id="deskripsi" name="deskripsi" required placeholder="Jelaskan detail proposal bantuan yang diajukan" rows="5"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="dokumen">Upload Dokumen Pendukung</label>
                        <input type="file" id="dokumen" name="dokumen" accept=".pdf,.doc,.docx,.jpg,.png">
                        <small>Format: PDF, DOC, JPG, PNG (Maks. 5MB)</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="reset" class="btn btn-secondary">Batal</button>
                        <button type="submit" name="ajukan_proposal" class="btn btn-primary">Ajukan Proposal</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-user.js"></script>
</body>
</html>