<?php
session_start();
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_profile':
            updateProfile($koneksi);
            break;
        
        case 'change_password':
            changePassword($koneksi);
            break;
        
        default:
            $_SESSION['error_messages'] = ["Aksi tidak valid"];
            if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                header('Location: ../admin/profile-admin.php');
            } else {
                header('Location: ../profile.php');
            }
            exit();
    }
}

function updateProfile($koneksi) {
    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    
    // Ambil field tambahan untuk semua user
    $nama_lengkap = isset($_POST['nama_lengkap']) ? mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']) : '';
    $telepon = isset($_POST['telepon']) ? mysqli_real_escape_string($koneksi, $_POST['telepon']) : '';
    $alamat = isset($_POST['alamat']) ? mysqli_real_escape_string($koneksi, $_POST['alamat']) : '';
    
    // Validasi input
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username harus diisi";
    }
    
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    // Validasi telepon (opsional)
    if (!empty($telepon) && !preg_match('/^[0-9+\-\s()]{10,15}$/', $telepon)) {
        $errors[] = "Format nomor telepon tidak valid";
    }
    
    // Cek apakah username atau email sudah digunakan oleh user lain
    $check_query = "SELECT id FROM users WHERE (username = '$username' OR email = '$email') AND id != '$user_id'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $errors[] = "Username atau email sudah digunakan";
    }
    
    // Jika ada error, kembali ke halaman profile
    if (!empty($errors)) {
        $_SESSION['error_messages'] = $errors;
        
        if ($_SESSION['role'] == 'admin') {
            header('Location: ../admin/profile-admin.php');
        } else {
            header('Location: ../user/profile.php');
        }
        exit();
    }
    
    // Update data profile dengan field tambahan
    $query = "UPDATE users SET 
              username = '$username', 
              email = '$email', 
              nama_lengkap = '$nama_lengkap', 
              telepon = '$telepon', 
              alamat = '$alamat' 
              WHERE id = '$user_id'";
    
    if (mysqli_query($koneksi, $query)) {
        // Update session data
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        $_SESSION['success_message'] = "Profile berhasil diperbarui";
    } else {
        $_SESSION['error_messages'] = ["Gagal memperbarui profile: " . mysqli_error($koneksi)];
    }
    
    // Redirect berdasarkan role
    if ($_SESSION['role'] == 'admin') {
        header('Location: ../admin/profile-admin.php');
    } else {
        header('Location: ../user/profile.php');
    }
    exit();
}

function changePassword($koneksi) {
    // Cek apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    $errors = [];
    
    if (empty($current_password)) {
        $errors[] = "Password saat ini harus diisi";
    }
    
    if (empty($new_password)) {
        $errors[] = "Password baru harus diisi";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Password baru minimal 6 karakter";
    }
    
    if (empty($confirm_password)) {
        $errors[] = "Konfirmasi password harus diisi";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }
    
    // Jika ada error, kembali ke halaman profile
    if (!empty($errors)) {
        $_SESSION['error_messages'] = $errors;
        
        if ($_SESSION['role'] == 'admin') {
            header('Location: ../admin/profile-admin.php');
        } else {
            header('Location: ../user/profile.php');
        }
        exit();
    }
    
    // Verifikasi password saat ini
    $query = "SELECT password FROM users WHERE id = '$user_id'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error_messages'] = ["Password saat ini salah"];
            
            if ($_SESSION['role'] == 'admin') {
                header('Location: ../admin/profile-admin.php');
            } else {
                header('Location: ../user/profile.php');
            }
            exit();
        }
        
        // Update password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
        
        if (mysqli_query($koneksi, $update_query)) {
            $_SESSION['success_message'] = "Password berhasil diubah";
        } else {
            $_SESSION['error_messages'] = ["Gagal mengubah password: " . mysqli_error($koneksi)];
        }
    } else {
        $_SESSION['error_messages'] = ["User tidak ditemukan"];
    }
    
    // Redirect berdasarkan role
    if ($_SESSION['role'] == 'admin') {
        header('Location: ../admin/profile-admin.php');
    } else {
        header('Location: ../user/profile.php');
    }
    exit();
}
?>