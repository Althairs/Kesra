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

// Cek apakah mode edit
$is_edit = false;
$berita_data = null;
if (isset($_GET['edit'])) {
    $is_edit = true;
    $id_berita = $_GET['edit'];
    require_once '../koneksi.php';
    $query = "SELECT * FROM berita WHERE id_berita = $id_berita";
    $result = mysqli_query($koneksi, $query);
    if (mysqli_num_rows($result) > 0) {
        $berita_data = mysqli_fetch_assoc($result);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit Berita' : 'Buat Berita'; ?> - Bagian Kesra</title>
    <link rel="stylesheet" href="../css/dashboard-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
                    <h1><?php echo $is_edit ? 'Edit Berita' : 'Buat Berita'; ?></h1>
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

                <div class="section-header">
                    <h2><?php echo $is_edit ? 'Edit Berita' : 'Buat Berita Baru'; ?></h2>
                    <p><?php echo $is_edit ? 'Perbarui berita yang dipilih' : 'Publikasikan berita terbaru mengenai program bantuan dan informasi Kesra'; ?></p>
                </div>

                <form class="news-form" action="../controller/berita-controller.php" method="POST" enctype="multipart/form-data" id="beritaForm">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id_berita" value="<?php echo $berita_data['id_berita']; ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="existing_images" id="existingImages" value="<?php echo $berita_data['gambar_berita'] ?? ''; ?>">
                    <?php else: ?>
                        <input type="hidden" name="action" value="create">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="judul">Judul Berita *</label>
                        <input type="text" id="judul" name="judul" required 
                               placeholder="Masukkan judul berita" 
                               value="<?php echo $is_edit ? htmlspecialchars($berita_data['judul_berita']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Singkat *</label>
                        <textarea id="deskripsi" name="deskripsi" required 
                                  placeholder="Masukkan deskripsi singkat berita" 
                                  rows="3"><?php echo $is_edit ? htmlspecialchars($berita_data['deskripsi_berita']) : ''; ?></textarea>
                        <small>Deskripsi singkat yang akan ditampilkan di halaman berita</small>
                    </div>

                    <div class="form-group">
                        <label for="konten">Konten Berita *</label>
                        <textarea id="konten" name="konten" required 
                                  placeholder="Tulis konten berita lengkap di sini..." 
                                  rows="10"><?php echo $is_edit ? htmlspecialchars($berita_data['konten_berita'] ?? '') : ''; ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <select id="kategori" name="kategori">
                                <option value="">Pilih Kategori</option>
                                <option value="bantuan" <?php echo ($is_edit && $berita_data['kategori_berita'] == 'bantuan') ? 'selected' : ''; ?>>Info Bantuan</option>
                                <option value="pengumuman" <?php echo ($is_edit && $berita_data['kategori_berita'] == 'pengumuman') ? 'selected' : ''; ?>>Pengumuman</option>
                                <option value="program" <?php echo ($is_edit && $berita_data['kategori_berita'] == 'program') ? 'selected' : ''; ?>>Program Kesra</option>
                                <option value="berita" <?php echo ($is_edit && $berita_data['kategori_berita'] == 'berita') ? 'selected' : ''; ?>>Berita Umum</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="gambar">Gambar Berita (Max 10 gambar)</label>
                            <input type="file" id="gambar" name="gambar[]" accept="image/*" multiple onchange="previewImages(this)">
                            <small>Format: JPG, PNG, GIF. Maksimal 2MB per gambar. <strong>Gambar pertama akan menjadi sampul</strong></small>
                            
                            <!-- Container untuk preview gambar baru -->
                            <div id="image-preview" class="image-preview-container"></div>
                            
                            <?php if ($is_edit && !empty($berita_data['gambar_berita'])): ?>
                                <div class="current-images">
                                    <p><strong>Gambar saat ini:</strong></p>
                                    <div id="existing-images-list" class="existing-images-list">
                                        <?php
                                        $gambar_array = explode(',', $berita_data['gambar_berita']);
                                        foreach ($gambar_array as $index => $gambar) {
                                            echo '<div class="image-item" data-image="' . $gambar . '">';
                                            echo '<img src="../img/berita/' . $gambar . '" alt="Gambar berita">';
                                            echo '<div class="image-info">';
                                            if ($index == 0) {
                                                echo '<span class="cover-badge"><i class="fas fa-star"></i> Sampul</span>';
                                            }
                                            echo '<button type="button" class="btn-remove-existing" onclick="removeExistingImage(\'' . $gambar . '\', ' . $index . ')"><i class="fas fa-times"></i></button>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    <small>Klik tombol <i class="fas fa-times"></i> untuk menghapus gambar</small>
                                </div>
                                <input type="hidden" name="removed_images" id="removedImages" value="">
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_publish">Tanggal Publish</label>
                            <input type="datetime-local" id="tanggal_publish" name="tanggal_publish" 
                                   value="<?php echo $is_edit ? date('Y-m-d\TH:i', strtotime($berita_data['tanggal_publish'] ?? $berita_data['tanggal_dibuat'])) : ''; ?>">
                            <small>Kosongkan untuk publish sekarang</small>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="publish" <?php echo ($is_edit && $berita_data['status_berita'] == 'publish') ? 'selected' : ''; ?>>Publish</option>
                                <option value="draft" <?php echo ($is_edit && $berita_data['status_berita'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <?php if ($is_edit): ?>
                            <a href="buat-berita.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Berita
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteNews(<?php echo $berita_data['id_berita']; ?>)">
                                <i class="fas fa-trash"></i> Hapus Berita
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline" onclick="saveDraft()">
                                <i class="fas fa-save"></i> Simpan Draft
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Publish Berita
                            </button>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Daftar Berita Existing -->
                <div class="recent-activity" style="margin-top: 3rem;">
                    <h3>Daftar Berita</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once '../koneksi.php';
                                $query = "SELECT * FROM berita ORDER BY tanggal_dibuat DESC";
                                $result = mysqli_query($koneksi, $query);
                                
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $gambar_pertama = !empty($row['gambar_berita']) ? explode(',', $row['gambar_berita'])[0] : null;
                                        echo '
                                        <tr>
                                            <td>' . htmlspecialchars($row['judul_berita']) . '</td>
                                            <td>' . ucfirst($row['kategori_berita'] ?? '-') . '</td>
                                            <td><span class="status-badge ' . $row['status_berita'] . '">' . ucfirst($row['status_berita']) . '</span></td>
                                            <td>' . date('d F Y', strtotime($row['tanggal_dibuat'])) . '</td>
                                            <td>';
                                        if ($gambar_pertama) {
                                            echo '<img src="../img/berita/' . $gambar_pertama . '" alt="Gambar" style="width: 50px; height: 50px; object-fit: cover;">';
                                        } else {
                                            echo '<i class="fas fa-newspaper"></i>';
                                        }
                                        echo '</td>
                                            <td>
                                                <a href="buat-berita.php?edit=' . $row['id_berita'] . '" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i> Edit</a>
                                                <button onclick="deleteNews(' . $row['id_berita'] . ')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                                            </td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">Belum ada berita</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/dashboard-admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        let selectedFiles = [];
        let removedImages = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Set tanggal default ke hari ini jika tidak dalam mode edit
            <?php if (!$is_edit): ?>
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                document.getElementById('tanggal_publish').value = now.toISOString().slice(0, 16);
            <?php endif; ?>
        });

        function previewImages(input) {
            const previewContainer = document.getElementById('image-preview');
            const files = Array.from(input.files);
            
            // Reset preview container
            previewContainer.innerHTML = '';
            
            // Filter files yang sudah dipilih sebelumnya
            const newFiles = files.filter(file => 
                !selectedFiles.some(existingFile => 
                    existingFile.name === file.name && existingFile.size === file.size
                )
            );
            
            // Tambahkan file baru
            selectedFiles = [...selectedFiles, ...newFiles];
            
            // Update input files
            updateFileInput();
            
            // Tampilkan preview
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="image-info">
                            ${index === 0 ? '<span class="cover-badge"><i class="fas fa-star"></i> Sampul</span>' : ''}
                            <span class="image-number">${index + 1}</span>
                            <button type="button" class="btn-remove" onclick="removeImage(${index})"><i class="fas fa-times"></i></button>
                            <button type="button" class="btn-move-up" onclick="moveImageUp(${index})" ${index === 0 ? 'disabled' : ''}><i class="fas fa-arrow-up"></i></button>
                            <button type="button" class="btn-move-down" onclick="moveImageDown(${index})" ${index === selectedFiles.length - 1 ? 'disabled' : ''}><i class="fas fa-arrow-down"></i></button>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
            
            // Reset input file
            input.value = '';
        }

        function removeImage(index) {
            Swal.fire({
                title: 'Hapus Gambar?',
                text: "Gambar akan dihapus dari daftar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    selectedFiles.splice(index, 1);
                    updateFileInput();
                    refreshPreview();
                }
            });
        }

        function removeExistingImage(imageName, index) {
            Swal.fire({
                title: 'Hapus Gambar?',
                text: "Gambar akan dihapus dari berita",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    removedImages.push(imageName);
                    document.getElementById('removedImages').value = removedImages.join(',');
                    
                    const imageItem = document.querySelector(`.image-item[data-image="${imageName}"]`);
                    if (imageItem) {
                        imageItem.remove();
                    }
                    
                    // Update existing images list
                    const existingImages = document.getElementById('existingImages');
                    if (existingImages) {
                        const currentImages = existingImages.value.split(',').filter(img => img !== imageName);
                        existingImages.value = currentImages.join(',');
                    }
                }
            });
        }

        function moveImageUp(index) {
            if (index > 0) {
                [selectedFiles[index], selectedFiles[index - 1]] = [selectedFiles[index - 1], selectedFiles[index]];
                updateFileInput();
                refreshPreview();
            }
        }

        function moveImageDown(index) {
            if (index < selectedFiles.length - 1) {
                [selectedFiles[index], selectedFiles[index + 1]] = [selectedFiles[index + 1], selectedFiles[index]];
                updateFileInput();
                refreshPreview();
            }
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            document.getElementById('gambar').files = dataTransfer.files;
        }

        function refreshPreview() {
            const previewContainer = document.getElementById('image-preview');
            previewContainer.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="image-info">
                            ${index === 0 ? '<span class="cover-badge"><i class="fas fa-star"></i> Sampul</span>' : ''}
                            <span class="image-number">${index + 1}</span>
                            <button type="button" class="btn-remove" onclick="removeImage(${index})"><i class="fas fa-times"></i></button>
                            <button type="button" class="btn-move-up" onclick="moveImageUp(${index})" ${index === 0 ? 'disabled' : ''}><i class="fas fa-arrow-up"></i></button>
                            <button type="button" class="btn-move-down" onclick="moveImageDown(${index})" ${index === selectedFiles.length - 1 ? 'disabled' : ''}><i class="fas fa-arrow-down"></i></button>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
        }

        function saveDraft() {
            const judul = document.getElementById('judul').value;
            if (!judul) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Judul berita harus diisi untuk menyimpan draft!',
                    confirmButtonColor: 'var(--primary-color)'
                });
                return;
            }

            document.getElementById('status').value = 'draft';
            document.getElementById('beritaForm').submit();
        }

        function deleteNews(id) {
            Swal.fire({
                title: 'Hapus Berita?',
                text: "Anda tidak akan dapat mengembalikan data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../controller/berita-controller.php?action=delete&id=' + id;
                }
            });
        }

        // Validasi form sebelum submit
        document.getElementById('beritaForm').addEventListener('submit', function(e) {
            const judul = document.getElementById('judul').value;
            const deskripsi = document.getElementById('deskripsi').value;
            const konten = document.getElementById('konten').value;
            
            if (!judul || !deskripsi || !konten) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Harap isi semua field yang wajib diisi!',
                    confirmButtonColor: 'var(--primary-color)'
                });
                return false;
            }
            
            return true;
        });
    </script>

    <style>
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px dashed #dee2e6;
        }

        .image-preview-item {
            position: relative;
            width: 150px;
            height: 150px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .image-preview-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .image-preview-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-bottom: 1px solid #e9ecef;
        }

        .image-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            padding: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2px;
        }

        .cover-badge {
            background: var(--success-color);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .image-number {
            background: var(--primary-color);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-remove, .btn-remove-existing {
            background: var(--error-color);
            color: white;
            border: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            transition: all 0.3s ease;
        }

        .btn-remove:hover, .btn-remove-existing:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        .btn-move-up, .btn-move-down {
            background: var(--secondary-color);
            color: white;
            border: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            transition: all 0.3s ease;
        }

        .btn-move-up:hover:not(:disabled), .btn-move-down:hover:not(:disabled) {
            background: var(--primary-color);
            transform: scale(1.1);
        }

        .btn-move-up:disabled, .btn-move-down:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .existing-images-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .image-item {
            position: relative;
            width: 150px;
            height: 150px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .current-images {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .table-responsive {
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .data-table th, .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .data-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        .data-table img {
            border-radius: 4px;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.875rem;
            margin: 2px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-badge.publish {
            background-color: #d4edda;
            color: #155724;
        }
        .status-badge.draft {
            background-color: #fff3cd;
            color: #856404;
        }
        .text-center {
            text-align: center;
        }
    </style>
</body>
</html>