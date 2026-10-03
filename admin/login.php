<?php
session_start();
include '../config/db.php';

if(isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = MD5($_POST['password']);

    $result = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

    if(mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin_id']   = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login - Vastra</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',sans-serif;
      background:linear-gradient(135deg,#0d0d0d,#1a1a2e,#0f3460);
      min-height:100vh; display:flex;
      align-items:center; justify-content:center;
    }
    .login-box {
      background:#fff; border-radius:16px;
      padding:44px; width:380px;
      box-shadow:0 24px 60px rgba(0,0,0,0.4);
    }
    .brand { text-align:center; margin-bottom:30px; }
    .brand h2 { font-size:26px; font-weight:800; color:#111; }
    .brand h2 span { color:#e94560; }
    .brand p { color:#888; font-size:13px; margin-top:5px; }
    .admin-badge {
      background:#fde8ec; color:#e94560;
      font-size:11px; font-weight:700;
      padding:4px 12px; border-radius:20px;
      display:inline-block; margin-top:8px;
    }
    .error-msg {
      background:#fde8ec; color:#c0392b;
      padding:11px 15px; border-radius:8px;
      font-size:13px; margin-bottom:18px;
      border-left:4px solid #e94560;
    }
    .form-group { margin-bottom:18px; }
    .form-group label {
      display:block; font-size:13px;
      font-weight:600; color:#555; margin-bottom:6px;
    }
    .form-group input {
      width:100%; padding:12px 16px;
      border:1.5px solid #e0e0e0; border-radius:9px;
      font-size:15px; outline:none;
      transition:border-color 0.2s;
    }
    .form-group input:focus { border-color:#e94560; }
    .login-btn {
      width:100%; background:#e94560; color:#fff;
      border:none; padding:13px; border-radius:9px;
      font-size:16px; font-weight:700; cursor:pointer;
      transition:background 0.2s;
    }
    .login-btn:hover { background:#c73652; }
    .back-link {
      text-align:center; margin-top:18px;
      font-size:13px;
    }
    .back-link a { color:#888; text-decoration:none; }
    .back-link a:hover { color:#e94560; }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="brand">
      <h2>Vastra<span>.</span> Admin</h2>
      <p>Sign in to manage your store</p>
      <span class="admin-badge">🔐 ADMIN PANEL</span>
    </div>

    <?php if(!empty($error)): ?>
      <div class="error-msg">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter admin username" required
               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required>
      </div>
      <button type="submit" class="login-btn">Login to Admin →</button>
    </form>

    <div class="back-link">
      <a href="../index.php">← Back to Store</a>
    </div>
  </div>
</body>
</html>