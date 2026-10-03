<?php
session_start();
include 'config/db.php';

// If already logged in, redirect
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error   = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email     = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone     = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address   = mysqli_real_escape_string($conn, trim($_POST['address']));
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];

    // Validations
    if(empty($full_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0) {
            $error = "This email is already registered. Please login.";
        } else {
            $hashed = MD5($password);
            $insert = "INSERT INTO users (full_name, email, password, phone, address) 
                       VALUES ('$full_name','$email','$hashed','$phone','$address')";
            if(mysqli_query($conn, $insert)) {
                // Auto login after register
                $new_id = mysqli_insert_id($conn);
                $_SESSION['user_id']   = $new_id;
                $_SESSION['user_name'] = $full_name;
                header("Location: index.php");
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Vastra by Phuyal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

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
    .nav-back { color:#aaa; text-decoration:none; font-size:14px; }
    .nav-back:hover { color:#fff; }

    .register-wrapper {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 20px;
    }

    .register-box {
      background: #fff;
      border-radius: 16px;
      padding: 36px 44px;
      width: 100%;
      max-width: 460px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .brand {
      text-align: center;
      margin-bottom: 26px;
    }
    .brand h2 { font-size:26px; font-weight:800; color:#111; }
    .brand h2 span { color:#e94560; }
    .brand p { color:#888; font-size:13px; margin-top:5px; }

    .error-msg {
      background:#fde8ec; color:#c0392b;
      padding:11px 15px; border-radius:8px;
      font-size:13px; margin-bottom:18px;
      border-left:4px solid #e94560;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    .form-group { margin-bottom:16px; }
    .form-group label {
      display:block; font-size:13px;
      font-weight:600; color:#555; margin-bottom:6px;
    }
    .form-group input, .form-group textarea {
      width:100%; padding:11px 15px;
      border:1.5px solid #e0e0e0; border-radius:9px;
      font-size:14px; outline:none;
      transition:border-color 0.2s; color:#222;
      font-family:'Segoe UI', sans-serif;
    }
    .form-group input:focus,
    .form-group textarea:focus { border-color:#e94560; }
    .form-group input::placeholder,
    .form-group textarea::placeholder { color:#bbb; }
    .form-group textarea { resize:none; height:70px; }

    .required { color:#e94560; }

    .register-btn {
      width:100%; background:#e94560; color:#fff;
      border:none; padding:13px; border-radius:9px;
      font-size:16px; font-weight:700; cursor:pointer;
      transition:background 0.2s; margin-top:4px;
    }
    .register-btn:hover { background:#c73652; }

    .login-link {
      text-align:center; font-size:14px;
      color:#666; margin-top:18px;
    }
    .login-link a { color:#e94560; font-weight:700; text-decoration:none; }
    .login-link a:hover { text-decoration:underline; }
  </style>
</head>
<body>

<nav>
  <a href="index.php" class="logo">Vastra<span>.</span></a>
  <a href="index.php" class="nav-back">← Back to Home</a>
</nav>

<div class="register-wrapper">
  <div class="register-box">

    <div class="brand">
      <h2>Join <span>Vastra</span></h2>
      <p>Create your account and start shopping</p>
    </div>

    <?php if(!empty($error)): ?>
      <div class="error-msg">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">

      <div class="form-group">
        <label>Full Name <span class="required">*</span></label>
        <input type="text" name="full_name" placeholder="Your full name"
               value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
      </div>

      <div class="form-group">
        <label>Email Address <span class="required">*</span></label>
        <input type="email" name="email" placeholder="your@email.com"
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
      </div>

      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="98XXXXXXXX"
               value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Password <span class="required">*</span></label>
          <input type="password" name="password" placeholder="Min 6 characters" required>
        </div>
        <div class="form-group">
          <label>Confirm Password <span class="required">*</span></label>
          <input type="password" name="confirm_password" placeholder="Repeat password" required>
        </div>
      </div>

      <div class="form-group">
        <label>Delivery Address</label>
        <textarea name="address" placeholder="Your delivery address (optional)"><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>
      </div>

      <button type="submit" class="register-btn">Create Account →</button>
    </form>

    <div class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </div>

  </div>
</div>

</body>
</html>