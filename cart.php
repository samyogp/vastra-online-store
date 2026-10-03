<?php
session_start();
include 'config/db.php';

// ── ADD TO CART ──────────────────────────────────────────────────
if(isset($_GET['add'])) {
    $pid = (int)$_GET['add'];
    if(!isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] = 1;
    } else {
        $_SESSION['cart'][$pid]++;
    }
    header("Location: cart.php");
    exit();
}

// ── REMOVE FROM CART ─────────────────────────────────────────────
if(isset($_GET['remove'])) {
    $pid = (int)$_GET['remove'];
    unset($_SESSION['cart'][$pid]);
    header("Location: cart.php");
    exit();
}

// ── UPDATE QUANTITY ──────────────────────────────────────────────
if(isset($_POST['update_cart'])) {
    foreach($_POST['qty'] as $pid => $qty) {
        $qty = (int)$qty;
        if($qty > 0) {
            $_SESSION['cart'][(int)$pid] = $qty;
        } else {
            unset($_SESSION['cart'][(int)$pid]);
        }
    }
    header("Location: cart.php");
    exit();
}

// ── PLACE ORDER ──────────────────────────────────────────────────
if(isset($_POST['place_order'])) {
    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    if(empty($_SESSION['cart'])) {
        header("Location: cart.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $address = mysqli_real_escape_string($conn, trim($_POST['delivery_address']));

    // Calculate total
    $total = 0;
    foreach($_SESSION['cart'] as $pid => $qty) {
        $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE product_id=$pid"));
        if($p) $total += $p['price'] * $qty;
    }

    // Insert order
    mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, delivery_address) 
                         VALUES ($user_id, $total, '$address')");
    $order_id = mysqli_insert_id($conn);

    // Insert order items
    foreach($_SESSION['cart'] as $pid => $qty) {
        $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM products WHERE product_id=$pid"));
        if($p) {
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, unit_price) 
                                 VALUES ($order_id, $pid, $qty, {$p['price']})");
        }
    }

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect based on payment method
    $payment = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';
    if($payment == 'esewa') {
        header("Location: esewa_payment.php?order_id=$order_id&amount=$total");
    } else {
        header("Location: order_success.php?id=$order_id");
    }
    exit();
}

// ── FETCH CART ITEMS ─────────────────────────────────────────────
$cart_items = [];
$grand_total = 0;

