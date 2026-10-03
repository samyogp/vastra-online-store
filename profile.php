<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid  = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$uid"));

// Update profile
$success = '';
$error   = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name    = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    mysqli_query($conn, "UPDATE users SET full_name='$name', phone='$phone', address='$address' WHERE user_id=$uid");
    $_SESSION['user_name'] = $name;
    $success = "Profile updated successfully!";
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$uid"));
}

// Fetch orders
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$uid ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Profile - Vastra by Phuyal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:#f5f5f5; color:#222; }

    nav {
      background:#fff; display:flex; align-items:center;
      justify-content:space-between; padding:14px 40px;
      box-shadow:0 2px 8px rgba(0,0,0,0.08);
      position:sticky; top:0; z-index:100;
    }
    .logo { font-size:26px; font-weight:800; color:#111; text-decoration:none; }
    .logo span { color:#e94560; }
    .nav-links { display:flex; gap:28px; list-style:none; }
    .nav-links a { text-decoration:none; color:#333; font-size:15px; font-weight:500; }
    .nav-links a:hover { color:#e94560; }
    .nav-right { display:flex; align-items:center; gap:18px; }
    .nav-right a { text-decoration:none; color:#333; font-size:14px; font-weight:500; }
    .cart-btn { background:#e94560; color:#fff !important; padding:8px 18px; border-radius:6px; font-weight:600 !important; }

    .page-title {
      background:linear-gradient(135deg,#1a1a2e,#0f3460);
      color:#fff; padding:28px 40px;
    }
    .page-title h1 { font-size:26px; font-weight:800; }
    .page-title p  { color:#aaa; font-size:13px; margin-top:4px; }

    .profile-layout {
      display:grid; grid-template-columns:320px 1fr;
      gap:24px; padding:30px 40px;
      max-width:1200px; margin:0 auto;
    }

    .card {
      background:#fff; border-radius:12px;
      padding:24px; box-shadow:0 2px 10px rgba(0,0,0,0.07);
      margin-bottom:20px;
    }
    .card h3 {
      font-size:16px; font-weight:700; color:#111;
      border-bottom:2px solid #e94560;
      padding-bottom:10px; margin-bottom:18px;
    }

    /* Avatar */
    .avatar {
      width:80px; height:80px; border-radius:50%;
      background:#e94560; display:flex;
      align-items:center; justify-content:center;
      font-size:32px; font-weight:800; color:#fff;
      margin:0 auto 14px;
    }
    .profile-name { text-align:center; font-size:18px; font-weight:700; }
    .profile-email { text-align:center; color:#888; font-size:13px; margin-top:4px; }
    .profile-meta {
      display:flex; justify-content:center; gap:20px;
      margin-top:16px; padding-top:16px;
      border-top:1px solid #f0f0f0;
    }
    .meta-item { text-align:center; }
    .meta-item strong { display:block; font-size:20px; font-weight:800; color:#e94560; }
    .meta-item span { font-size:12px; color:#888; }

    /* Form */
    .form-group { margin-bottom:16px; }
    .form-group label { display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:6px; }
    .form-group input,
    .form-group textarea {
      width:100%; padding:10px 14px;
      border:1.5px solid #e0e0e0; border-radius:9px;
      font-size:14px; outline:none; color:#222;
      font-family:'Segoe UI',sans-serif;
      transition:border-color 0.2s;
    }
    .form-group input:focus,
    .form-group textarea:focus { border-color:#e94560; }
    .form-group textarea { resize:none; height:70px; }
    .update-btn {
      width:100%; background:#e94560; color:#fff;
      border:none; padding:12px; border-radius:9px;
      font-size:15px; font-weight:700; cursor:pointer;
    }
    .update-btn:hover { background:#c73652; }

    .alert-success {
      background:#d4edda; color:#155724;
      padding:10px 14px; border-radius:8px;
      font-size:13px; margin-bottom:16px;
      border-left:4px solid #28a745;
    }

    /* Orders */
    .order-card {
      border:1px solid #f0f0f0; border-radius:10px;
      padding:16px; margin-bottom:14px;
      transition:box-shadow 0.2s;
    }
    .order-card:hover { box-shadow:0 4px 12px rgba(0,0,0,0.08); }
    .order-header {
      display:flex; justify-content:space-between;
      align-items:center; margin-bottom:12px;
    }
    .order-id { font-size:15px; font-weight:700; color:#111; }
    .order-date { font-size:12px; color:#888; }
    .order-body { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; }
    .order-total { font-size:16px; font-weight:800; color:#e94560; }
    .order-address { font-size:13px; color:#666; max-width:300px; }

    .badge {
      padding:4px 12px; border-radius:20px;
      font-size:11px; font-weight:700; text-transform:uppercase;
    }
    .badge-pending    { background:#fff3cd; color:#856404; }
    .badge-processing { background:#cce5ff; color:#004085; }
    .badge-delivered  { background:#d4edda; color:#155724; }
    .badge-cancelled  { background:#f8d7da; color:#721c24; }

    .empty-orders {
      text-align:center; padding:40px 20px; color:#888;
    }
    .empty-orders .icon { font-size:48px; margin-bottom:12px; }
    .shop-link {
      display:inline-block; margin-top:12px;
      background:#e94560; color:#fff;
      padding:10px 24px; border-radius:8px;
      text-decoration:none; font-weight:700; font-size:14px;
    }

    footer {
      background:#111; color:#aaa;
      text-align:center; padding:22px;
      font-size:13px; margin-top:30px;
    }
    footer span { color:#e94560; }
  </style>
</head>
<body>

<nav>
  <a href="index.php" class="logo">Vastra<span>.</span></a>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="products.php">Shop</a></li>
    <li><a href="products.php?cat=1">Men</a></li>
    <li><a href="products.php?cat=2">Women</a></li>
    <li><a href="products.php?cat=3">Kids</a></li>
  </ul>
  <div class="nav-right">
    <a href="profile.php" style="color:#e94560; font-weight:700;">My Account</a>
    <a href="logout.php">Logout</a>
    <a href="cart.php" class="cart-btn">🛒 Cart
      <?php $cnt = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; if($cnt > 0) echo "($cnt)"; ?>
    </a>
  </div>
</nav>

<div class="page-title">
  <h1>👤 My Account</h1>
  <p>Manage your profile and view your orders</p>
</div>

<div class="profile-layout">

  <!-- LEFT: PROFILE -->
  <div>
    <!-- Avatar card -->
    <div class="card">
      <div class="avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
      <div class="profile-name"><?= htmlspecialchars($user['full_name']) ?></div>
      <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
      <div class="profile-meta">
        <?php
          $order_count = mysqli_num_rows(mysqli_query($conn, "SELECT order_id FROM orders WHERE user_id=$uid"));
          $spent_row   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE user_id=$uid"));
          $total_spent = $spent_row['total'] ?? 0;
        ?>
        <div class="meta-item">
          <strong><?= $order_count ?></strong>
          <span>Orders</span>
        </div>
        <div class="meta-item">
          <strong>Rs.<?= number_format($total_spent, 0) ?></strong>
          <span>Total Spent</span>
        </div>
      </div>
    </div>

    <!-- Edit profile -->
    <div class="card">
      <h3>✏️ Edit Profile</h3>
      <?php if($success): ?>
        <div class="alert-success">✅ <?= $success ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:#f8f8f8; color:#888;">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="98XXXXXXXX">
        </div>
        <div class="form-group">
          <label>Default Delivery Address</label>
          <textarea name="address" placeholder="Your address..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>
        <button type="submit" name="update_profile" class="update-btn">💾 Update Profile</button>
      </form>
    </div>
  </div>

  <!-- RIGHT: ORDER HISTORY -->
  <div class="card">
    <h3>📦 My Orders (<?= $order_count ?>)</h3>

    <?php if($order_count == 0): ?>
      <div class="empty-orders">
        <div class="icon">📦</div>
        <p>You haven't placed any orders yet.</p>
        <a href="products.php" class="shop-link">Start Shopping</a>
      </div>
    <?php else: ?>
      <?php mysqli_data_seek($orders, 0); while($order = mysqli_fetch_assoc($orders)):
        $items = mysqli_query($conn, "SELECT oi.*, p.product_name FROM order_items oi 
                                      JOIN products p ON oi.product_id = p.product_id 
                                      WHERE oi.order_id=".$order['order_id']);
      ?>
      <div class="order-card">
        <div class="order-header">
          <span class="order-id">#<?= $order['order_id'] ?></span>
          <span class="badge badge-<?= strtolower($order['status']) ?>"><?= $order['status'] ?></span>
        </div>
        <!-- Items list -->
        <?php while($item = mysqli_fetch_assoc($items)): ?>
          <div style="font-size:13px; color:#555; margin-bottom:4px;">
            • <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?>
            <span style="color:#e94560; font-weight:600;">Rs. <?= number_format($item['unit_price'] * $item['quantity'], 0) ?></span>
          </div>
        <?php endwhile; ?>
        <div class="order-body" style="margin-top:10px;">
          <div>
            <div class="order-total">Total: Rs. <?= number_format($order['total_amount'], 0) ?></div>
            <div class="order-address">📍 <?= htmlspecialchars(substr($order['delivery_address'], 0, 60)) ?>...</div>
          </div>
          <div class="order-date"><?= date('M d, Y  h:i A', strtotime($order['order_date'])) ?></div>
        </div>
      </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

</div>

<footer>
  <p>&copy; 2026 <span>Vastra by Phuyal</span>. All rights reserved. | Made with ❤️ in Nepal</p>
</footer>

</body>
</html>