<?php
require 'config.php';
if (!is_admin_logged_in()) { header('Location: admin_login.php'); exit; }

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_dashboard.php'); exit; }

$err = ''; $success = '';

// proses POST (simpan perubahan)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $device_name = trim($_POST['device_name'] ?? '');
    $device_brand = trim($_POST['device_brand'] ?? '');
    $bandwidth = ($_POST['bandwidth'] !== '' ? intval($_POST['bandwidth']) : NULL);

    if ($device_name === '' || $student_name === '') {
        $err = "Nama user dan nama perangkat wajib diisi.";
    } else {
        // ambil student_id dulu dari devices
        $q = $conn->prepare("SELECT student_id FROM devices WHERE id = ?");
        $q->bind_param('i', $id);
        $q->execute();
        $q->bind_result($student_id);
        if (!$q->fetch()) {
            $q->close();
            $err = "Perangkat tidak ditemukan.";
        } else {
            $q->close();
            // update students.name
            $u1 = $conn->prepare("UPDATE students SET name = ? WHERE id = ?");
            $u1->bind_param('si', $student_name, $student_id);
            $u1->execute();
            $u1->close();

            // update devices
            $u2 = $conn->prepare("UPDATE devices SET device_name = ?, device_brand = ?, bandwidth = ? WHERE id = ?");
            $u2->bind_param('ssii', $device_name, $device_brand, $bandwidth, $id);
            if ($u2->execute()) {
                $success = "Perubahan berhasil disimpan.";
            } else {
                $err = "Gagal menyimpan perubahan: " . $u2->error;
            }
            $u2->close();
        }
    }

    if (!$err) {
        $_SESSION['flash'] = $success;
        header('Location: admin_dashboard.php'); exit;
    }
}

// ambil data device & student untuk form
$stmt = $conn->prepare("SELECT d.id, d.device_name, d.device_brand, d.device_ip, d.bandwidth, d.status, s.id AS sid, s.name AS student_name, s.email AS student_email
                        FROM devices d JOIN students s ON d.student_id = s.id WHERE d.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    $stmt->close();
    $_SESSION['flash'] = "Perangkat tidak ditemukan.";
    header('Location: admin_dashboard.php'); exit;
}
$row = $res->fetch_assoc();
$stmt->close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Perangkat</title>
<link rel="icon" type="image/x-icon" href="10.24.0.1.png">
<link rel="stylesheet" href="styles.css">
<style>
    body{
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', sans-serif;
        transition: background-color 0.5s ease, color 0.5s ease;
        background: linear-gradient(132deg, #000000,#00ff00, #0000ff,#e60073,#ff0000,#ffffff);
        background-size: 400% 400%;
        animation: BackgroundGradient 15s ease infinite;
    }
        @keyframes BackgroundGradient {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
}
    .form-container{
        width: 380px;
        padding: 35px 30px;
        border-radius: 12px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
        position: relative;
        transform: perspective(1500px) rotateY(45deg);
        border-radius: 1rem;
        box-shadow: rgba(0, 0, 0, 0.25) 0px 25px 50px -12px;
        transition: transform 1s ease 0s;
    }
    .form-container:hover{
       transform: perspective(3000px) rotateY(5deg);
    }
    .light-mode{
        background: rgba(255,255,255,0.9);
        color: #222;
      }
      .dark-mode{
        background: rgba(25,25,25,0.95);
        color: #f4f4f4;
      }
      .form-container h1{
        text-align: center;
        margin-bottom: 25px;
        font-size: 1.6rem;
      }
      label{
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        transition: color 0.3s;
      }
      input{
        width: 100%;
        padding: 10px;
        margin-bottom: 18px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 1rem;
        outline: none;
        transition: all 0.3s ease;
      }
      .light-mode input{
        background-color: #fff;
        color: #000;
        border-color: #ccc;
      }
      .dark-mode input{
        background-color: #333;
        color: #fff;
        border-color: #555;
      }
      input:focus{
        border-color: #3f5efb;
        box-shadow: 0 0 6px rgba(63,94,251,0.4);
      }
      button {
        padding: 12px;
        border-radius: 8px;
        background-color: #3f5efb;
        color: #fff;
        border: none;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s;
  }
  .notice {
    text-align: center;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 0.9rem;
  }
  .error {
    background: rgba(255,0,0,0.1);
    color: #b30000;
  }
  .success {
    background: rgba(0,255,0,0.1);
    color: #006b00;
  }
  p {
    text-align: center;
    font-size: 0.9rem;
  }

  a {
    color: #3f5efb;
    text-decoration: none;
    font-weight: 500;
  }
  a:hover {
    text-decoration: underline;
  }

  
  .toggle-btn {
    position: absolute;
    top: 12px;
    right: 15px;
    font-size: 22px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: inherit;
    transition: transform 0.2s;
  }

  .toggle-btn:hover {
    transform: scale(0.9);
  }
</style>
</head>
<body>
<div class="container form-container light-mode" id="formBox">
    <button class="toggle-btn" id="toggleMode" title="Ganti Mode 🌙">🌙</button>
  <h1>Edit Perangkat #<?=htmlspecialchars($row['id'])?></h1>
  <?php if($err) echo "<div class='notice'>$err</div>"; ?>
  <form method="post">
    <label>Nama User</label>
    <input name="student_name" value="<?=htmlspecialchars($row['student_name'])?>" required>
    <label>Email (tidak bisa diubah di sini)</label>
    <input value="<?=htmlspecialchars($row['student_email'])?>" disabled>
    <label>Nama Perangkat</label>
    <input name="device_name" value="<?=htmlspecialchars($row['device_name'])?>" required>
    <label>Merk</label>
    <input name="device_brand" value="<?=htmlspecialchars($row['device_brand'])?>">
    <label>Bandwidth (Mbps) — kosongkan jika offline</label>
    <input name="bandwidth" type="number" min="0" value="<?=htmlspecialchars($row['bandwidth'])?>">
    <div style="margin-top:12px;">
      <button type="submit">Simpan Perubahan</button>
      <a style="text-decoration: none;" href="admin_dashboard.php" class="button">Batal</a>
    </div>
  </form>
</div>
<script>
  const toggle = document.getElementById('toggleMode');
  const form = document.getElementById('formBox');
  let dark = false;

  toggle.addEventListener('click', () => {
    dark = !dark;
    if (dark) {
      form.classList.remove('light-mode');
      form.classList.add('dark-mode');
      toggle.textContent = '☀️';
      toggle.title = 'Ganti ke Light Mode ☀️';
    } else {
      form.classList.remove('dark-mode');
      form.classList.add('light-mode');
      toggle.textContent = '🌙';
      toggle.title = 'Ganti ke Dark Mode 🌙';
    }
  });
</script>
</body>
</html>
