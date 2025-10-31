<?php
session_start();
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header('Location: ../login.php');
        exit();
    }

    $action = $_POST['action'] ?? 'create';

    if ($action == 'create' || $action == 'update') {
        $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
        $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
        $konten = mysqli_real_escape_string($koneksi, $_POST['konten']);
        $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
        $status = mysqli_real_escape_string($koneksi, $_POST['status']);
        $tanggal_publish = $_POST['tanggal_publish'] ? $_POST['tanggal_publish'] : date('Y-m-d H:i:s');
        $id_user = $_SESSION['user_id'];

        // Handle multiple file upload
        $gambar_berita = [];
        
        // Untuk update, ambil gambar yang sudah ada (kecuali yang dihapus)
        if ($action == 'update' && isset($_POST['id_berita'])) {
            $id_berita = $_POST['id_berita'];
            
            // Ambil gambar yang dihapus
            $removed_images = isset($_POST['removed_images']) ? explode(',', $_POST['removed_images']) : [];
            
            // Ambil gambar yang masih ada
            if (isset($_POST['existing_images']) && !empty($_POST['existing_images'])) {
                $existing_images = explode(',', $_POST['existing_images']);
                foreach ($existing_images as $image) {
                    if (!in_array($image, $removed_images) && !empty($image)) {
                        $gambar_berita[] = $image;
                    }
                }
                
                // Hapus file gambar yang dihapus dari server
                foreach ($removed_images as $removed_image) {
                    if (!empty($removed_image)) {
                        $file_path = '../img/berita/' . $removed_image;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
            }
        }

        // Upload gambar baru
        if (isset($_FILES['gambar']) && !empty($_FILES['gambar']['name'][0])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB per file
            $max_files = 10 - count($gambar_berita); // Maksimal 10 gambar total

            if ($max_files > 0) {
                foreach ($_FILES['gambar']['tmp_name'] as $key => $tmp_name) {
                    if (count($gambar_berita) >= 10) break;
                    
                    if ($_FILES['gambar']['error'][$key] === 0) {
                        if (in_array($_FILES['gambar']['type'][$key], $allowed_types) && $_FILES['gambar']['size'][$key] <= $max_size) {
                            $file_extension = pathinfo($_FILES['gambar']['name'][$key], PATHINFO_EXTENSION);
                            $filename = 'berita_' . time() . '_' . uniqid() . '.' . $file_extension;
                            $upload_path = '../img/berita/' . $filename;
                            
                            if (move_uploaded_file($tmp_name, $upload_path)) {
                                $gambar_berita[] = $filename;
                            }
                        }
                    }
                }
            }
        }

        $gambar_berita_str = implode(',', $gambar_berita);

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
        
        if (mysqli_query($koneksi, $query)) {
            if ($action == 'create') {
                $_SESSION['success'] = "Berita berhasil " . ($status == 'publish' ? 'dipublikasikan' : 'disimpan sebagai draft');
            } else {
                $_SESSION['success'] = "Berita berhasil diperbarui";
            }
        } else {
            $_SESSION['error'] = "Gagal menyimpan berita: " . mysqli_error($koneksi);
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

    $id_berita = $_GET['id'];
    
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