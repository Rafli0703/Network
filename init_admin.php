<?php
// init_admin.php - jalankan sekali untuk membuat admin default, lalu hapus
require 'config.php';

$username = 'admin';
$password_plain = 'admin123';

$hash = password_hash($password_plain, PASSWORD_DEFAULT);

// cek apakah sudah ada
$stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "Admin sudah ada. Hapus file init_admin.php setelah ini.";
    exit;
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param('ss', $username, $hash);
if ($stmt->execute()) {
    echo "Admin dibuat. username: admin  password: admin123<br>";
    echo "Hapus atau pindahkan file init_admin.php untuk keamanan.";
} else {
    echo "Gagal membuat admin: " . $stmt->error;
}
$stmt->close();
?>
