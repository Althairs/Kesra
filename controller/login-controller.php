<?php 
session_start();
include __DIR__ . '/../koneksi.php';

function clean_input($data){
    return htmlspecialchars(strip_tags(trim($data)));
}

function tampilkan($pesan, $type = 'error'){
    $_SESSION['pesan'] = $pesan;
    $_SESSION['type-pesan'] = $type;
    header('Location: ../login.php');
    exit();
}

if(isset($_POST['login'])){
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $selected_role = clean_input($_POST['role']);

    if(empty($username) || empty($password) || empty($selected_role)){
        tampilkan('Username, Password, dan Jenis Pengguna Harus Diisi!');
    }

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($user = mysqli_fetch_assoc($result)){

        if(password_verify($password,$user['password'])){
            
            // Validasi: apakah role yang dipilih sesuai dengan role di database
            if ($selected_role != $user['role']) {
                tampilkan('Jenis pengguna tidak sesuai! Silakan pilih jenis pengguna yang benar.');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role']; 

            $_SESSION['pesan'] = 'Login berhasil';
            $_SESSION['type-pesan'] = 'success';

            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../user/dashboard.php');
            }
            exit();
        }else{
            tampilkan('Password salah!');
        }
    } else{
        tampilkan('Username tidak ditemukan!');
    }
}

if (isset($_POST['register'])){
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);

    if (empty($username)||empty($email)||empty($password)||empty($confirm_password)){
        tampilkan('Semua Field Harus Di isi!');
    } 
    if ($password != $confirm_password){
        tampilkan('Konfirmasi Password tidak sesuai!');
    } 
    if (strlen($password) < 10){
        tampilkan('Password minimal 10 karakter!');
    }

    // Bagian mengecek username apakah ada
    $check_username = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($check_username, 's', $username);
    mysqli_stmt_execute($check_username);
    mysqli_stmt_store_result($check_username);

    if (mysqli_stmt_num_rows($check_username) > 0){
        tampilkan('Username Sudah digunakan!');
    }

    $check_email = mysqli_prepare($koneksi, "SELECT id FROM users WHERE email = ? ");
    mysqli_stmt_bind_param($check_email, 's', $email);
    mysqli_stmt_execute($check_email);
    mysqli_stmt_store_result($check_email);

    if (mysqli_stmt_num_rows($check_email) > 0){
        tampilkan('Email Sudah Digunakan');
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Tambahkan user baru 
    $insert_query = "INSERT INTO users (username, email, password, role) VALUES(?,?,?,'user')";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt)){
        // Untuk registrasi
        $user_id = mysqli_insert_id($koneksi);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'user'; 
        $_SESSION['pesan'] = 'Registrasi berhasil!';
        $_SESSION['type-pesan'] = 'success';
        header('Location: ../login.php');
        exit();
    } else{
        tampilkan('Terjadi kesalahan saat registrasi');
    }
}

if (isset($_GET["logout"])){
    session_destroy();
    header('Location: ../index.php');
    exit();
}
?>