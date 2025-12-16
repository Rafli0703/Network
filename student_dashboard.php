<?php
require 'config.php';
if (!is_student_logged_in()) { header('Location: student_login.php'); exit; }

// ambil perangkat yang online (bandwidth > 0)
$devices = [];
$res = $conn->query("SELECT d.device_name, d.device_brand, d.device_ip, d.bandwidth, s.name AS student_name, s.email AS student_email
                     FROM devices d
                     JOIN students s ON d.student_id = s.id
                     WHERE d.bandwidth IS NOT NULL AND d.bandwidth > 0
                     ORDER BY d.id DESC");
if ($res) $devices = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard Mahasiswa</title>
<link rel="icon" type="image/x-icon" href="10.24.0.1.png">
<link rel="stylesheet" href="styles.css">

<style>
  * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}
body {
  background-size: 400% 400%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  color: #333;
  animation: rotate-bg-color 10s infinite;
  background: radial-gradient(circle,rgba(34, 193, 195, 1) 0%, rgba(253, 187, 45, 1) 100%);
}
.table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  border-radius: 8px;
  overflow: hidden;
  margin-top: 15px;
}
.table th {
  background: #3f5efb;
  color: #fff;
  font-weight: 600;
}
.table tr:nth-child(even) {
  background: #f9f9f9;
}
.table tr:hover {
  background: #eef3ff;
  transition: 0.2s ease-in;
}
.container{
  box-shadow: 1rem 1rem 2rem rgba(0, 0, 0, 0.25);
}
.light-mode{
  background: rgba(255,255,255,0.9);
  color: #222;
}
.dark-mode{
  background: rgba(25,25,25,0.95);
  color: #f4f4f4;
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
  /* === Logout Button === */
.sidebar nav ul li.logout {
  margin-top: auto;
  border-top: 1px solid rgba(255,255,255,0.2);
  padding-top: 15px;
}
.sidebar nav ul li.logout a {
  color: #ff6b6b;
  justify-content: center;
  font-weight: 600;
}
.sidebar nav ul li.logout a:hover {
  background: rgba(255, 0, 0, 0.2);
  color: #fff;
}
</style>
</head>
<body>
<div class="container">
  <div class="nav">
    <button class="toggle-btn" id="toggleMode" title="Ganti Mode 🌙">🌙</button>
    Halo, <?=htmlspecialchars($_SESSION['student_name'])?>
    <a style="float: right;" class="navbar-brand" href="logout.php">
      <svg style="width: 1.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M569 337C578.4 327.6 578.4 312.4 569 303.1L425 159C418.1 152.1 407.8 150.1 398.8 153.8C389.8 157.5 384 166.3 384 176L384 256L272 256C245.5 256 224 277.5 224 304L224 336C224 362.5 245.5 384 272 384L384 384L384 464C384 473.7 389.8 482.5 398.8 486.2C407.8 489.9 418.1 487.9 425 481L569 337zM224 160C241.7 160 256 145.7 256 128C256 110.3 241.7 96 224 96L160 96C107 96 64 139 64 192L64 448C64 501 107 544 160 544L224 544C241.7 544 256 529.7 256 512C256 494.3 241.7 480 224 480L160 480C142.3 480 128 465.7 128 448L128 192C128 174.3 142.3 160 160 160L224 160z"/></svg>
      Logout
    </a>
  </div>
  <h1>Dashboard Mahasiswa</h1>
  <p>Menampilkan seluruh perangkat yang <strong>Online</strong> di sistem.</p>

  <?php if(count($devices) === 0): ?>
    <div class="notice">Saat ini tidak ada perangkat yang online.</div>
  <?php else: ?>
    <table class="table">
      <tr><th>#</th>
        <th>Nama User</th>
        <th>Email</th>
        <th>Nama Perangkat</th>
        <th>Merk</th>
        <th>IP</th>
        <th>Bandwidth (Mbps)</th>
      </tr>
      <?php $i=1; foreach($devices as $d): ?>
        <tr>
          <td><?=$i++?></td>
          <td><?=htmlspecialchars($d['student_name'])?></td>
          <td><?=htmlspecialchars($d['student_email'])?></td>
          <td><?=htmlspecialchars($d['device_name'])?></td>
          <td><?=htmlspecialchars($d['device_brand'] ?? '-')?></td>
          <td><?=htmlspecialchars($d['device_ip'] ?? '-')?></td>
          <td><?=htmlspecialchars($d['bandwidth'])?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
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
