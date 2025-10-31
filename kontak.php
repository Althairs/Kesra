<?php
$password = 'admin123';
$hash = '$2y$10$4Hx0swbyEdmbLagzPIufIuAf1UftepO0V.gCcI78twu2YJ.HPXaOy';

echo "Password: " . $password . "<br>";
echo "Hash: " . $hash . "<br>";
echo "Verify result: " . (password_verify($password, $hash) ? 'TRUE' : 'FALSE') . "<br>";

// Coba buat hash baru
$new_hash = password_hash($password, PASSWORD_DEFAULT);
echo "New hash: " . $new_hash . "<br>";
echo "New verify result: " . (password_verify($password, $new_hash) ? 'TRUE' : 'FALSE');
?>