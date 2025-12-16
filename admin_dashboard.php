<?php
require 'config.php';
if (!is_admin_logged_in()) { header('Location: admin_login.php'); exit; }

// flash
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// pending students
$pending = [];
$res_p = $conn->query("SELECT id, name, email, created_at FROM students WHERE can_login = 0 ORDER BY created_at ASC");
if ($res_p) $pending = $res_p->fetch_all(MYSQLI_ASSOC);

// devices join students
$devices = [];
$res_d = $conn->query("SELECT d.id as did, d.device_name, d.device_brand, d.device_ip, d.bandwidth, s.id AS sid, s.name AS student_name, s.email AS student_email
                       FROM devices d
                       JOIN students s ON d.student_id = s.id
                       ORDER BY d.id DESC");
if ($res_d) $devices = $res_d->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>
<link rel="icon" type="image/x-icon" href="10.24.0.1.png">
<link rel="stylesheet" href="styles.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
}
@keyframes rotate-bg-color {
  0% {
    background-color: steelblue;
  }
  10% {
    background-color: red;
  }
  20% {
    background-color: orange;
  }
  30% {
    background-color: yellow;
  }
  40% {
    background-color: green;
  }
  50% {
    background-color: blue;
  }
  60% {
    background-color: indigo;
  }
  70% {
    background-color: violet;
  }
  80% {
    background-color: pink;
  }
  90% {
    background-color: brown;
  }
  100% {
    background-color: black;
  }
}
.container-sidebar {
  display: flex;
  min-height: 100vh;
}
.sidebar {
  width: 250px;
  background: #1e1f26;
  color: #fff;
  display: flex;
  flex-direction: column;
  padding: 20px 0;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  box-shadow: 2px 0 10px rgba(0,0,0,0.4);
  z-index: 100;
}
.sidebar h2 {
  text-align: center;
  margin-bottom: 15px;
  font-size: 1.4rem;
  letter-spacing: 1px;
}
.sb-user {
  text-align: center;
  font-size: 0.9rem;
  margin-bottom: 25px;
  color: #bbb;
}
.sidebar nav ul {
  list-style: none;
}
.sidebar nav ul li {
  margin: 10px 0;
}
.sidebar nav ul li a {
  display: flex;
  align-items: center;
  color: #fff;
  text-decoration: none;
  padding: 10px 25px;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}
