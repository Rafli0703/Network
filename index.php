<?php
require 'config.php';
if (is_student_logged_in()) {
    header('Location: student_dashboard.php'); exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) { $err = "Email & password harus diisi."; }
    else {
        $stmt = $conn->prepare("SELECT id, password, can_login, name FROM students WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) { $err = "Akun tidak ditemukan."; }
        else {
            $stmt->bind_result($id, $hash, $can_login, $name);
            $stmt->fetch();
            if (!password_verify($password, $hash)) { $err = "Password salah."; }
            elseif (!$can_login) { $err = "Akun belum diberi perangkat oleh admin. Tunggu admin menambahkan perangkat."; }
            else {
                // login sukses
                $_SESSION['student_id'] = $id;
                $_SESSION['student_name'] = $name;
                header('Location: student_dashboard.php'); exit;
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login Mahasiswa</title>
<link rel="icon" type="image/x-icon" href="10.24.0.1.png">
<link rel="stylesheet" href="styles.css">
<style>
    body{
        margin: 0;
        padding: 0;
        height: 100vh;
        animation: m 3s infinite;
        display: flex;
        background-image: radial-gradient(circle, #7d2fb9, #7349c5, #6a5cce, #646dd5, #627dd9, #518ee4, #439fec, #3caef1, #05c3f9, #00d7fb, #2deaf8, #5ffbf1);
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
        transform: perspective(2000px) translate3d(0px, -66px, 198px) rotateX(-55deg)
        scale3d(0.86, 0.75, 1) translateY(50px);
        will-change: transform;
        transition: 0.4s ease-in-out transform;
        background: #0b0b0f;
        background-size: 16px 16px;
        background-position: 50%;
        background-image: linear-gradient(90deg, rgba(55, 55, 255, 0.4) 1px,transparent 0),
        linear-gradient(180deg, rgba(55, 55, 255, 0.4) 1px, transparent 0);
        transform: perspective(2000px) translate3d(0, -66px, 198px) rotateX(-55deg)
        scale3d(0.86, 0.75, 1) translateY(50px);
    }
    .form-container:hover{
        transform: scale3d(1, 1, 1);
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
  <h1>Login Mahasiswa</h1>
  <?php if($err) echo "<div class='notice'>$err</div>"; ?>
  <form method="post">
    <label>Email</label>
    <input name="email" type="email" required>
    <label>Password</label>
    <input name="password" type="password" required>
    <button type="submit">Login</button>
  </form>
  <p><a style="text-decoration: none;" href="register.php">Register</a></p>
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
