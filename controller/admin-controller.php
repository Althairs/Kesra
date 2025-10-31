<?php
session_start();
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header('Location: ../login.php');
        exit();
    }

    $action = $_POST['action'] ?? 'create';

    switch ($action) {
        case 'create':
            createAdmin($koneksi);
            break;
        
        case 'update':
            updateAdmin($koneksi);
            break;
        
        case 'delete':
            deleteAdmin($koneksi);
            break;
        
        default:
            $_SESSION['error'] = "Aksi tidak valid";
            header('Location: ../admin/tambah-admin.php');
            exit();
    }
}

function createAdmin($koneksi) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Konfirmasi password tidak sesuai";
        header('Location: ../admin/tambah-admin.php');
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error'] = "Password minimal 8 karakter";
        header('Location: ../admin/tambah-admin.php');
        exit();
    }

    // Cek apakah username atau email sudah ada
    $check_query = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Username atau email sudah terdaftar";
        header('Location: ../admin/tambah-admin.php');
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'admin';

    $query = "INSERT INTO users (username, email, password, role) 
              VALUES ('$username', '$email', '$hashed_password', '$role')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Admin berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menambahkan admin: " . mysqli_error($koneksi);
    }
    
    header('Location: ../admin/tambah-admin.php');
    exit();
}

function updateAdmin($koneksi) {
    $id = mysqli_real_escape_string($koneksi, $_POST['admin_id']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi konfirmasi password jika password diisi
    if (!empty($password)) {
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Konfirmasi password tidak sesuai";
            header('Location: ../admin/tambah-admin.php');
            exit();
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = "Password minimal 8 karakter";
            header('Location: ../admin/tambah-admin.php');
            exit();
        }
    }

    // Cek apakah username atau email sudah ada (kecuali untuk user yang sedang diupdate)
    $check_query = "SELECT id FROM users WHERE (username = '$username' OR email = '$email') AND id != '$id'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Username atau email sudah terdaftar";
        header('Location: ../admin/tambah-admin.php');
        exit();
    }

    // Update query
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET username = '$username', email = '$email', password = '$hashed_password' WHERE id = '$id'";
    } else {
        $query = "UPDATE users SET username = '$username', email = '$email' WHERE id = '$id'";
    }
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Admin berhasil diupdate";
    } else {
        $_SESSION['error'] = "Gagal mengupdate admin: " . mysqli_error($koneksi);
    }
    
    header('Location: ../admin/tambah-admin.php');
    exit();
}

function deleteAdmin($koneksi) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    
    // Cek apakah admin mencoba menghapus dirinya sendiri
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Anda tidak dapat menghapus akun sendiri";
        header('Location: ../admin/tambah-admin.php');
        exit();
    }

    // Cek apakah admin ada
    $check_query = "SELECT id FROM users WHERE id = '$id' AND role = 'admin'";
    $check_result = mysqli_query($koneksi, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        $_SESSION['error'] = "Admin tidak ditemukan";
        header('Location: ../admin/tambah-admin.php');
        exit();
    }

    $query = "DELETE FROM users WHERE id = '$id' AND role = 'admin'";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Admin berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus admin: " . mysqli_error($koneksi);
    }
    
    header('Location: ../admin/tambah-admin.php');
    exit();
}
?>