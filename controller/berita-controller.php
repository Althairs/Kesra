<?php
session_start();
require_once '../koneksi.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start debug log
error_log("=== BERITA CONTROLLER STARTED ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header('Location: ../login.php');
        exit();
    }

    $action = $_POST['action'] ?? 'create';
    
    // Debug: Log semua input
    error_log("=== START PROCESSING FORM ===");
    error_log("Action: " . $action);
    
    if (isset($_POST['judul'])) {
        error_log("Judul: " . $_POST['judul']);
    }
    
    if (isset($_POST['id_berita'])) {
        error_log("Berita ID: " . $_POST['id_berita']);
    }
    
    if ($action == 'create' || $action == 'update') {
        $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
        $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
        $konten = mysqli_real_escape_string($koneksi, $_POST['konten']);
        $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
        $status = mysqli_real_escape_string($koneksi, $_POST['status']);
        $tanggal_publish = $_POST['tanggal_publish'] ? $_POST['tanggal_publish'] : date('Y-m-d H:i:s');
        $id_user = $_SESSION['user_id'];

        // Inisialisasi array untuk gambar
        $gambar_berita = [];
        
        // **PROCESS EXISTING IMAGES FOR UPDATE**
        if ($action == 'update' && isset($_POST['id_berita'])) {
            $id_berita = (int)$_POST['id_berita'];
            error_log("=== UPDATE MODE ===");
            error_log("Berita ID for update: " . $id_berita);
            
            // Ambil gambar yang dihapus
            $removed_images = isset($_POST['removed_images']) ? explode(',', $_POST['removed_images']) : [];
            error_log("Removed images count: " . count($removed_images));
            
            // Ambil urutan gambar dari form (jika ada) atau dari database
            $existing_images = [];
            if (isset($_POST['image_order']) && !empty($_POST['image_order'])) {
                // Gunakan urutan dari form (drag & drop)
                $existing_images = explode(',', $_POST['image_order']);
                error_log("Using image order from form");
            } else {
                // Ambil dari database
                $query_existing = "SELECT gambar_berita FROM berita WHERE id_berita = $id_berita";
                $result_existing = mysqli_query($koneksi, $query_existing);
                if ($result_existing && mysqli_num_rows($result_existing) > 0) {
                    $row = mysqli_fetch_assoc($result_existing);
                    if (!empty($row['gambar_berita'])) {
                        $existing_images = explode(',', $row['gambar_berita']);
                    }
                }
            }
            
            error_log("Existing images before removal: " . implode(', ', $existing_images));
            
            // Filter gambar yang tidak dihapus
            foreach ($existing_images as $image) {
                $image = trim($image);
                if (!empty($image) && !in_array($image, $removed_images)) {
                    $gambar_berita[] = $image;
                }
            }
            error_log("Images after keeping: " . implode(', ', $gambar_berita));
            
            // Handle cover image jika ada perubahan
            if (isset($_POST['cover_image']) && !empty($_POST['cover_image'])) {
                $cover_image = $_POST['cover_image'];
                error_log("Cover image specified: " . $cover_image);
                
                // Jika cover image ada di array, pindahkan ke posisi pertama
                $cover_index = array_search($cover_image, $gambar_berita);
                if ($cover_index !== false && $cover_index > 0) {
                    // Pindahkan ke posisi pertama
                    array_splice($gambar_berita, $cover_index, 1);
                    array_unshift($gambar_berita, $cover_image);
                    error_log("Cover image moved to first position");
                }
            }
            
            // Hapus file gambar yang dihapus dari server
            foreach ($removed_images as $removed_image) {
                $removed_image = trim($removed_image);
                if (!empty($removed_image)) {
                    $file_path = '../img/berita/' . $removed_image;
                    if (file_exists($file_path)) {
                        if (unlink($file_path)) {
                            error_log("Deleted file: " . $removed_image);
                        }
                    }
                }
            }
        }
        
        // **PROCESS NEW UPLOADED IMAGES**
        error_log("=== PROCESSING NEW UPLOADS ===");
        
        if (isset($_FILES['gambar']) && is_array($_FILES['gambar']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
            $max_size = 2 * 1024 * 1024; // 2MB per file
            
            $files_count = count($_FILES['gambar']['name']);
            error_log("New files to process: " . $files_count);
            
            // Array untuk menyimpan file baru
            $new_images = [];
            
            for ($i = 0; $i < $files_count; $i++) {
                // Skip empty files
                if ($_FILES['gambar']['error'][$i] != 0 || $_FILES['gambar']['size'][$i] == 0) {
                    continue;
                }
                
                // Check if we've reached the limit
                if ((count($gambar_berita) + count($new_images)) >= 10) {
                    error_log("Max image limit reached (10)");
                    break;
                }
                
                $file_type = $_FILES['gambar']['type'][$i];
                $file_size = $_FILES['gambar']['size'][$i];
                $tmp_name = $_FILES['gambar']['tmp_name'][$i];
                $original_name = $_FILES['gambar']['name'][$i];
                
                // Validate file
                if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
                    // Generate unique filename
                    $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                    $filename = 'berita_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = '../img/berita/' . $filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $new_images[] = $filename;
                        error_log("Uploaded: " . $filename . " (" . round($file_size / 1024, 2) . " KB)");
                    }
                }
            }
            
            // Tambahkan gambar baru ke array
            // Untuk create: semua gambar baru
            // Untuk update: tambahkan di depan atau belakang sesuai kebutuhan
            
            if ($action == 'create') {
                // Untuk create, gambar pertama adalah sampul
                $gambar_berita = $new_images;
                error_log("Create mode: All new images added");
            } else {
                // Untuk update, kita perlu menentukan posisi gambar baru
                // Biasanya gambar baru ditambahkan di akhir, tapi jika user ingin menjadikan 
                // gambar baru sebagai sampul, harus diatur di frontend
                
                // Cek jika ada spesifikasi cover image untuk file baru
                if (isset($_POST['cover_image']) && $_POST['cover_image'] === 'NEW_FIRST' && !empty($new_images)) {
                    // Gambar pertama dari file baru menjadi sampul
                    // Tambahkan semua gambar baru di depan
                    $gambar_berita = array_merge($new_images, $gambar_berita);
                    error_log("Update mode: New images added at beginning (first new image is cover)");
                } else {
                    // Tambahkan gambar baru di akhir
                    $gambar_berita = array_merge($gambar_berita, $new_images);
                    error_log("Update mode: New images added at end");
                }
            }
        }
        
        // Prepare images string for database
        $gambar_berita_str = !empty($gambar_berita) ? implode(',', $gambar_berita) : '';
        error_log("Final images for DB: " . $gambar_berita_str);
        error_log("Total images: " . count($gambar_berita));

        if ($action == 'create') {
            $query = "INSERT INTO berita (judul_berita, deskripsi_berita, konten_berita, kategori_berita, gambar_berita, status_berita, tanggal_publish, id_user) 
                      VALUES ('$judul', '$deskripsi', '$konten', '$kategori', '$gambar_berita_str', '$status', '$tanggal_publish', '$id_user')";
        } else {
            $query = "UPDATE berita 
                      SET judul_berita = '$judul', 
                          deskripsi_berita = '$deskripsi', 
                          konten_berita = '$konten',
                          kategori_berita = '$kategori',
                          gambar_berita = '$gambar_berita_str', 
                          status_berita = '$status', 
                          tanggal_publish = '$tanggal_publish',
                          updated_at = NOW()
                      WHERE id_berita = $id_berita";
        }
        
        // Execute query
        if (mysqli_query($koneksi, $query)) {
            if ($action == 'create') {
                $_SESSION['success'] = "Berita berhasil " . ($status == 'publish' ? 'dipublikasikan' : 'disimpan sebagai draft');
            } else {
                $_SESSION['success'] = "Berita berhasil diperbarui";
                error_log("Update successful. Images in DB: " . $gambar_berita_str);
            }
        } else {
            $_SESSION['error'] = "Gagal menyimpan berita: " . mysqli_error($koneksi);
            error_log("Database error: " . mysqli_error($koneksi));
        }
        
        header('Location: ../admin/buat-berita.php');
        exit();
    }
}

// Handle DELETE action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header('Location: ../login.php');
        exit();
    }

    $id_berita = (int)$_GET['id'];
    
    // Ambil data gambar sebelum menghapus
    $query = "SELECT gambar_berita FROM berita WHERE id_berita = $id_berita";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $berita = mysqli_fetch_assoc($result);
        
        // Hapus file gambar dari server
        if (!empty($berita['gambar_berita'])) {
            $gambar_array = explode(',', $berita['gambar_berita']);
            foreach ($gambar_array as $gambar) {
                $file_path = '../img/berita/' . $gambar;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        // Hapus dari database
        $delete_query = "DELETE FROM berita WHERE id_berita = $id_berita";
        if (mysqli_query($koneksi, $delete_query)) {
            $_SESSION['success'] = "Berita berhasil dihapus";
        } else {
            $_SESSION['error'] = "Gagal menghapus berita: " . mysqli_error($koneksi);
        }
    } else {
        $_SESSION['error'] = "Berita tidak ditemukan";
    }
    
    header('Location: ../admin/buat-berita.php');
    exit();
}
?>