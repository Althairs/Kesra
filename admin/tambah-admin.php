<?php
session_start();
// Cek apakah user sudah login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Tampilkan pesan sukses/error
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Ambil data admin untuk ditampilkan
require_once '../koneksi.php';
$query = "SELECT * FROM users WHERE role = 'admin' ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-admin.css">
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
                    <h1>Manajemen Admin</h1>
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
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-error">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Form Tambah/Edit Admin -->
                <div class="section-header">
                    <h2 id="form-title">Tambah Admin Baru</h2>
                    <p id="form-subtitle">Tambahkan administrator baru untuk sistem Bagian Kesra</p>
                </div>

                <form class="admin-form" action="../controller/admin-controller.php" method="POST" id="adminForm">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="admin_id" id="adminId" value="">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" required placeholder="Masukkan username">
                            <small>Username harus unik dan tidak mengandung spasi</small>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required placeholder="Masukkan email">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password <span id="password-required">*</span></label>
                            <input type="password" id="password" name="password" placeholder="Masukkan password">
                            <small id="password-help">Minimal 8 karakter</small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password <span id="confirm-password-required">*</span></label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" id="cancelBtn" style="display: none;">
                            <i class="fas fa-times"></i> Batal Edit
                        </button>
                        <button type="button" class="btn btn-outline" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-user-plus"></i> Tambah Admin
                        </button>
                    </div>
                </form>

                <!-- Daftar Admin Existing -->
                <div class="recent-activity" style="margin-top: 3rem;">
                    <h3>Daftar Administrator</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <!-- <td><?php echo $row['id']; ?></td> -->
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><span class="badge badge-admin"><?php echo ucfirst($row['role']); ?></span></td>
                                            <td><?php echo date('d F Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-edit" onclick="editAdmin(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>', '<?php echo htmlspecialchars($row['email']); ?>')">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button class="btn btn-delete" onclick="deleteAdmin(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')" <?php echo $row['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="empty-state">
                                            <i class="fas fa-users"></i>
                                            <h3>Belum Ada Admin</h3>
                                            <p>Belum ada admin yang terdaftar</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Konfirmasi Hapus</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus admin <strong id="deleteAdminName"></strong>?</p>
                <p class="text-warning">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>

    <script src="../js/dashboard-admin.js"></script>
    <script>
        let adminToDelete = null;

        // Fungsi untuk edit admin
        function editAdmin(id, username, email) {
            document.getElementById('form-title').textContent = 'Edit Admin';
            document.getElementById('form-subtitle').textContent = 'Edit data administrator';
            document.getElementById('formAction').value = 'update';
            document.getElementById('adminId').value = id;
            document.getElementById('username').value = username;
            document.getElementById('email').value = email;
            
            // Ubah required password menjadi opsional saat edit
            document.getElementById('password').removeAttribute('required');
            document.getElementById('confirm_password').removeAttribute('required');
            document.getElementById('password-required').textContent = '';
            document.getElementById('confirm-password-required').textContent = '';
            document.getElementById('password-help').textContent = 'Kosongkan jika tidak ingin mengubah password';
            
            // Ubah tombol submit
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save"></i> Update Admin';
            document.getElementById('cancelBtn').style.display = 'inline-block';
            
            // Scroll ke form
            document.getElementById('adminForm').scrollIntoView({ behavior: 'smooth' });
        }

        // Fungsi untuk batal edit
        document.getElementById('cancelBtn').onclick = function() {
            resetForm();
        };

        // Fungsi reset form
        function resetForm() {
            document.getElementById('form-title').textContent = 'Tambah Admin Baru';
            document.getElementById('form-subtitle').textContent = 'Tambahkan administrator baru untuk sistem Bagian Kesra';
            document.getElementById('formAction').value = 'create';
            document.getElementById('adminId').value = '';
            document.getElementById('adminForm').reset();
            
            // Set required password kembali
            document.getElementById('password').setAttribute('required', 'required');
            document.getElementById('confirm_password').setAttribute('required', 'required');
            document.getElementById('password-required').textContent = '*';
            document.getElementById('confirm-password-required').textContent = '*';
            document.getElementById('password-help').textContent = 'Minimal 8 karakter';
            
            // Ubah tombol submit
            document.getElementById('submitBtn').innerHTML = '<i class="fas fa-user-plus"></i> Tambah Admin';
            document.getElementById('cancelBtn').style.display = 'none';
        }

        // Fungsi untuk hapus admin
        function deleteAdmin(id, username) {
            adminToDelete = id;
            document.getElementById('deleteAdminName').textContent = username;
            document.getElementById('deleteModal').style.display = 'block';
        }

        // Fungsi konfirmasi hapus
        document.getElementById('confirmDeleteBtn').onclick = function() {
            if (adminToDelete) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../controller/admin-controller.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = adminToDelete;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        };

        // Fungsi tutup modal hapus
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            adminToDelete = null;
        }

        // Tutup modal ketika klik di luar
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        };

        // Validasi form sebelum submit
        document.getElementById('adminForm').onsubmit = function(e) {
            const action = document.getElementById('formAction').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (action === 'create') {
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password minimal 8 karakter');
                    return false;
                }
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Konfirmasi password tidak sesuai');
                return false;
            }
            
            return true;
        };
    </script>
</body>
</html>