.sidebar nav ul li a:hover {
  background: rgba(255,255,255,0.1);
  padding-left: 30px;
}
.sidebar nav ul li a.active {
  background: #3f5efb;
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
.main {
  margin-left: 250px;
  padding: 30px;
  flex: 1;
  background: rgba(255,255,255,0.95);
  border-radius: 15px 0 0 15px;
  box-shadow: -2px 0 10px rgba(0,0,0,0.15);
}
.main h1 {
  font-size: 1.8rem;
  margin-bottom: 15px;
  color: #222;
}
.notice {
  background: #e3f2fd;
  padding: 10px 15px;
  border-left: 4px solid #2196F3;
  border-radius: 5px;
  margin-bottom: 15px;
  font-size: 0.95rem;
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
h2 {
  margin-top: 35px;
  color: #333;
  border-left: 4px solid #3f5efb;
  padding-left: 10px;
  margin-bottom: 10px;
}
.form-inline {
  background: #f8f8f8;
  padding: 15px;
  border-radius: 8px;
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
  margin-bottom: 15px;
}

.form-inline input,
.form-inline select {
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.form-inline button {
  background: #3f5efb;
  color: white;
}

.action-buttons {
  display: flex;
  gap: 5px; 
  justify-content: center; 
}


@media (max-width: 576px) {
  .action-buttons {
    flex-direction: column;
    gap: 3px;
  }
}
/* === Responsive Sidebar === */
@media (max-width: 768px) {
  .sidebar {
    width: 200px;
  }

  .main {
    margin-left: 200px;
    padding: 20px;
  }

  .sidebar nav ul li a {
    font-size: 0.85rem;
  }
}

@media (max-width: 576px) {
  .sidebar {
    position: relative;
    width: 100%;
    height: auto;
    flex-direction: row;
    justify-content: space-between;
    padding: 10px 15px;
  }

  .main {
    margin-left: 0;
    border-radius: 0;
  }

  .sidebar nav ul {
    display: flex;
    gap: 15px;
  }

  .sidebar nav ul li.logout {
    margin-top: 0;
    border-top: none;
    padding-top: 0;
  }
}

</style>
</head>
<body onload="alert('HANYA ADMIN YANG BISA MASUK!')">
<div class="container-sidebar">
  <aside class="sidebar">
    <h2>Admin</h2>
    <nav>
      <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
      </ul>
    </nav>
  </aside>
  <main class="main">
    <a style="float: right;" class="navbar-brand" href="logout.php">
      <svg style="width: 1.5rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M569 337C578.4 327.6 578.4 312.4 569 303.1L425 159C418.1 152.1 407.8 150.1 398.8 153.8C389.8 157.5 384 166.3 384 176L384 256L272 256C245.5 256 224 277.5 224 304L224 336C224 362.5 245.5 384 272 384L384 384L384 464C384 473.7 389.8 482.5 398.8 486.2C407.8 489.9 418.1 487.9 425 481L569 337zM224 160C241.7 160 256 145.7 256 128C256 110.3 241.7 96 224 96L160 96C107 96 64 139 64 192L64 448C64 501 107 544 160 544L224 544C241.7 544 256 529.7 256 512C256 494.3 241.7 480 224 480L160 480C142.3 480 128 465.7 128 448L128 192C128 174.3 142.3 160 160 160L224 160z"/></svg>
      Logout
    </a>
    <?php if($flash) echo "<div class='notice'>{$flash}</div>"; ?>

    <section id="pending">
      <h2>Pending Registrations (Menunggu Approve)</h2>
      <?php if(count($pending) === 0): ?>
        <div class="notice">Tidak ada mahasiswa yang menunggu approve.</div>
      <?php else: ?>
        <table class="table">
          <tr><th>#</th><th>Nama</th><th>Email</th><th>Daftar</th><th>Aksi</th></tr>
          <?php foreach($pending as $p): ?>
            <tr>
              <td><?=htmlspecialchars($p['id'])?></td>
              <td><?=htmlspecialchars($p['name'])?></td>
              <td><?=htmlspecialchars($p['email'])?></td>
              <td><?=htmlspecialchars($p['created_at'])?></td>
              <td>
                <form action="approve_device.php" method="post" style="display:inline-block;">
                  <input type="hidden" name="student_id" value="<?=htmlspecialchars($p['id'])?>">
                  <button type="submit">Approve</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </section>

    <section id="devices">
      <h2>Perangkat (Daftar Lengkap)</h2>
      <?php if(count($devices) === 0): ?>
        <div class="notice">Belum ada perangkat yang terdaftar.</div>
      <?php else: ?>
        <table class="table">
          <tr>
            <th>#</th><th>Nama User</th><th>Email</th><th>Nama Perangkat</th><th>Merk</th><th>IP</th><th>Bandwidth (Mbps)</th><th>Status</th><th>Aksi</th>
          </tr>
          <?php foreach($devices as $d): 
            $isOnline = ($d['bandwidth'] !== null && intval($d['bandwidth']) > 0);
          ?>
            <tr>
              <td><?=htmlspecialchars($d['did'])?></td>
              <td><?=htmlspecialchars($d['student_name'])?></td>
              <td><?=htmlspecialchars($d['student_email'])?></td>
              <td><?=htmlspecialchars($d['device_name'])?></td>
              <td><?=htmlspecialchars($d['device_brand'] ?? '-')?></td>
              <td><?=htmlspecialchars($d['device_ip'] ?? '-')?></td>
              <td><?= $isOnline ? htmlspecialchars($d['bandwidth']) : '-' ?></td>
              <td><?= $isOnline ? 'Online' : 'Offline' ?></td>
              <td>
                <div class="action-buttons">
                  <a class="btn btn-success btn-sm me-1" href="edit_device.php?id=<?=urlencode($d['did'])?>">Edit</a>
                  <a class="btn btn-danger btn-sm" href="delete_device.php?id=<?=urlencode($d['did'])?>" onclick="return confirm('Yakin hapus perangkat & akun mahasiswa ini secara permanen?')">Hapus</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </section>

  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
