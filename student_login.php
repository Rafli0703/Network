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
<link rel="stylesheet" href="styles.css">
<style>
    body,
    body:before{
        margin: 0;
        padding: 0;
        height: 100vh;
        background: linear-gradient(90deg,rgba(131, 58, 180, 1) 0%, rgba(253, 29, 29, 1) 50%, rgba(252, 176, 69, 1) 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', sans-serif;
        transition: background-color 0.5s ease, color 0.5s ease;
      }
      body:before{
        content: "";
        position: fixed;
        inset: 0;
        -webkit-mask: 
          linear-gradient(#000 50%,#0000 0) 
          0 calc(.866*var(--s))/100% calc(3.466*var(--s));
        animation-direction: reverse;
      }
      .form-container{
        width: 380px;
        padding: 35px 30px;
        border-radius: 12px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
        position: relative;
        transform: perspective(800px) rotateY(25deg) scale(0.9) rotateX(10deg);
        filter: blur(2px);
        opacity: 0.5;
        transition: 0.6s ease all;
        background: linear-gradient(325deg, #3f5efb, #fc466b);
      }
      .form-container:hover{
        transform: perspective(800px) rotateY(-15deg) translateY(-50px)
        rotateX(10deg) scale(1);
        filter: blur(0);
        opacity: 1;
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
  .submit{
    transform-style: preserve-3d;
    box-shadow: 0px 0px 0 0px #f9f9fb, -1px 0 28px 0 rgba(34, 33, 81, 0.01),
    28px 28px 28px 0 rgba(34, 33, 81, 0.25);
    transition: 0.4s ease-in-out transform, 0.4s ease-in-out box-shadow;
  }
  .submit:hover{
    box-shadow: 1px 1px 0 1px #f9f9fb, -1px 0 28px 0 rgba(34, 33, 81, 0.01),
      54px 54px 28px -10px rgba(34, 33, 81, 0.15);
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
  <p><a href="register.php">Register</a></p>
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
