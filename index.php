<?php
session_start();
include 'config/db.php';

// Fetch featured products from DB
$result = mysqli_query($conn, "SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.category_id LIMIT 7");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vastra by Phuyal</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f5f5;
      color: #222;
    }

    /* ── TOPBAR ── */
    .topbar {
      background: #111;
      color: #ccc;
      font-size: 13px;
      text-align: center;
      padding: 7px;
    }

    /* ── NAVBAR ── */
    nav {
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 40px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .logo {
      font-size: 26px;
      font-weight: 800;
      color: #111;
      text-decoration: none;
    }
    .logo span { color: #e94560; }
    .nav-links { display: flex; gap: 28px; list-style: none; }
    .nav-links a {
      text-decoration: none;
      color: #333;
      font-size: 15px;
      font-weight: 500;
      transition: color 0.2s;
    }
    .nav-links a:hover, .nav-links a.active { color: #e94560; }
    .nav-right { display: flex; align-items: center; gap: 18px; }
    .nav-right a {
      text-decoration: none;
      color: #333;
      font-size: 14px;
      font-weight: 500;
    }
    .nav-right a:hover { color: #e94560; }
    .cart-btn {
      background: #e94560;
      color: #fff !important;
      padding: 8px 18px;
      border-radius: 6px;
      font-weight: 600 !important;
    }
    .cart-btn:hover { background: #c73652 !important; color: #fff !important; }

    /* ── HERO ── */
    .hero {
      background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 60%, #16213e 100%);
      color: #fff;
      padding: 80px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-height: 420px;
    }
    .hero-text { max-width: 520px; }
    .hero-text p {
      font-size: 14px;
      letter-spacing: 3px;
      color: #e94560;
      text-transform: uppercase;
      margin-bottom: 14px;
    }
    .hero-text h1 {
      font-size: 52px;
      font-weight: 800;
      line-height: 1.15;
      margin-bottom: 18px;
    }
    .hero-text h1 span { color: #e94560; }
    .hero-text .sub {
      font-size: 16px;
      color: #bbb;
      letter-spacing: 0;
      text-transform: none;
      margin-bottom: 32px;
    }
    .hero-btn {
      background: #e94560;
      color: #fff;
      padding: 14px 36px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 700;
      display: inline-block;
      transition: background 0.2s;
    }
    .hero-btn:hover { background: #c73652; }
    .hero-dots { display: flex; gap: 8px; margin-top: 30px; }
    .hero-dots span {
      width: 10px; height: 10px;
      border-radius: 50%;
      background: #555;
    }
    .hero-dots span.active { background: #e94560; width: 28px; border-radius: 5px; }

    /* ── CATEGORIES ── */
    .categories {
      background: #fff;
      display: flex;
      justify-content: center;
      gap: 0;
      border-bottom: 1px solid #eee;
    }
    .cat-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 22px 50px;
      border-right: 1px solid #eee;
      cursor: pointer;
      text-decoration: none;
      color: #333;
      transition: background 0.15s;
    }
    .cat-item:last-child { border-right: none; }
    .cat-item:hover { background: #fef5f7; }
    .cat-icon {
      width: 44px; height: 44px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px;
    }
    .cat-icon.men    { background: #fde8ec; }
    .cat-icon.women  { background: #e8f4fd; }
    .cat-icon.kids   { background: #e8fde8; }
    .cat-icon.offers { background: #fdf6e8; }
    .cat-item h4 { font-size: 15px; font-weight: 700; }
    .cat-item p  { font-size: 12px; color: #999; }

    /* ── SECTION TITLE ── */
    .section { padding: 50px 40px; }
    .section-title {
      text-align: center;
      margin-bottom: 36px;
    }
    .section-title h2 { font-size: 28px; font-weight: 800; }
    .section-title p  { color: #888; font-size: 14px; margin-top: 6px; }

    /* ── PRODUCT GRID ── */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 24px;
    }
    .product-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .product-img {
      width: 100%;
      height: 220px;
      background: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      position: relative;
    }
    .product-img img {
      width: 100%; height: 100%; object-fit: cover;
    }
    .wish-btn {
      position: absolute;
      top: 10px; right: 10px;
      background: #fff;
      border: none;
      border-radius: 50%;
      width: 32px; height: 32px;
      cursor: pointer;
      font-size: 16px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.12);
      display: flex; align-items: center; justify-content: center;
    }
    .product-info { padding: 14px 16px; }
    .product-info h4 { font-size: 15px; font-weight: 600; margin-bottom: 4px; }
    .product-info .price {
      color: #e94560;
      font-weight: 700;
      font-size: 16px;
      margin-bottom: 12px;
    }
    .add-cart-btn {
      width: 100%;
      background: #111;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s;
      text-decoration: none;
      display: block;
      text-align: center;
    }
    .add-cart-btn:hover { background: #e94560; }

    /* ── FEATURES BAR ── */
    .features {
      background: #fff;
      display: flex;
      justify-content: space-around;
      padding: 30px 40px;
      border-top: 1px solid #eee;
      border-bottom: 1px solid #eee;
      flex-wrap: wrap;
      gap: 20px;
    }
    .feature-item {
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .feature-icon { font-size: 28px; }
    .feature-item h4 { font-size: 15px; font-weight: 700; }
    .feature-item p  { font-size: 12px; color: #888; }

    /* ── FOOTER ── */
    footer {
      background: #111;
      color: #aaa;
      text-align: center;
      padding: 24px;
      font-size: 13px;
      margin-top: 20px;
    }
    footer span { color: #e94560; }
  </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  🚚 Free Delivery on orders over Rs. 2000 &nbsp;|&nbsp; 📞 Help &amp; Support &nbsp;|&nbsp;
  <?php if(isset($_SESSION['user_id'])): ?>
    Welcome, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> &nbsp;|&nbsp;
    <a href="logout.php" style="color:#e94560;">Logout</a>
  <?php else: ?>
    <a href="login.php" style="color:#e94560;">Login</a> /
    <a href="register.php" style="color:#e94560;">Register</a>
  <?php endif; ?>
</div>

<!-- NAVBAR -->
<nav>
  <a href="index.php" class="logo">Vastra<span>.</span></a>
  <ul class="nav-links">
    <li><a href="index.php" class="active">Home</a></li>
    <li><a href="products.php">Shop</a></li>
    <li><a href="products.php?cat=1">Men</a></li>
    <li><a href="products.php?cat=2">Women</a></li>
    <li><a href="products.php?cat=3">Kids</a></li>
    <li><a href="about.php">About Us</a></li>
  </ul>
  <div class="nav-right">
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php">My Account</a>
    <?php else: ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
    <a href="cart.php" class="cart-btn">🛒 Cart
      <?php
        $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
        if($cart_count > 0) echo "($cart_count)";
      ?>
    </a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-text">
    <p>New Collection</p>
    <h1>Discover Your <span>Style</span></h1>
    <p class="sub">Trendy, Comfortable &amp; Affordable Clothing For Every Occasion.</p>
    <a href="products.php" class="hero-btn">Shop Now</a>
    <div class="hero-dots">
      <span class="active"></span>
      <span></span>
      <span></span>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<div class="categories">
  <a href="products.php?cat=1" class="cat-item">
    <div class="cat-icon men">👔</div>
    <div><h4>Men</h4><p>View Collection</p></div>
  </a>
  <a href="products.php?cat=2" class="cat-item">
    <div class="cat-icon women">👗</div>
    <div><h4>Women</h4><p>View Collection</p></div>
  </a>
  <a href="products.php?cat=3" class="cat-item">
    <div class="cat-icon kids">🧒</div>
    <div><h4>Kids</h4><p>View Collection</p></div>
  </a>
  <a href="products.php?cat=4" class="cat-item">
    <div class="cat-icon offers">🏷️</div>
    <div><h4>Offers</h4><p>View Collection</p></div>
  </a>
</div>

<!-- FEATURED PRODUCTS -->
<section class="section">
  <div class="section-title">
    <h2>Featured Products</h2>
    <p>Shop the most loved styles by our customers</p>
  </div>
  <div class="product-grid">
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="product-card">
      <div class="product-img">
      <?php if(!empty($row['image']) && file_exists('images/' . $row['image'])): ?>
  <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
<?php else: ?>
  👕
<?php endif; ?>
        <button class="wish-btn">♡</button>
      </div>
      <div class="product-info">
        <h4><?= htmlspecialchars($row['product_name']) ?></h4>
        <div class="price">Rs. <?= number_format($row['price'], 0) ?></div>
        <a href="cart.php?add=<?= $row['product_id'] ?>" class="add-cart-btn">🛒 Add to Cart</a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- FEATURES BAR -->
<div class="features">
  <div class="feature-item">
    <div class="feature-icon">🚚</div>
    <div><h4>Free Shipping</h4><p>On orders over Rs. 2000</p></div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">↩️</div>
    <div><h4>Easy Returns</h4><p>Within 7 days</p></div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">🔒</div>
    <div><h4>Secure Payment</h4><p>100% secure payments</p></div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">💬</div>
    <div><h4>24/7 Support</h4><p>Dedicated support</p></div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <p>&copy; 2026 <span>Vastra by Phuyal</span>. All rights reserved. | Made with ❤️ in Nepal</p>
</footer>

</body>
</html>