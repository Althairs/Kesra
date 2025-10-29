<?php
// include 'koneksi.php';

// test local
// $ip_address = $_SERVER['REMOTE_ADDR'];
// // Jika IPv6 localhost, sederhanakan menjadi localhost
// if ($ip_address == '::1') {
//     $ip_address = '127.0.0.1';
// }

// // $ip_address = $_SERVER['REMOTE_ADDR'];
// $user_agent = $_SERVER['HTTP_USER_AGENT'];
// $tanggal_kunjungan = date('Y-m-d');

// // Cek apakah IP sudah tercatat hari ini
// $cek_query = "SELECT id FROM pengunjung WHERE ip_address = ? AND tanggal_kunjungan = ?";
// $stmt = mysqli_prepare($koneksi, $cek_query);
// mysqli_stmt_bind_param($stmt, "ss", $ip_address, $tanggal_kunjungan);
// mysqli_stmt_execute($stmt);
// mysqli_stmt_store_result($stmt);

// if (mysqli_stmt_num_rows($stmt) == 0) {
//     // Jika belum, catat pengunjung baru
//     $insert_query = "INSERT INTO pengunjung (ip_address, user_agent, tanggal_kunjungan) VALUES (?, ?, ?)";
//     $stmt2 = mysqli_prepare($koneksi, $insert_query);
//     mysqli_stmt_bind_param($stmt2, "sss", $ip_address, $user_agent, $tanggal_kunjungan);
//     mysqli_stmt_execute($stmt2);
//     mysqli_stmt_close($stmt2);
// }

// Pastikan koneksi masih tersedia sebelum melakukan query
if (isset($koneksi)) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    // Jika IPv6 localhost, sederhanakan menjadi localhost
    if ($ip_address == '::1') {
        $ip_address = '127.0.0.1';
    }

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $tanggal_kunjungan = date('Y-m-d');

    // Cek apakah IP sudah tercatat hari ini
    $cek_query = "SELECT id FROM pengunjung WHERE ip_address = ? AND tanggal_kunjungan = ?";
    $stmt = mysqli_prepare($koneksi, $cek_query);
    mysqli_stmt_bind_param($stmt, "ss", $ip_address, $tanggal_kunjungan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 0) {
        // Jika belum, catat pengunjung baru
        $insert_query = "INSERT INTO pengunjung (ip_address, user_agent, tanggal_kunjungan) VALUES (?, ?, ?)";
        $stmt2 = mysqli_prepare($koneksi, $insert_query);
        mysqli_stmt_bind_param($stmt2, "sss", $ip_address, $user_agent, $tanggal_kunjungan);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    }

    mysqli_stmt_close($stmt);
    // Jangan tutup koneksi di sini
}
?>


