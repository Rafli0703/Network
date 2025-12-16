<?php
require 'config.php';
if (!is_admin_logged_in()) { header('Location: admin_login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id'] ?? 0);
    $device_name = trim($_POST['device_name'] ?? '');
    $device_brand = trim($_POST['device_brand'] ?? '');
    $device_ip = trim($_POST['device_ip'] ?? '');
    $bandwidth = ($_POST['bandwidth'] !== '' ? intval($_POST['bandwidth']) : NULL);
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;

    if ($student_id <= 0 || $device_name === '') {
        $_SESSION['flash'] = "Data perangkat tidak valid.";
        header('Location: admin_dashboard.php'); exit;
    }

    $stmt = $conn->prepare("INSERT INTO devices (student_id, device_name, device_brand, device_ip, bandwidth, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issiii', $student_id, $device_name, $device_brand, $device_ip, $bandwidth, $status);
    if ($stmt->execute()) {
        // set can_login = 1 untuk mahasiswa tsb
        $upd = $conn->prepare("UPDATE students SET can_login = 1 WHERE id = ?");
        $upd->bind_param('i', $student_id);
        $upd->execute();
        $upd->close();

        $_SESSION['flash'] = "Perangkat berhasil ditambahkan.";
    } else {
        $_SESSION['flash'] = "Gagal menambahkan perangkat: " . $stmt->error;
    }
    $stmt->close();
    header('Location: admin_dashboard.php');
    exit;
}
header('Location: admin_dashboard.php'); exit;
?>
