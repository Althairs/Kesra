<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Ambil data proposal yang akan diedit
$proposal = null;
if (isset($_GET['id'])) {
    $id_proposal = $_GET['id'];
    $id_user = $_SESSION['user_id'];
    
    $query = "SELECT * FROM proposals WHERE id = ? AND id_user = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ii", $id_proposal, $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $proposal = mysqli_fetch_assoc($result);
        
        // Cek apakah proposal bisa diedit (hanya status pending)
        if ($proposal['status'] !== 'pending') {
            $_SESSION['error_messages'] = ['Hanya proposal dengan status Menunggu yang dapat diedit'];
            header('Location: status-proposal.php');
            exit();
        }
    } else {
        $_SESSION['error_messages'] = ['Proposal tidak ditemukan!'];
        header('Location: status-proposal.php');
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    header('Location: status-proposal.php');
    exit();
}

// Ambil data form dari session jika ada (untuk prefill setelah error)
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// Jika ada form data dari session, gunakan itu. Jika tidak, gunakan data dari database
if (empty($form_data)) {
    $form_data = [
        'nama_lembaga' => $proposal['nama_lembaga'],
        'alamat' => $proposal['alamat'],
        'jenis_bantuan' => $proposal['jenis_bantuan'],
        'jumlah_diajukan' => $proposal['jumlah_diajukan'],
        'deskripsi' => $proposal['deskripsi'],
        'bank' => $proposal['bank'],
        'nomor_rekening' => $proposal['nomor_rekening']
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Proposal - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-user.css">
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .alert {
            position: relative;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .alert-error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #7f1d1d;
        }
        .alert-success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        .alert-content {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .alert-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            margin-left: auto;
        }
        .alert ul {
            margin: 0.5rem 0 0 1rem;
        }
        .required::after {
            content: " *";
            color: #ef4444;
        }
        .file-preview {
            background: #f3f4f6;
            padding: 0.5rem;
            border-radius: 4px;
            margin-top: 0.5rem;
            border: 1px solid #e5e7eb;
        }
        .file-preview.error {
            background: #fee2e2;
            border-color: #ef4444;
            color: #7f1d1d;
        }
        .current-file {
            background: #d1fae5;
            padding: 0.5rem;
            border-radius: 4px;
            margin-top: 0.5rem;
            border: 1px solid #10b981;
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
                    <h1>Edit Proposal</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        <div class="user-role">User</div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Tampilkan pesan error jika ada -->
                <?php if (isset($_SESSION['error_messages'])): ?>
                    <div class="alert alert-error">
                        <div class="alert-content">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div style="flex: 1;">
                                <strong>Terjadi kesalahan:</strong>
                                <ul>
                                    <?php foreach ($_SESSION['error_messages'] as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                        </div>
                    </div>
                    <?php unset($_SESSION['error_messages']); ?>
                <?php endif; ?>

                <div class="section-header">
                    <h2>Edit Proposal Bantuan</h2>
                    <p>Perbarui data proposal Anda</p>
                </div>

                <form class="proposal-form" action="../controller/pengajuan-controller.php?action=edit" method="POST" enctype="multipart/form-data" id="proposalForm">
                    <input type="hidden" name="proposal_id" value="<?php echo $proposal['id']; ?>">
                    
                    <div class="form-group">
                        <label for="nama-lembaga" class="required">Nama Lembaga</label>
                        <input type="text" id="nama-lembaga" name="nama_lembaga" required
                            placeholder="Masukkan nama lembaga" 
                            value="<?php echo htmlspecialchars($form_data['nama_lembaga'] ?? ''); ?>"
                            oninvalid="this.setCustomValidity('Nama lembaga harus diisi')" 
                            oninput="this.setCustomValidity('')">
                    </div>

                    <div class="form-group">
                        <label for="alamat" class="required">Alamat</label>
                        <textarea id="alamat" name="alamat" required
                            placeholder="Masukkan alamat lengkap" rows="3"
                            oninvalid="this.setCustomValidity('Alamat harus diisi')" 
                            oninput="this.setCustomValidity('')"><?php echo htmlspecialchars($form_data['alamat'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="jenis-bantuan" class="required">Jenis Bantuan</label>
                            <select id="jenis-bantuan" name="jenis_bantuan" required
                                oninvalid="this.setCustomValidity('Jenis bantuan harus dipilih')" 
                                oninput="this.setCustomValidity('')">
                                <option value="">Pilih Jenis Bantuan</option>
                                <option value="renovasi" <?php echo ($form_data['jenis_bantuan'] ?? '') == 'renovasi' ? 'selected' : ''; ?>>Renovasi Bangunan</option>
                                <option value="sarana" <?php echo ($form_data['jenis_bantuan'] ?? '') == 'sarana' ? 'selected' : ''; ?>>Sarana Ibadah</option>
                                <option value="pendidikan" <?php echo ($form_data['jenis_bantuan'] ?? '') == 'pendidikan' ? 'selected' : ''; ?>>Pendidikan Agama</option>
                                <option value="lainnya" <?php echo ($form_data['jenis_bantuan'] ?? '') == 'lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jumlah-diajukan" class="required">Jumlah yang Diajukan (Rp)</label>
                            <input type="number" id="jumlah-diajukan" name="jumlah_diajukan" required
                                placeholder="Contoh: 5000000" min="0" step="1000" 
                                value="<?php echo htmlspecialchars($form_data['jumlah_diajukan'] ?? ''); ?>"
                                oninvalid="this.setCustomValidity('Jumlah yang diajukan harus diisi')" 
                                oninput="this.setCustomValidity('')">
                            <small>Minimal: Rp 0</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi" class="required">Deskripsi Proposal</label>
                        <textarea id="deskripsi" name="deskripsi" required
                            placeholder="Jelaskan detail proposal bantuan yang diajukan" rows="5"
                            oninvalid="this.setCustomValidity('Deskripsi proposal harus diisi')" 
                            oninput="this.setCustomValidity('')"><?php echo htmlspecialchars($form_data['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="dokumen">Upload Dokumen Pendukung Baru</label>
                        <input type="file" id="dokumen" name="dokumen" accept=".pdf,.doc,.docx,.jpg,.png">
                        <small>Format: PDF, DOC, DOCX, JPG, PNG (Maks. 5MB). Kosongkan jika tidak ingin mengubah file.</small>
                        
                        <!-- Tampilkan file saat ini -->
                        <?php if (!empty($proposal['dokumen'])): ?>
                            <div class="current-file">
                                <i class="fas fa-file"></i> 
                                <strong>File Saat Ini:</strong> 
                                <a href="../<?php echo $proposal['dokumen']; ?>" target="_blank" style="margin-left: 0.5rem;">
                                    <?php echo basename($proposal['dokumen']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div id="file-preview"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="bank" class="required">Bank</label>
                            <select id="bank" name="bank" required
                                oninvalid="this.setCustomValidity('Bank harus dipilih')" 
                                oninput="this.setCustomValidity('')">
                                <option value="">Pilih Jenis Bank</option>
                                <option value="BRI" <?php echo ($form_data['bank'] ?? '') == 'BRI' ? 'selected' : ''; ?>>BRI</option>
                                <option value="BNI" <?php echo ($form_data['bank'] ?? '') == 'BNI' ? 'selected' : ''; ?>>BNI</option>
                                <option value="BSG" <?php echo ($form_data['bank'] ?? '') == 'BSG' ? 'selected' : ''; ?>>BSG</option>
                                <option value="Mandiri" <?php echo ($form_data['bank'] ?? '') == 'Mandiri' ? 'selected' : ''; ?>>Mandiri</option>
                                <option value="BCA" <?php echo ($form_data['bank'] ?? '') == 'BCA' ? 'selected' : ''; ?>>BCA</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nomor-rekening" class="required">Nomor Rekening</label>
                            <input type="text" id="nomor-rekening" name="nomor_rekening" required
                                placeholder="164238423837" pattern="[0-9]+" 
                                title="Hanya angka yang diperbolehkan"
                                value="<?php echo htmlspecialchars($form_data['nomor_rekening'] ?? ''); ?>"
                                oninvalid="this.setCustomValidity('Nomor rekening harus diisi dan hanya berisi angka')" 
                                oninput="this.setCustomValidity('')">
                            <small>Hanya angka tanpa spasi atau karakter khusus</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="status-proposal.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" name="update_proposal" value="1" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Perbarui Proposal
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-user.js"></script>
    <script>
        // Validasi form client-side
        function validateForm() {
            console.log('Validating form...');
            
            const form = document.getElementById('proposalForm');
            const jumlahDiajukan = document.getElementById('jumlah-diajukan').value;
            const nomorRekening = document.getElementById('nomor-rekening').value;
            const fileInput = document.getElementById('dokumen');
            let isValid = true;
            
            console.log('Form data:', {
                jumlahDiajukan: jumlahDiajukan,
                nomorRekening: nomorRekening,
                hasFile: fileInput.files.length > 0
            });
            
            // Validasi jumlah diajukan
            if (jumlahDiajukan < 0) {
                alert('Jumlah yang diajukan tidak boleh negatif');
                isValid = false;
            }
            
            // Validasi nomor rekening
            if (!/^\d+$/.test(nomorRekening)) {
                alert('Nomor rekening hanya boleh berisi angka');
                isValid = false;
            }
            
            // Validasi file size
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB');
                    isValid = false;
                }
            }
            
            if (isValid) {
                // Tampilkan loading
                const submitBtn = document.getElementById('submitBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memperbarui...';
                submitBtn.disabled = true;
                
                console.log('Form validation passed, submitting...');
            } else {
                console.log('Form validation failed');
            }
            
            return isValid;
        }
        
        // Event listener untuk form submission
        document.getElementById('proposalForm').addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            console.log('Form submission allowed');
            return true;
        });
        
        // Preview file yang diupload
        document.getElementById('dokumen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('file-preview');
            
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                const fileType = file.type;
                const isValidType = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg',
                    'image/jpg',
                    'image/png'
                ].includes(fileType);
                
                const isValidSize = file.size <= (5 * 1024 * 1024);
                
                let previewHTML = `
                    <div class="file-preview ${!isValidType || !isValidSize ? 'error' : ''}">
                        <i class="fas fa-file"></i> 
                        <strong>${file.name}</strong> (${fileSize} MB)
                `;
                
                if (!isValidType) {
                    previewHTML += `<div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">
                        <i class="fas fa-exclamation-triangle"></i> Format file tidak didukung
                    </div>`;
                }
                
                if (!isValidSize) {
                    previewHTML += `<div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;">
                        <i class="fas fa-exclamation-triangle"></i> File melebihi 5MB
                    </div>`;
                }
                
                previewHTML += `</div>`;
                preview.innerHTML = previewHTML;
            } else {
                preview.innerHTML = '';
            }
        });
        
        // Format input jumlah uang
        document.getElementById('jumlah-diajukan').addEventListener('input', function(e) {
            // Hapus karakter non-digit
            let value = e.target.value.replace(/\D/g, '');
            
            // Format sebagai angka
            if (value) {
                value = parseInt(value).toString();
            }
            
            e.target.value = value;
        });
        
        // Format input nomor rekening
        document.getElementById('nomor-rekening').addEventListener('input', function(e) {
            // Hapus karakter non-digit
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });
        
        console.log('Edit form initialization completed');
    </script>
</body>
</html>