if(!empty($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $pid => $qty) {
        $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE product_id=$pid"));
        if($r) {
            $r['cart_qty']  = $qty;
            $r['subtotal']  = $r['price'] * $qty;
            $grand_total   += $r['subtotal'];
            $cart_items[]   = $r;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cart - Vastra by Phuyal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:#f5f5f5; color:#222; }

    /* NAVBAR */
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
    .cart-btn {
      background:#e94560; color:#fff !important;
      padding:8px 18px; border-radius:6px; font-weight:600 !important;
    }

    /* PAGE TITLE */
    .page-title {
      background:linear-gradient(135deg,#1a1a2e,#0f3460);
      color:#fff; padding:28px 40px;
    }
    .page-title h1 { font-size:26px; font-weight:800; }
    .page-title p  { color:#aaa; font-size:13px; margin-top:4px; }

    /* LAYOUT */
    .cart-layout {
      display:flex; gap:24px;
      padding:30px 40px; max-width:1200px; margin:0 auto;
      align-items:flex-start;
    }

    /* CART TABLE */
    .cart-main { flex:1; }
    .cart-table {
      background:#fff; border-radius:12px;
      overflow:hidden;
      box-shadow:0 2px 10px rgba(0,0,0,0.07);
      margin-bottom:16px;
    }
    table { width:100%; border-collapse:collapse; }
    thead { background:#f8f8f8; }
    th {
      padding:14px 18px; text-align:left;
      font-size:13px; font-weight:700;
      color:#555; border-bottom:1px solid #eee;
    }
    td {
      padding:16px 18px; border-bottom:1px solid #f0f0f0;
      vertical-align:middle;
    }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#fafafa; }

    .product-cell { display:flex; align-items:center; gap:14px; }
    .product-thumb {
      width:60px; height:60px; border-radius:8px;
      background:#f0f0f0; display:flex;
      align-items:center; justify-content:center;
      font-size:24px; overflow:hidden; flex-shrink:0;
    }
    .product-thumb img { width:100%; height:100%; object-fit:cover; }
    .product-cell h4 { font-size:14px; font-weight:600; }
    .product-cell p  { font-size:12px; color:#888; margin-top:2px; }

    .price-cell { color:#e94560; font-weight:700; font-size:15px; }
    .subtotal-cell { font-weight:700; font-size:15px; color:#111; }

    /* QTY INPUT */
    .qty-input {
      width:60px; padding:7px 10px;
      border:1.5px solid #ddd; border-radius:7px;
      font-size:14px; text-align:center; outline:none;
    }
    .qty-input:focus { border-color:#e94560; }

    /* REMOVE BTN */
    .remove-btn {
      background:#fde8ec; color:#e94560;
      border:none; padding:7px 14px;
      border-radius:7px; font-size:13px;
      font-weight:600; cursor:pointer;
      text-decoration:none; display:inline-block;
    }
    .remove-btn:hover { background:#e94560; color:#fff; }

    /* UPDATE BTN */
    .update-btn {
      background:#111; color:#fff;
      border:none; padding:10px 22px;
      border-radius:8px; font-size:14px;
      font-weight:600; cursor:pointer;
    }
    .update-btn:hover { background:#333; }

    .continue-link {
      display:inline-block; margin-top:10px;
      color:#e94560; font-size:14px;
      font-weight:600; text-decoration:none;
    }
    .continue-link:hover { text-decoration:underline; }

    /* ORDER SUMMARY */
    .order-summary {
      width:320px; flex-shrink:0;
    }
    .summary-card {
      background:#fff; border-radius:12px;
      padding:24px;
      box-shadow:0 2px 10px rgba(0,0,0,0.07);
    }
    .summary-card h3 {
      font-size:17px; font-weight:800;
      margin-bottom:20px; color:#111;
      border-bottom:2px solid #e94560;
      padding-bottom:10px;
    }
    .summary-row {
      display:flex; justify-content:space-between;
      margin-bottom:12px; font-size:14px; color:#555;
    }
    .summary-row.total {
      font-size:17px; font-weight:800;
      color:#111; border-top:1px solid #eee;
      padding-top:14px; margin-top:8px;
    }
    .summary-row.total span:last-child { color:#e94560; }

    /* CHECKOUT FORM */
    .checkout-form { margin-top:18px; }
    .checkout-form label {
      display:block; font-size:13px;
      font-weight:600; color:#555; margin-bottom:6px;
    }
    .checkout-form textarea {
      width:100%; padding:10px 14px;
      border:1.5px solid #e0e0e0; border-radius:9px;
      font-size:13px; outline:none; resize:none;
      height:80px; font-family:'Segoe UI',sans-serif;
      color:#222;
    }
    .checkout-form textarea:focus { border-color:#e94560; }

    .checkout-btn {
      width:100%; background:#e94560; color:#fff;
      border:none; padding:14px; border-radius:9px;
      font-size:16px; font-weight:700; cursor:pointer;
      margin-top:14px; transition:background 0.2s;
    }
    .checkout-btn:hover { background:#c73652; }

    .login-note {
      background:#fef5f7; border:1px solid #fde8ec;
      border-radius:9px; padding:14px;
      text-align:center; font-size:13px; color:#666;
      margin-top:14px;
    }
    .login-note a { color:#e94560; font-weight:700; text-decoration:none; }

    /* EMPTY CART */
    .empty-cart {
      background:#fff; border-radius:12px;
      padding:60px 20px; text-align:center;
      box-shadow:0 2px 10px rgba(0,0,0,0.07);
    }
    .empty-cart .icon { font-size:64px; margin-bottom:16px; }
    .empty-cart h3 { font-size:22px; font-weight:700; margin-bottom:8px; }
    .empty-cart p  { color:#888; margin-bottom:24px; }
    .shop-btn {
      background:#e94560; color:#fff;
      padding:12px 32px; border-radius:8px;
      text-decoration:none; font-weight:700; font-size:15px;
    }
    .shop-btn:hover { background:#c73652; }

    /* FOOTER */
    footer {
      background:#111; color:#aaa;
      text-align:center; padding:22px;
      font-size:13px; margin-top:40px;
    }
    footer span { color:#e94560; }
  </style>
</head>
<body>

<!-- NAVBAR -->
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
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php">My Account</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
    <a href="cart.php" class="cart-btn">🛒 Cart
      <?php
        $cnt = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
        if($cnt > 0) echo "($cnt)";
      ?>
    </a>
  </div>
</nav>

<!-- PAGE TITLE -->
<div class="page-title">
  <h1>🛒 My Cart</h1>
  <p><?= count($cart_items) ?> item(s) in your cart</p>
</div>

<!-- CART LAYOUT -->
<div class="cart-layout">

  <!-- LEFT: CART ITEMS -->
  <div class="cart-main">
    <?php if(empty($cart_items)): ?>
      <div class="empty-cart">
        <div class="icon">🛒</div>
        <h3>Your cart is empty!</h3>
        <p>Looks like you haven't added anything yet.</p>
        <a href="products.php" class="shop-btn">Start Shopping</a>
      </div>

    <?php else: ?>
      <form method="POST" action="cart.php">
        <div class="cart-table">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($cart_items as $item): ?>
              <tr>
                <td>
                  <div class="product-cell">
                    <div class="product-thumb">
                      <?php if(!empty($item['image']) && file_exists('images/'.$item['image'])): ?>
                        <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="">
                      <?php else: ?>
                        👕
                      <?php endif; ?>
                    </div>
                    <div>
                      <h4><?= htmlspecialchars($item['product_name']) ?></h4>
                      <p>In Stock: <?= $item['stock_qty'] ?></p>
                    </div>
                  </div>
                </td>
                <td class="price-cell">Rs. <?= number_format($item['price'], 0) ?></td>
                <td>
                  <input type="number" name="qty[<?= $item['product_id'] ?>]"
                         value="<?= $item['cart_qty'] ?>"
                         min="0" max="<?= $item['stock_qty'] ?>"
                         class="qty-input">
                </td>
                <td class="subtotal-cell">Rs. <?= number_format($item['subtotal'], 0) ?></td>
                <td>
                  <a href="cart.php?remove=<?= $item['product_id'] ?>" class="remove-btn">Remove</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <button type="submit" name="update_cart" class="update-btn">🔄 Update Cart</button>
      </form>
      <a href="products.php" class="continue-link">← Continue Shopping</a>
    <?php endif; ?>
  </div>

  <!-- RIGHT: ORDER SUMMARY -->
  <?php if(!empty($cart_items)): ?>
  <div class="order-summary">
    <div class="summary-card">
      <h3>Order Summary</h3>
      <div class="summary-row">
        <span>Subtotal</span>
        <span>Rs. <?= number_format($grand_total, 0) ?></span>
      </div>
      <div class="summary-row">
        <span>Shipping</span>
        <span><?= $grand_total >= 2000 ? '<span style="color:green">Free</span>' : 'Rs. 100' ?></span>
      </div>
      <div class="summary-row total">
        <span>Total</span>
        <span>Rs. <?= number_format($grand_total >= 2000 ? $grand_total : $grand_total + 100, 0) ?></span>
      </div>

      <?php if(isset($_SESSION['user_id'])): ?>
        <form method="POST" action="cart.php" class="checkout-form">
          <label>Delivery Address <span style="color:#e94560">*</span></label>
          <textarea name="delivery_address" placeholder="Enter your delivery address..." required></textarea>

          <!-- Payment Method -->
          <label style="margin-top:14px; display:block;">Payment Method <span style="color:#e94560">*</span></label>
          <div style="display:flex; gap:10px; margin-top:8px; margin-bottom:14px;">
            <label style="flex:1; border:2px solid #ddd; border-radius:9px; padding:10px 12px;
                          cursor:pointer; display:flex; align-items:center; gap:8px; font-size:14px;
                          font-weight:600;" id="cod-label">
              <input type="radio" name="payment_method" value="cod" checked
                     onchange="document.getElementById('cod-label').style.borderColor='#e94560';
                               document.getElementById('esewa-label').style.borderColor='#ddd';">
              💵 Cash on Delivery
            </label>
            <label style="flex:1; border:2px solid #ddd; border-radius:9px; padding:10px 12px;
                          cursor:pointer; display:flex; align-items:center; gap:8px; font-size:14px;
                          font-weight:600;" id="esewa-label">
              <input type="radio" name="payment_method" value="esewa"
                     onchange="document.getElementById('esewa-label').style.borderColor='#60BB46';
                               document.getElementById('cod-label').style.borderColor='#ddd';">
              <span style="color:#60BB46;">e</span>Sewa
            </label>
          </div>

          <button type="submit" name="place_order" class="checkout-btn">
            ✅ Proceed to Order
          </button>
        </form>
      <?php else: ?>
        <div class="login-note">
          Please <a href="login.php">login</a> or
          <a href="register.php">register</a> to place your order.
        </div>
      <?php endif; ?>

    </div>
  </div>
  <?php endif; ?>

</div>

<!-- FOOTER -->
<footer>
  <p>&copy; 2026 <span>Vastra by Phuyal</span>. All rights reserved. | Made with ❤️ in Nepal</p>
</footer>

</body>
</html>