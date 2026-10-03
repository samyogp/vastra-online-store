<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE order_id=$order_id AND user_id=".$_SESSION['user_id']));

if(!$order) {
    header("Location: index.php");
    exit();
}

// Fetch order items
$items = mysqli_query($conn, "SELECT oi.*, p.product_name FROM order_items oi 
                               JOIN products p ON oi.product_id = p.product_id 
                               WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order Placed! - Vastra by Phuyal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',sans-serif;
      background:linear-gradient(135deg,#1a1a2e,#0f3460);
      min-height:100vh; display:flex;
      flex-direction:column; align-items:center;
      justify-content:center; padding:30px 20px;
    }
    .success-box {
      background:#fff; border-radius:16px;
      padding:40px; max-width:500px; width:100%;
      text-align:center;
      box-shadow:0 20px 60px rgba(0,0,0,0.3);
    }
    .check-icon { font-size:64px; margin-bottom:16px; }
    h2 { font-size:26px; font-weight:800; color:#111; margin-bottom:8px; }
    .sub { color:#888; font-size:14px; margin-bottom:28px; }
    .order-info {
      background:#f8f8f8; border-radius:10px;
      padding:18px; margin-bottom:24px; text-align:left;
    }
    .order-info p {
      font-size:14px; color:#555;
      margin-bottom:8px;
      display:flex; justify-content:space-between;
    }
    .order-info p strong { color:#111; }
    .items-list {
      text-align:left; margin-bottom:24px;
    }
    .items-list h4 {
      font-size:14px; color:#888;
      margin-bottom:10px; font-weight:600;
    }
    .item-row {
      display:flex; justify-content:space-between;
      font-size:14px; padding:8px 0;
      border-bottom:1px solid #f0f0f0; color:#444;
    }
    .item-row:last-child { border-bottom:none; }
    .total-row {
      display:flex; justify-content:space-between;
      font-size:16px; font-weight:800; color:#111;
      padding-top:12px; margin-top:4px;
    }
    .total-row span:last-child { color:#e94560; }
    .btn-group { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
    .btn {
      padding:12px 28px; border-radius:9px;
      text-decoration:none; font-weight:700;
      font-size:15px; display:inline-block;
    }
    .btn-primary { background:#e94560; color:#fff; }
    .btn-primary:hover { background:#c73652; }
    .btn-outline { background:#fff; color:#111; border:2px solid #111; }
    .btn-outline:hover { background:#111; color:#fff; }
  </style>
</head>
<body>
  <div class="success-box">
    <div class="check-icon">✅</div>
    <h2>Order Placed Successfully!</h2>
    <p class="sub">Thank you <?= htmlspecialchars($_SESSION['user_name']) ?>! Your order is confirmed.</p>

    <div class="order-info">
      <p><span>Order ID:</span> <strong>#<?= $order_id ?></strong></p>
      <p><span>Payment:</span> <strong>Cash on Delivery</strong></p>
      <p><span>Status:</span> <strong style="color:orange">Pending</strong></p>
      <p><span>Address:</span> <strong><?= htmlspecialchars($order['delivery_address']) ?></strong></p>
    </div>

    <div class="items-list">
      <h4>Items Ordered:</h4>
      <?php while($item = mysqli_fetch_assoc($items)): ?>
      <div class="item-row">
        <span><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?></span>
        <span>Rs. <?= number_format($item['unit_price'] * $item['quantity'], 0) ?></span>
      </div>
      <?php endwhile; ?>
      <div class="total-row">
        <span>Total Paid</span>
        <span>Rs. <?= number_format($order['total_amount'], 0) ?></span>
      </div>
    </div>

    <div class="btn-group">
      <a href="index.php" class="btn btn-primary">🏠 Go to Home</a>
      <a href="products.php" class="btn btn-outline">🛍️ Shop More</a>
    </div>
  </div>
</body>
</html>