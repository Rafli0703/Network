<?php
// delete_device.php (hard delete total device + mahasiswa)
// Hanya ubah file ini. Pastikan session/config benar.
require 'config.php';
if (!is_admin_logged_in()) { header('Location: admin_login.php'); exit; }

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['flash'] = "ID perangkat tidak valid.";
    header('Location: admin_dashboard.php'); exit;
}

// Mulai proses aman: gunakan transaction agar konsisten
$conn->begin_transaction();

try {
    // Ambil informasi device dan student terkait
    $stmt = $conn->prepare("SELECT student_id, device_name, device_brand, device_ip FROM devices WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $stmt->close();
        $conn->rollback();
        $_SESSION['flash'] = "Perangkat tidak ditemukan.";
        header('Location: admin_dashboard.php'); exit;
    }
    $stmt->bind_result($student_id, $dname, $dbrand, $dip);
    $stmt->fetch();
    $stmt->close();

    // OPTIONAL: kalau ada referensi dummy_devices, kosongkan flag used untuk dummy yang cocok
    if (!empty($dip) && !empty($dname)) {
        $upd_dummy = $conn->prepare("UPDATE dummy_devices SET used = 0 WHERE device_ip = ? AND device_name = ? LIMIT 1");
        $upd_dummy->bind_param('ss', $dip, $dname);
        $upd_dummy->execute();
        $upd_dummy->close();
    }

    // Hapus semua devices milik mahasiswa (biar bersih)
    $del_devices = $conn->prepare("DELETE FROM devices WHERE student_id = ?");
    $del_devices->bind_param('i', $student_id);
    $del_devices->execute();
    $del_devices->close();

    // Hapus mahasiswa sendiri (hard delete)
    $del_student = $conn->prepare("DELETE FROM students WHERE id = ?");
    $del_student->bind_param('i', $student_id);
    $del_student->execute();
    $del_student->close();

    $conn->commit();

    $_SESSION['flash'] = "Perangkat dan akun mahasiswa (ID: {$student_id}) telah dihapus permanen.";
} catch (Exception $e) {
    $conn->rollback();
    // log error ringan (jangan expose ke user)
    error_log("delete_device error: " . $e->getMessage());
    $_SESSION['flash'] = "Terjadi kesalahan saat menghapus perangkat.";
}

header('Location: admin_dashboard.php');
exit;
?>
