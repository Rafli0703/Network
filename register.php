<?php
require 'config.php';

$err = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !$password) {
        $err = "Nama, email, dan password wajib diisi.";
    } else {
        // cek duplicate email
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $err = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO students (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $name, $email, $hash);
            if ($stmt->execute()) {
                $success = "Registrasi berhasil. Tunggu admin untuk approve agar bisa login.";
            } else {
                $err = "Gagal registrasi: " . $stmt->error;
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register - Network Monitor</title>
  <link rel="icon" type="image/x-icon" href="10.24.0.1.png">
  <link rel="stylesheet" href="styles.css">
  <style>
       body{
        margin: 0;
        padding: 0;
        height: 100vh;
        background: radial-gradient(circle,rgba(2, 0, 36, 1) 0%, rgba(9, 9, 121, 1) 35%, rgba(0, 212, 255, 1) 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', sans-serif;
        transition: background-color 0.5s ease, color 0.5s ease;
      }
      .form-container{
        width: 380px;
        padding: 35px 30px;
        border-radius: 12px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
        position: relative;
        transform: scale(0.75) rotateY(-30deg) rotateX(45deg) translateZ(4.5rem);
        transform-origin: 50% 100%;
        transform-style: preserve-3d;
        box-shadow: 1rem 1rem 2rem rgba(0, 0, 0, 0.25);
        transition: 0.6s ease transform;
        background: #fbe806;
      }
      .form-container:hover{
        transform: scale(1);
        transform: translateZ(0);
      }
      .form-container::before{
         transform: translateZ(4rem);
          border: 5px solid #f96b59;
      }
      .form-container::after{
        transform: translateZ(-4rem);
        background: #f96b59;
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
  <h1>Registrasi Mahasiswa</h1>
  <?php if(!empty($err)) echo "<div class='notice'>$err</div>"; ?>
  <?php if(!empty($success)) echo "<div class='notice'>$success</div>"; ?>
  <form method="post">
    <label>Nama</label>
    <input name="name" required>
    <label>Email</label>
    <input name="email" type="email" required>
    <label>Password</label>
    <input name="password" type="password" required>
    <button type="submit">Daftar</button>
  </form>
  <p>Sudah punya akun? <a style="text-decoration: none;" href="student_login.php">Login Mahasiswa</a></p>
  <p>Masuk sebagai admin? <a style="text-decoration: none;" href="admin_login.php">Login Admin</a></p>
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
