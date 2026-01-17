<?php
session_start();
// Cek apakah user sudah login dan role admin
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
//     header('Location: ../login.php');
//     exit();
// }

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
    <link rel="shortcut icon" href="../img/kesra.png" type="image/x-icon">
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
                        <input type="hidden" name="cover_image" id="coverImage" value="<?php echo !empty($berita_data['gambar_berita']) ? explode(',', $berita_data['gambar_berita'])[0] : ''; ?>">
                    <?php else: ?>
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="cover_image" id="coverImage" value="">
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
                            <input type="file" id="gambar" name="gambar[]" accept="image/*" multiple>
                            <small>Format: JPG, PNG, GIF. Maksimal 2MB per gambar. <strong><?php echo $is_edit ? 'Gambar sampul dipilih dari gambar yang ada' : 'Gambar pertama akan menjadi sampul'; ?></strong></small>
                            
                            <!-- Container untuk preview gambar baru -->
                            <div id="image-preview" class="image-preview-container"></div>
                            
                            <?php if ($is_edit && !empty($berita_data['gambar_berita'])): ?>
                                <div class="current-images">
                                    <p><strong>Gambar saat ini:</strong> <small>(Drag untuk mengurutkan, klik bintang untuk jadikan sampul)</small></p>
                                    <div id="existing-images-list" class="existing-images-list" data-sortable="true">
                                        <?php
                                        $gambar_array = explode(',', $berita_data['gambar_berita']);
                                        $cover_image = $gambar_array[0] ?? '';
                                        foreach ($gambar_array as $index => $gambar) {
                                            $is_cover = ($gambar === $cover_image);
                                            echo '<div class="image-item" data-image="' . $gambar . '" draggable="true">';
                                            echo '<img src="../img/berita/' . $gambar . '" alt="Gambar berita">';
                                            echo '<div class="image-info">';
                                            if ($is_cover) {
                                                echo '<span class="cover-badge active"><i class="fas fa-star"></i> Sampul</span>';
                                            } else {
                                                echo '<button type="button" class="btn-set-cover" onclick="setAsCover(\'' . $gambar . '\')" title="Jadikan sampul"><i class="far fa-star"></i></button>';
                                            }
                                            echo '<button type="button" class="btn-remove-existing" onclick="removeExistingImage(\'' . $gambar . '\', ' . $index . ')" title="Hapus gambar"><i class="fas fa-times"></i></button>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    <small>Drag untuk mengurutkan • Klik bintang untuk jadikan sampul • Klik X untuk hapus</small>
                                </div>
                                <input type="hidden" name="removed_images" id="removedImages" value="">
                                <input type="hidden" name="image_order" id="imageOrder" value="<?php echo $berita_data['gambar_berita']; ?>">
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
        // Global variables
        let removedImages = [];
        let draggedItem = null;
        let newFilesArray = []; // Untuk menyimpan file baru
        const isEditMode = <?php echo $is_edit ? 'true' : 'false'; ?>;
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== BERITA FORM LOADED ===');
            console.log('Edit mode:', isEditMode);
            
            <?php if ($is_edit && !empty($berita_data['gambar_berita'])): ?>
                console.log('Existing images:', '<?php echo $berita_data['gambar_berita']; ?>'.split(','));
                // Initialize image order
                document.getElementById('imageOrder').value = '<?php echo $berita_data['gambar_berita']; ?>';
            <?php endif; ?>

            // Set tanggal default ke hari ini jika tidak dalam mode edit
            <?php if (!$is_edit): ?>
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                document.getElementById('tanggal_publish').value = now.toISOString().slice(0, 16);
            <?php endif; ?>
            
            // Setup file input change listener
            setupFileInput();
            
            // Setup drag and drop for existing images (hanya jika mode edit)
            if (isEditMode) {
                setupDragAndDrop();
            }
        });

        // FUNGSI UTAMA UNTUK HANDLE FILE INPUT
        function setupFileInput() {
            const fileInput = document.getElementById('gambar');
            
            fileInput.addEventListener('change', function(e) {
                console.log('=== FILE INPUT CHANGE ===');
                const files = Array.from(e.target.files);
                console.log('New files selected:', files.length);
                
                // Reset array file baru
                newFilesArray = [];
                
                // Filter file yang valid
                files.forEach(file => {
                    if (file.size > 2 * 1024 * 1024) {
                        console.warn(`File ${file.name} exceeds 2MB limit`);
                        Swal.fire({
                            icon: 'warning',
                            title: 'File Terlalu Besar',
                            text: `File "${file.name}" melebihi batas 2MB`,
                            timer: 3000
                        });
                        return;
                    }
                    
                    if (!file.type.match('image.*')) {
                        console.warn(`File ${file.name} is not an image`);
                        return;
                    }
                    
                    newFilesArray.push(file);
                });
                
                // Tampilkan preview
                showNewFilesPreview();
                
                // Update file input dengan files yang valid
                updateFileInputWithValidFiles();
            });
        }
        
        function showNewFilesPreview() {
            const previewContainer = document.getElementById('image-preview');
            previewContainer.innerHTML = '';
            
            if (newFilesArray.length === 0) {
                previewContainer.innerHTML = '<p class="no-preview">Tidak ada gambar baru</p>';
                return;
            }
            
            newFilesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'image-preview-item';
                    previewItem.setAttribute('data-index', index);
                    
                    // Tentukan apakah mode create atau edit
                    let badgeHtml = '';
                    if (!isEditMode && index === 0) {
                        // Saat create, gambar pertama adalah sampul
                        badgeHtml = '<span class="cover-badge"><i class="fas fa-star"></i> Sampul</span>';
                    }
                    
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="image-info">
                            ${badgeHtml}
                            <span class="file-name">${file.name}</span>
                            <small>${formatFileSize(file.size)}</small>
                            <div class="image-actions">
                                <button type="button" class="btn-move-up" onclick="moveNewImageUp(${index})" title="Pindah ke atas" ${index === 0 ? 'disabled' : ''}>
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button type="button" class="btn-move-down" onclick="moveNewImageDown(${index})" title="Pindah ke bawah" ${index === newFilesArray.length - 1 ? 'disabled' : ''}>
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                                ${!isEditMode ? `
                                <button type="button" class="btn-set-new-cover" onclick="setNewImageAsCover(${index})" title="Jadikan sampul" ${index === 0 ? 'disabled' : ''}>
                                    <i class="${index === 0 ? 'fas' : 'far'} fa-star"></i>
                                </button>
                                ` : ''}
                                <button type="button" class="btn-remove-new" onclick="removeNewImage(${index})" title="Hapus">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
            
            console.log('Preview shown for', newFilesArray.length, 'files');
            console.log('Edit mode:', isEditMode, '- No cover badge for new files in edit mode');
        }
        
        function updateFileInputWithValidFiles() {
            const fileInput = document.getElementById('gambar');
            const dataTransfer = new DataTransfer();
            
            newFilesArray.forEach(file => {
                dataTransfer.items.add(file);
            });
            
            fileInput.files = dataTransfer.files;
            console.log('File input updated with', newFilesArray.length, 'valid files');
        }
        
        // FUNGSI UNTUK MENGATUR URUTAN GAMBAR BARU
        function moveNewImageUp(index) {
            if (index > 0) {
                [newFilesArray[index], newFilesArray[index - 1]] = [newFilesArray[index - 1], newFilesArray[index]];
                showNewFilesPreview();
            }
        }
        
        function moveNewImageDown(index) {
            if (index < newFilesArray.length - 1) {
                [newFilesArray[index], newFilesArray[index + 1]] = [newFilesArray[index + 1], newFilesArray[index]];
                showNewFilesPreview();
            }
        }
        
        function removeNewImage(index) {
            Swal.fire({
                title: 'Hapus Gambar Baru?',
                text: "Gambar akan dihapus dari daftar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    newFilesArray.splice(index, 1);
                    showNewFilesPreview();
                    updateFileInputWithValidFiles();
                }
            });
        }
        
        // Fungsi ini hanya untuk CREATE mode
        function setNewImageAsCover(index) {
            if (index > 0 && !isEditMode) {
                // Pindahkan gambar ke posisi pertama
                const coverImage = newFilesArray.splice(index, 1)[0];
                newFilesArray.unshift(coverImage);
                showNewFilesPreview();
                updateFileInputWithValidFiles();
                
                // Update cover image input
                document.getElementById('coverImage').value = 'NEW_FIRST';
                
                Swal.fire({
                    icon: 'success',
                    title: 'Gambar dijadikan sampul',
                    text: 'Gambar ini akan menjadi gambar utama',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // FUNGSI UNTUK GAMBAR EXISTING (Hanya untuk EDIT mode)
        function setupDragAndDrop() {
            const existingImagesList = document.getElementById('existing-images-list');
            if (!existingImagesList) return;
            
            // Setup drag events
            const items = existingImagesList.querySelectorAll('.image-item');
            items.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragover', handleDragOver);
                item.addEventListener('drop', handleDrop);
                item.addEventListener('dragend', handleDragEnd);
            });
        }
        
        function handleDragStart(e) {
            draggedItem = this;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.getAttribute('data-image'));
            setTimeout(() => this.classList.add('dragging'), 0);
        }
        
        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        }
        
        function handleDrop(e) {
            e.preventDefault();
            if (draggedItem !== this) {
                const existingImagesList = document.getElementById('existing-images-list');
                const items = Array.from(existingImagesList.querySelectorAll('.image-item:not(.dragging)'));
                const draggedIndex = items.indexOf(draggedItem);
                const targetIndex = items.indexOf(this);
                
                if (draggedIndex < targetIndex) {
                    this.parentNode.insertBefore(draggedItem, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedItem, this);
                }
                
                // Update image order
                updateExistingImageOrder();
            }
        }
        
        function handleDragEnd() {
            this.classList.remove('dragging');
            draggedItem = null;
        }
        
        function updateExistingImageOrder() {
            const existingImagesList = document.getElementById('existing-images-list');
            const items = existingImagesList.querySelectorAll('.image-item');
            const imageOrder = Array.from(items).map(item => item.getAttribute('data-image'));
            
            document.getElementById('imageOrder').value = imageOrder.join(',');
            
            // Update cover image jika berubah
            const firstImage = imageOrder[0];
            const currentCover = document.getElementById('coverImage').value;
            
            if (firstImage && firstImage !== currentCover) {
                document.getElementById('coverImage').value = firstImage;
                
                // Update UI untuk menunjukkan gambar sampul baru
                items.forEach((item, index) => {
                    const badge = item.querySelector('.cover-badge');
                    const starBtn = item.querySelector('.btn-set-cover');
                    const isCover = index === 0;
                    
                    if (isCover) {
                        if (starBtn) {
                            starBtn.outerHTML = '<span class="cover-badge active"><i class="fas fa-star"></i> Sampul</span>';
                        } else if (badge) {
                            badge.classList.add('active');
                        }
                    } else {
                        if (badge && badge.classList.contains('active')) {
                            badge.outerHTML = '<button type="button" class="btn-set-cover" onclick="setAsCover(\'' + item.getAttribute('data-image') + '\')" title="Jadikan sampul"><i class="far fa-star"></i></button>';
                        }
                    }
                });
                
                console.log('Cover image updated to:', firstImage);
            }
        }

        function setAsCover(imageName) {
            console.log('Setting as cover:', imageName);
            
            const existingImagesList = document.getElementById('existing-images-list');
            const items = Array.from(existingImagesList.querySelectorAll('.image-item'));
            const targetItem = items.find(item => item.getAttribute('data-image') === imageName);
            
            if (targetItem) {
                // Pindahkan item ke posisi pertama
                existingImagesList.insertBefore(targetItem, existingImagesList.firstChild);
                
                // Update cover image
                document.getElementById('coverImage').value = imageName;
                
                // Update UI
                updateExistingImageOrder();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Gambar dijadikan sampul',
                    text: 'Gambar ini akan menjadi gambar utama',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }

        function removeExistingImage(imageName, index) {
            console.log('Removing existing image:', imageName, 'at index:', index);
            
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
                    
                    // Update image order
                    updateExistingImageOrder();
                    
                    console.log('Image marked for removal:', imageName);
                    console.log('Removed images list:', removedImages);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Gambar Dihapus',
                        text: 'Gambar akan dihapus saat Anda menyimpan perubahan',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
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
            console.log('Delete news with ID:', id);
            
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
                    console.log('Proceeding with deletion of ID:', id);
                    window.location.href = '../controller/berita-controller.php?action=delete&id=' + id;
                }
            });
        }

        // Validasi form sebelum submit
        document.getElementById('beritaForm').addEventListener('submit', function(e) {
            console.log('=== FORM SUBMISSION START ===');
            console.log('Action:', document.querySelector('input[name="action"]').value);
            
            // Log form data for debugging
            const formData = new FormData(this);
            
            // Get file input
            const fileInput = document.getElementById('gambar');
            console.log('New files for upload:', newFilesArray.length);
            console.log('File input files count:', fileInput.files.length);
            
            // Log existing images status (hanya untuk edit)
            if (isEditMode) {
                const existingImagesInput = document.getElementById('existingImages');
                const removedImagesInput = document.getElementById('removedImages');
                const imageOrderInput = document.getElementById('imageOrder');
                const coverImageInput = document.getElementById('coverImage');
                
                if (existingImagesInput) {
                    console.log('Existing images:', existingImagesInput.value);
                }
                if (removedImagesInput) {
                    console.log('Removed images:', removedImagesInput.value);
                }
                if (imageOrderInput) {
                    console.log('Image order:', imageOrderInput.value);
                }
                if (coverImageInput) {
                    console.log('Cover image:', coverImageInput.value);
                }
            }
            
            // Validate form
            const judul = document.getElementById('judul').value.trim();
            const deskripsi = document.getElementById('deskripsi').value.trim();
            const konten = document.getElementById('konten').value.trim();
            
            if (!judul || !deskripsi || !konten) {
                e.preventDefault();
                
                let errorMessage = 'Harap isi semua field yang wajib diisi:\n';
                if (!judul) errorMessage += '• Judul Berita\n';
                if (!deskripsi) errorMessage += '• Deskripsi Singkat\n';
                if (!konten) errorMessage += '• Konten Berita\n';
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    html: errorMessage.replace(/\n/g, '<br>'),
                    confirmButtonColor: 'var(--primary-color)'
                });
                return false;
            }
            
            // Check total images count
            let totalImagesCount = newFilesArray.length;
            if (isEditMode) {
                const existingImagesInput = document.getElementById('existingImages');
                const removedImagesInput = document.getElementById('removedImages');
                
                if (existingImagesInput && existingImagesInput.value) {
                    const existingCount = existingImagesInput.value.split(',').filter(img => img.trim()).length;
                    const removedCount = removedImagesInput && removedImagesInput.value ? 
                        removedImagesInput.value.split(',').filter(img => img.trim()).length : 0;
                    
                    totalImagesCount += (existingCount - removedCount);
                }
            }
            
            if (totalImagesCount > 10) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Terlalu Banyak Gambar',
                    text: `Total gambar tidak boleh lebih dari 10. Saat ini ada ${totalImagesCount} gambar.`,
                    confirmButtonColor: 'var(--primary-color)'
                });
                return false;
            }
            
            // Check file sizes for new files
            let oversizedFiles = [];
            newFilesArray.forEach(file => {
                if (file.size > 2 * 1024 * 1024) {
                    oversizedFiles.push(file.name);
                }
            });
            
            if (oversizedFiles.length > 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    html: `File berikut melebihi 2MB:<br>${oversizedFiles.join('<br>')}`,
                    confirmButtonColor: 'var(--primary-color)'
                });
                return false;
            }
            
            console.log('Form validation passed');
            console.log('Total images count:', totalImagesCount);
            console.log('=== FORM SUBMISSION END ===');
            
            // Show loading indicator
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
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
            min-height: 100px;
        }

        .no-preview {
            color: #666;
            font-style: italic;
            text-align: center;
            width: 100%;
            padding: 20px;
        }

        .image-preview-item {
            position: relative;
            width: 180px;
            padding: 10px;
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
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .image-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .file-name {
            font-size: 0.85rem;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .image-info small {
            font-size: 0.75rem;
            color: #666;
        }

        .image-actions {
            display: flex;
            gap: 5px;
            margin-top: 5px;
            justify-content: center;
        }

        .cover-badge {
            background: var(--success-color);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 5px;
        }

        .cover-badge.active {
            background: #f59e0b;
        }

        .btn-set-cover {
            background: #fbbf24;
            color: white;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .btn-set-cover:hover {
            background: #f59e0b;
            transform: scale(1.1);
        }

        .btn-remove-existing, .btn-remove-new {
            background: var(--error-color);
            color: white;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .btn-remove-existing:hover, .btn-remove-new:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        .btn-move-up, .btn-move-down {
            background: var(--secondary-color);
            color: white;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .btn-move-up:hover:not(:disabled), .btn-move-down:hover:not(:disabled) {
            background: var(--primary-color);
            transform: scale(1.1);
        }

        .btn-move-up:disabled, .btn-move-down:disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .existing-images-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
            min-height: 100px;
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: #f9fafb;
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
            cursor: move;
            transition: all 0.3s ease;
        }

        .image-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .image-item.dragging {
            opacity: 0.5;
            border: 2px dashed var(--primary-color);
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-item .image-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255,255,255,0.95);
            padding: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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