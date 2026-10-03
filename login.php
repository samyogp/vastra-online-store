<?php
session_start();
include 'config/db.php';

// If already logged in, redirect to home
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = MD5($_POST['password']);

    $query  = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['user_name'] = $user['full_name'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Vastra by Phuyal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* NAVBAR */
    nav {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 40px;
    }
    .logo { font-size:24px; font-weight:800; color:#fff; text-decoration:none; }
    .logo span { color:#e94560; }
    .nav-back {
      color:#aaa; text-decoration:none; font-size:14px;
      display:flex; align-items:center; gap:6px;
    }
    .nav-back:hover { color:#fff; }

    /* LOGIN WRAPPER */
    .login-wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .login-box {
      background: #fff;
      border-radius: 16px;
      padding: 40px 44px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .login-box .brand {
      text-align: center;
      margin-bottom: 28px;
    }
    .login-box .brand h2 {
      font-size: 28px;
      font-weight: 800;
      color: #111;
    }
    .login-box .brand h2 span { color: #e94560; }
    .login-box .brand p {
      color: #888;
      font-size: 14px;
      margin-top: 6px;
    }

    /* ERROR */
    .error-msg {
      background: #fde8ec;
      color: #c0392b;
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 14px;
      margin-bottom: 20px;
      border-left: 4px solid #e94560;
    }

    /* FORM */
    .form-group { margin-bottom: 20px; }
    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #555;
      margin-bottom: 7px;
    }
    .form-group input {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid #e0e0e0;
      border-radius: 9px;
      font-size: 15px;
      outline: none;
      transition: border-color 0.2s;
      color: #222;
    }
    .form-group input:focus { border-color: #e94560; }
    .form-group input::placeholder { color: #bbb; }

    .login-btn {
      width: 100%;
      background: #e94560;
      color: #fff;
      border: none;
      padding: 14px;
      border-radius: 9px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.2s;
      margin-top: 6px;
    }
    .login-btn:hover { background: #c73652; }

    .divider {
      text-align: center;
      margin: 22px 0;
      color: #bbb;
      font-size: 13px;
      position: relative;
    }
    .divider::before, .divider::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 42%;
      height: 1px;
      background: #eee;
    }
    .divider::before { left: 0; }
    .divider::after  { right: 0; }

    .register-link {
      text-align: center;
      font-size: 14px;
      color: #666;
    }
    .register-link a {
      color: #e94560;
      font-weight: 700;
      text-decoration: none;
    }
    .register-link a:hover { text-decoration: underline; }

    .home-link {
      text-align: center;
      margin-top: 16px;
    }
    .home-link a {
      color: #888;
      font-size: 13px;
      text-decoration: none;
    }
    .home-link a:hover { color: #e94560; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
  <a href="index.php" class="logo">Vastra<span>.</span></a>
  <a href="index.php" class="nav-back">← Back to Home</a>
</nav>

<!-- LOGIN BOX -->
<div class="login-wrapper">
  <div class="login-box">

    <div class="brand">
      <h2>Welcome to <span>Vastra</span></h2>
      <p>Login to your account to continue shopping</p>
    </div>

    <?php if(!empty($error)): ?>
      <div class="error-msg">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               placeholder="Enter your email"
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
               required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               placeholder="Enter your password"
               required>
      </div>

      <button type="submit" class="login-btn">Login →</button>
    </form>

    <div class="divider">or</div>

    <div class="register-link">
      Don't have an account? <a href="register.php">Register here</a>
    </div>

    <div class="home-link">
      <a href="index.php">🏠 Continue without login</a>
    </div>

  </div>
</div>

</body>
</html>