<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get order details from session
$order_id     = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$total_amount = isset($_GET['amount'])   ? (float)$_GET['amount'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pay via eSewa - Vastra by Phuyal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',sans-serif;
      background:linear-gradient(135deg,#1a1a2e,#0f3460);
      min-height:100vh; display:flex;
      align-items:center; justify-content:center; padding:20px;
    }
    .payment-box {
      background:#fff; border-radius:16px;
      padding:40px; max-width:440px; width:100%;
      box-shadow:0 20px 60px rgba(0,0,0,0.3);
      text-align:center;
    }
    .esewa-logo {
      background:#60BB46; color:#fff;
      font-size:28px; font-weight:900;
      padding:14px 30px; border-radius:10px;
      display:inline-block; margin-bottom:20px;
      letter-spacing:1px;
    }
    h2 { font-size:22px; font-weight:800; color:#111; margin-bottom:6px; }
    .sub { color:#888; font-size:14px; margin-bottom:28px; }

    .amount-box {
      background:#f0fdf0; border:2px solid #60BB46;
      border-radius:12px; padding:20px; margin-bottom:24px;
    }
    .amount-box p { font-size:13px; color:#555; margin-bottom:6px; }
    .amount-box h3 { font-size:32px; font-weight:800; color:#60BB46; }

    .steps { text-align:left; margin-bottom:28px; }
    .steps h4 { font-size:14px; font-weight:700; color:#333; margin-bottom:14px; }
    .step {
      display:flex; gap:12px; align-items:flex-start;
      margin-bottom:12px;
    }
    .step-num {
      width:26px; height:26px; border-radius:50%;
      background:#60BB46; color:#fff;
      font-size:13px; font-weight:700;
      display:flex; align-items:center; justify-content:center;
      flex-shrink:0;
    }
    .step p { font-size:13px; color:#555; line-height:1.5; }
    .step strong { color:#111; }

    .esewa-number {
      background:#f8f8f8; border:2px dashed #60BB46;
      border-radius:10px; padding:16px; margin-bottom:24px;
    }
    .esewa-number p { font-size:13px; color:#888; margin-bottom:4px; }
    .esewa-number h3 { font-size:24px; font-weight:800; color:#111; letter-spacing:2px; }
    .esewa-number span { font-size:12px; color:#60BB46; font-weight:700; }

    .copy-btn {
      background:#60BB46; color:#fff; border:none;
      padding:8px 20px; border-radius:7px;
      font-size:13px; font-weight:700; cursor:pointer;
      margin-top:8px;
    }
    .copy-btn:hover { background:#4a9a35; }

    .confirm-btn {
      width:100%; background:#e94560; color:#fff;
      border:none; padding:14px; border-radius:9px;
      font-size:16px; font-weight:700; cursor:pointer;
      text-decoration:none; display:block;
      transition:background 0.2s;
    }
    .confirm-btn:hover { background:#c73652; }
    .back-link {
      display:block; margin-top:14px;
      color:#888; font-size:13px; text-decoration:none;
    }
    .back-link:hover { color:#e94560; }

    .note {
      background:#fff8e1; border-left:4px solid #ffc107;
      border-radius:8px; padding:12px 14px;
      font-size:12px; color:#666; text-align:left; margin-bottom:20px;
    }
  </style>
</head>
<body>
<div class="payment-box">

  <div class="esewa-logo">e<span style="color:#fff;">Sewa</span></div>
  <h2>Pay via eSewa</h2>
  <p class="sub">Complete your payment using eSewa digital wallet</p>

  <div class="amount-box">
    <p>Total Amount to Pay</p>
    <h3>Rs. <?= number_format($total_amount, 0) ?></h3>
  </div>

  <div class="esewa-number">
    <p>Send payment to this eSewa number:</p>
    <h3>9746996236 </h3>
    <span>Samyog & uday - Vastra by Phuyal</span><br>
    <button class="copy-btn" onclick="navigator.clipboard.writeText('9746996236'); this.innerText='✅ Copied!'">
      📋 Copy Number
    </button>
  </div>

  <div class="steps">
    <h4>How to Pay:</h4>
    <div class="step">
      <div class="step-num">1</div>
      <p>Open your <strong>eSewa app</strong> on your phone</p>
    </div>
    <div class="step">
      <div class="step-num">2</div>
      <p>Go to <strong>Send Money</strong> and enter the number above</p>
    </div>
    <div class="step">
      <div class="step-num">3</div>
      <p>Enter amount <strong>Rs. <?= number_format($total_amount, 0) ?></strong> and complete payment</p>
    </div>
    <div class="step">
      <div class="step-num">4</div>
      <p>Take a <strong>screenshot</strong> of the payment confirmation</p>
    </div>
    <div class="step">
      <div class="step-num">5</div>
      <p>Click <strong>"I Have Paid"</strong> below to confirm your order</p>
    </div>
  </div>

  <div class="note">
    ⚠️ Your order will be confirmed once we verify your payment. This usually takes 1-2 hours during business hours.
  </div>

  <a href="order_success.php?id=<?= $order_id ?>" class="confirm-btn">
    ✅ I Have Paid — Confirm Order
  </a>
  <a href="cart.php" class="back-link">← Back to Cart</a>

</div>
</body>
</html>