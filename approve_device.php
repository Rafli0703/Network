<?php
// approve_device.php (assign dummy device + random bandwidth 0-100)
require 'config.php';
if (!is_admin_logged_in()) { header('Location: admin_login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id'] ?? 0);
    if ($student_id <= 0) {
        $_SESSION['flash'] = "Student ID tidak valid.";
        header('Location: admin_dashboard.php'); exit;
    }

    // cek mahasiswa exist
    $s = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $s->bind_param('i', $student_id);
    $s->execute();
    $s->store_result();
    if ($s->num_rows == 0) {
        $s->close();
        $_SESSION['flash'] = "Mahasiswa tidak ditemukan.";
        header('Location: admin_dashboard.php'); exit;
    }
    $s->close();

    // ambil first available dummy device
    $d = $conn->query("SELECT id, device_name, device_brand, device_ip FROM dummy_devices WHERE used = 0 LIMIT 1");
    if (!$d || $d->num_rows == 0) {
        $_SESSION['flash'] = "Tidak ada dummy device tersisa. Tambahkan dummy devices lewat phpMyAdmin.";
        header('Location: admin_dashboard.php'); exit;
    }
    $dd = $d->fetch_assoc();
    $dummy_id = intval($dd['id']);
    $device_name = $dd['device_name'];
    $device_brand = $dd['device_brand'];
    $device_ip = $dd['device_ip'];

    // generate random bandwidth 0-100
    $bandwidth = rand(0, 100);
    // status implied by bandwidth; we store bandwidth, not status column (status derived when tampil)

    // insert ke devices
    $ins = $conn->prepare("INSERT INTO devices (student_id, device_name, device_brand, device_ip, bandwidth) VALUES (?, ?, ?, ?, ?)");
    $ins->bind_param('isssi', $student_id, $device_name, $device_brand, $device_ip, $bandwidth);
    if ($ins->execute()) {
        // tandai dummy used = 1
        $upd_dummy = $conn->prepare("UPDATE dummy_devices SET used = 1 WHERE id = ?");
        $upd_dummy->bind_param('i', $dummy_id);
        $upd_dummy->execute();
        $upd_dummy->close();

        // set student can_login = 1
        $upd_student = $conn->prepare("UPDATE students SET can_login = 1 WHERE id = ?");
        $upd_student->bind_param('i', $student_id);
        $upd_student->execute();
        $upd_student->close();

        $_SESSION['flash'] = "Mahasiswa di-approve. Perangkat dummy telah ditambahkan (bandwidth: {$bandwidth} Mbps).";
    } else {
        $_SESSION['flash'] = "Gagal menambah perangkat: " . $ins->error;
    }
    $ins->close();
}

header('Location: admin_dashboard.php'); exit;
?>
