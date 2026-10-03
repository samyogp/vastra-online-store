<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us - Vastra by Phuyal</title>
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
    .nav-links a:hover, .nav-links a.active { color:#e94560; }
    .nav-right { display:flex; align-items:center; gap:18px; }
    .nav-right a { text-decoration:none; color:#333; font-size:14px; font-weight:500; }
    .cart-btn { background:#e94560; color:#fff !important; padding:8px 18px; border-radius:6px; font-weight:600 !important; }

    /* HERO */
    .hero {
      background:linear-gradient(135deg,#1a1a2e,#0f3460);
      color:#fff; text-align:center; padding:70px 40px;
    }
    .hero h1 { font-size:42px; font-weight:800; margin-bottom:14px; }
    .hero h1 span { color:#e94560; }
    .hero p { font-size:16px; color:#aaa; max-width:560px; margin:0 auto; }

    .section { padding:50px 40px; max-width:1100px; margin:0 auto; }

    /* ABOUT CARDS */
    .about-grid {
      display:grid; grid-template-columns:1fr 1fr;
      gap:30px; margin-bottom:50px;
    }
    .about-text h2 { font-size:28px; font-weight:800; margin-bottom:14px; }
    .about-text h2 span { color:#e94560; }
    .about-text p { color:#555; line-height:1.8; font-size:15px; margin-bottom:12px; }

    .about-stats {
      display:grid; grid-template-columns:1fr 1fr;
      gap:16px;
    }
    .stat-box {
      background:#fff; border-radius:12px; padding:22px;
      text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.07);
      border-top:3px solid #e94560;
    }
    .stat-box h3 { font-size:32px; font-weight:800; color:#e94560; }
    .stat-box p  { font-size:13px; color:#888; margin-top:4px; }

    /* VALUES */
    .section-title { text-align:center; margin-bottom:36px; }
    .section-title h2 { font-size:28px; font-weight:800; }
    .section-title p  { color:#888; font-size:14px; margin-top:6px; }

    .values-grid {
      display:grid; grid-template-columns:repeat(3,1fr); gap:22px;
      margin-bottom:50px;
    }
    .value-card {
      background:#fff; border-radius:12px; padding:28px 22px;
      text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.07);
      transition:transform 0.2s;
    }
    .value-card:hover { transform:translateY(-4px); }
    .value-icon { font-size:40px; margin-bottom:14px; }
    .value-card h4 { font-size:16px; font-weight:700; margin-bottom:8px; }
    .value-card p  { font-size:13px; color:#888; line-height:1.6; }

    /* TEAM */
    .team-grid {
      display:grid; grid-template-columns:repeat(2,1fr);
      gap:22px; max-width:600px; margin:0 auto 50px;
    }
    .team-card {
      background:#fff; border-radius:12px; padding:28px 22px;
      text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.07);
    }
    .team-avatar {
      width:72px; height:72px; border-radius:50%;
      background:#e94560; color:#fff;
      font-size:28px; font-weight:800;
      display:flex; align-items:center;
      justify-content:center; margin:0 auto 14px;
    }
    .team-card h4 { font-size:16px; font-weight:700; }
    .team-card p  { font-size:13px; color:#888; margin-top:4px; }

    /* CTA */
    .cta {
      background:linear-gradient(135deg,#1a1a2e,#0f3460);
      color:#fff; text-align:center; padding:50px 40px;
    }
    .cta h2 { font-size:28px; font-weight:800; margin-bottom:12px; }
    .cta p  { color:#aaa; margin-bottom:24px; }
    .cta-btn {
      background:#e94560; color:#fff;
      padding:14px 36px; border-radius:8px;
      text-decoration:none; font-size:16px; font-weight:700;
    }
    .cta-btn:hover { background:#c73652; }

    footer {
      background:#111; color:#aaa;
      text-align:center; padding:22px;
      font-size:13px;
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
    <li><a href="about.php" class="active">About Us</a></li>
  </ul>
  <div class="nav-right">
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php">My Account</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
    <a href="cart.php" class="cart-btn">🛒 Cart</a>
  </div>
</nav>

<!-- HERO -->
<div class="hero">
  <h1>About <span>Vastra</span></h1>
  <p>We are on a mission to make fashion accessible, affordable, and convenient for every Nepali.</p>
</div>

<div class="section">

  <!-- ABOUT -->
  <div class="about-grid">
    <div class="about-text">
      <h2>Who We <span>Are</span></h2>
      <p>Vastra by Phuyal is a Nepal-based online clothing store designed to bring quality fashion to your doorstep. The name "Vastra" comes from Sanskrit, meaning garment — reflecting our deep connection to Nepali culture and identity.</p>
      <p>We believe every person deserves access to stylish, comfortable, and affordable clothing without having to leave their home. Our platform makes it easy for customers to browse, select, and order clothing with just a few clicks.</p>
      <p>From trendy streetwear to comfortable everyday essentials, we have something for everyone — Men, Women, and Kids.</p>
    </div>
    <div class="about-stats">
      <div class="stat-box">
        <h3>6+</h3>
        <p>Products Available</p>
      </div>
      <div class="stat-box">
        <h3>4</h3>
        <p>Categories</p>
      </div>
      <div class="stat-box">
        <h3>24/7</h3>
        <p>Online Shopping</p>
      </div>
      <div class="stat-box">
        <h3>COD</h3>
        <p>Cash on Delivery</p>
      </div>
    </div>
  </div>

  <!-- VALUES -->
  <div class="section-title">
    <h2>Our Values</h2>
    <p>What drives us every day</p>
  </div>
  <div class="values-grid">
    <div class="value-card">
      <div class="value-icon">🎯</div>
      <h4>Quality First</h4>
      <p>We carefully select every product to ensure our customers receive only the best quality clothing.</p>
    </div>
    <div class="value-card">
      <div class="value-icon">💰</div>
      <h4>Affordable Prices</h4>
      <p>Fashion should not break the bank. We keep our prices fair and competitive for Nepali shoppers.</p>
    </div>
    <div class="value-card">
      <div class="value-icon">🚚</div>
      <h4>Fast Delivery</h4>
      <p>Free delivery on orders above Rs. 2000 with easy returns within 7 days — no questions asked.</p>
    </div>
    <div class="value-card">
      <div class="value-icon">🔒</div>
      <h4>Secure Shopping</h4>
      <p>Your privacy and security matter to us. Shop safely with our secure platform.</p>
    </div>
    <div class="value-card">
      <div class="value-icon">💬</div>
      <h4>Customer Support</h4>
      <p>Our dedicated support team is always ready to help you with any questions or concerns.</p>
    </div>
    <div class="value-card">
      <div class="value-icon">🇳🇵</div>
      <h4>Made for Nepal</h4>
      <p>Built specifically for Nepali customers, understanding local needs and preferences.</p>
    </div>
  </div>

  <!-- TEAM -->
  <div class="section-title">
    <h2>Meet the Team</h2>
    <p>The people behind Vastra by Phuyal</p>
  </div>
  <div class="team-grid">
    <div class="team-card">
      <div class="team-avatar">S</div>
      <h4>Samyog Phuyal</h4>
      <p>Developer & Designer</p>
    </div>
    <div class="team-card">
      <div class="team-avatar">U</div>
      <h4>Uday Poudel</h4>
      <p>Developer & Tester</p>
    </div>
  </div>

</div>

<!-- CTA -->
<div class="cta">
  <h2>Ready to Shop?</h2>
  <p>Browse our latest collection and find your perfect style today.</p>
  <a href="products.php" class="cta-btn">Shop Now →</a>
</div>

<footer>
  <p>&copy; 2026 <span>Vastra by Phuyal</span>. All rights reserved. | Made with ❤️ in Nepal</p>
</footer>

</body>
</html>