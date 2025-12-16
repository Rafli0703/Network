<?php
// header.php
// Pastikan config.php sudah di-include di file yang memanggil header.php
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Monitoring Jaringan PNJ</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="top-header">
    <div class="header-left">
      <div class="site-title">Monitoring Jaringan PNJ</div>
    </div>
    <div class="header-right">
      <button id="logoutBtn" class="logout-btn">Logout</button>
    </div>
  </header>

  <!-- Logout confirmation modal -->
  <div id="logoutModal" class="modal" aria-hidden="true">
    <div class="modal-content">
      <h3>Konfirmasi Logout</h3>
      <p>Apakah anda yakin ingin log out?</p>
      <div class="modal-actions">
        <form method="post" action="logout.php" style="display:inline;">
          <button type="submit" class="btn btn-danger">Log Out</button>
        </form>
        <button id="logoutCancel" class="btn btn-secondary">Batal</button>
      </div>
    </div>
  </div>

<script>
  // Modal logic
  const logoutBtn = document.getElementById('logoutBtn');
  const logoutModal = document.getElementById('logoutModal');
  const logoutCancel = document.getElementById('logoutCancel');
  logoutBtn && logoutBtn.addEventListener('click', () => {
    logoutModal.style.display = 'flex';
    logoutModal.setAttribute('aria-hidden', 'false');
  });
  logoutCancel && logoutCancel.addEventListener('click', () => {
    logoutModal.style.display = 'none';
    logoutModal.setAttribute('aria-hidden', 'true');
  });
  // close modal on click outside
  window.addEventListener('click', (e) => {
    if (e.target === logoutModal) {
      logoutModal.style.display = 'none';
      logoutModal.setAttribute('aria-hidden', 'true');
    }
  });
</script>
<!-- Page content must continue after including this header -->
