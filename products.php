<?php
session_start();
include 'config/db.php';

// Get category filter from URL
$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Build query
$query = "SELECT p.*, c.category_name FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE 1=1";

if($cat_id > 0) {
    $query .= " AND p.category_id = $cat_id";
}
if(!empty($search)) {
    $query .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

$result = mysqli_query($conn, $query);

// Fetch all categories for sidebar
$cat_result = mysqli_query($conn, "SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shop - Vastra by Phuyal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:#f5f5f5; color:#222; }

    /* TOPBAR */
    .topbar {
      background:#111; color:#ccc; font-size:13px;
      text-align:center; padding:7px;
    }

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
    .nav-links a:hover, .nav-links a.active { color:#e94560; }
    .nav-right { display:flex; align-items:center; gap:18px; }
    .nav-right a { text-decoration:none; color:#333; font-size:14px; font-weight:500; }
    .nav-right a:hover { color:#e94560; }
    .cart-btn {
      background:#e94560; color:#fff !important;
      padding:8px 18px; border-radius:6px; font-weight:600 !important;
    }

    /* SEARCH BAR */
    .search-bar {
      background:#fff; padding:16px 40px;
      border-bottom:1px solid #eee;
      display:flex; align-items:center; gap:12px;
    }
    .search-bar form { display:flex; gap:10px; width:100%; max-width:500px; }
    .search-bar input {
      flex:1; padding:10px 16px; border:1px solid #ddd;
      border-radius:8px; font-size:14px; outline:none;
    }
    .search-bar input:focus { border-color:#e94560; }
    .search-bar button {
      background:#e94560; color:#fff; border:none;
      padding:10px 22px; border-radius:8px;
      font-size:14px; font-weight:600; cursor:pointer;
    }
    .search-bar button:hover { background:#c73652; }
    .search-label { color:#888; font-size:14px; }

    /* PAGE TITLE */
    .page-title {
      background:linear-gradient(135deg, #1a1a2e, #0f3460);
      color:#fff; padding:30px 40px;
    }
    .page-title h1 { font-size:28px; font-weight:800; }
    .page-title p  { color:#aaa; font-size:14px; margin-top:5px; }

    /* LAYOUT */
    .shop-layout {
      display:flex; gap:24px;
      padding:30px 40px; max-width:1300px; margin:0 auto;
    }

    /* SIDEBAR */
    .sidebar { width:220px; flex-shrink:0; }
    .sidebar-card {
      background:#fff; border-radius:12px;
      padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06);
      margin-bottom:20px;
    }
    .sidebar-card h3 {
      font-size:15px; font-weight:700;
      margin-bottom:14px; color:#111;
      border-bottom:2px solid #e94560;
      padding-bottom:8px;
    }
    .cat-list { list-style:none; }
    .cat-list li { margin-bottom:8px; }
    .cat-list a {
      text-decoration:none; color:#555; font-size:14px;
      display:flex; justify-content:space-between;
      padding:6px 0; transition:color 0.2s;
    }
    .cat-list a:hover, .cat-list a.active { color:#e94560; font-weight:600; }
    .cat-list a .badge {
      background:#f5f5f5; color:#888;
      font-size:11px; padding:2px 8px; border-radius:20px;
    }
    .cat-list a.active .badge { background:#fde8ec; color:#e94560; }

    /* PRODUCTS AREA */
    .products-area { flex:1; }
    .products-header {
      display:flex; justify-content:space-between;
      align-items:center; margin-bottom:20px;
    }
    .products-header p { color:#888; font-size:14px; }
    .sort-select {
      padding:8px 14px; border:1px solid #ddd;
      border-radius:8px; font-size:13px;
      outline:none; cursor:pointer;
    }

    /* PRODUCT GRID */
    .product-grid {
      display:grid;
      grid-template-columns:repeat(auto-fill, minmax(210px, 1fr));
      gap:22px;
    }
    .product-card {
      background:#fff; border-radius:12px;
      overflow:hidden;
      box-shadow:0 2px 10px rgba(0,0,0,0.07);
      transition:transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
      transform:translateY(-4px);
      box-shadow:0 8px 24px rgba(0,0,0,0.12);
    }
    .product-img {
      width:100%; height:210px;
      background:#f0f0f0;
      display:flex; align-items:center;
      justify-content:center; font-size:48px;
      position:relative; overflow:hidden;
    }
    .product-img img { width:100%; height:100%; object-fit:cover; }
    .wish-btn {
      position:absolute; top:10px; right:10px;
      background:#fff; border:none; border-radius:50%;
      width:32px; height:32px; cursor:pointer;
      font-size:16px; box-shadow:0 2px 6px rgba(0,0,0,0.12);
    }
    .cat-badge {
      position:absolute; top:10px; left:10px;
      background:#e94560; color:#fff;
      font-size:10px; font-weight:700;
      padding:3px 8px; border-radius:20px;
      text-transform:uppercase;
    }
    .product-info { padding:14px 16px; }
    .product-info h4 { font-size:15px; font-weight:600; margin-bottom:4px; }
    .product-info .price {
      color:#e94560; font-weight:700;
      font-size:16px; margin-bottom:12px;
    }
    .add-cart-btn {
      width:100%; background:#111; color:#fff;
      border:none; padding:10px; border-radius:7px;
      font-size:14px; font-weight:600; cursor:pointer;
      text-decoration:none; display:block;
      text-align:center; transition:background 0.2s;
    }
    .add-cart-btn:hover { background:#e94560; }

    /* EMPTY STATE */
    .empty-state {
      text-align:center; padding:60px 20px; color:#888;
    }
    .empty-state .icon { font-size:60px; margin-bottom:16px; }
    .empty-state h3 { font-size:20px; margin-bottom:8px; color:#444; }

    /* FOOTER */
    footer {
      background:#111; color:#aaa;
      text-align:center; padding:24px;
      font-size:13px; margin-top:40px;
    }
    footer span { color:#e94560; }
  </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  🚚 Free Delivery on orders over Rs. 2000 &nbsp;|&nbsp;
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
    <li><a href="index.php">Home</a></li>
    <li><a href="products.php" class="active">Shop</a></li>
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

<!-- PAGE TITLE -->
<div class="page-title">
  <h1>
    <?php
      if(!empty($search)) echo "Search: \"" . htmlspecialchars($search) . "\"";
      elseif($cat_id > 0) {
        $c = mysqli_fetch_assoc(mysqli_query($conn, "SELECT category_name FROM categories WHERE category_id=$cat_id"));
        echo $c['category_name'] . " Collection";
      } else echo "All Products";
    ?>
  </h1>
  <p>Browse our latest collection of clothing</p>
</div>

<!-- SEARCH BAR -->
<div class="search-bar">
  <form method="GET" action="products.php">
    <?php if($cat_id > 0): ?>
      <input type="hidden" name="cat" value="<?= $cat_id ?>">
    <?php endif; ?>
    <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">🔍 Search</button>
  </form>
  <?php if(!empty($search)): ?>
    <span class="search-label">
      Results for "<?= htmlspecialchars($search) ?>" &nbsp;
      <a href="products.php" style="color:#e94560;">Clear</a>
    </span>
  <?php endif; ?>
</div>

<!-- SHOP LAYOUT -->
<div class="shop-layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-card">
      <h3>Categories</h3>
      <ul class="cat-list">
        <li>
          <a href="products.php" class="<?= $cat_id == 0 ? 'active' : '' ?>">
            All Products <span class="badge"><?= mysqli_num_rows(mysqli_query($conn,"SELECT product_id FROM products")) ?></span>
          </a>
        </li>
        <?php
          mysqli_data_seek($cat_result, 0);
          while($cat = mysqli_fetch_assoc($cat_result)):
            $cnt = mysqli_num_rows(mysqli_query($conn,"SELECT product_id FROM products WHERE category_id=".$cat['category_id']));
        ?>
        <li>
          <a href="products.php?cat=<?= $cat['category_id'] ?>"
             class="<?= $cat_id == $cat['category_id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['category_name']) ?>
            <span class="badge"><?= $cnt ?></span>
          </a>
        </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </aside>

  <!-- PRODUCTS -->
  <div class="products-area">
    <div class="products-header">
      <p><?= mysqli_num_rows($result) ?> products found</p>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
    <div class="product-grid">
      <?php while($row = mysqli_fetch_assoc($result)): ?>
      <div class="product-card">
        <div class="product-img">
          <?php if(!empty($row['image']) && file_exists('images/'.$row['image'])): ?>
            <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
          <?php else: ?>
            👕
          <?php endif; ?>
          <span class="cat-badge"><?= htmlspecialchars($row['category_name']) ?></span>
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

    <?php else: ?>
    <div class="empty-state">
      <div class="icon">🔍</div>
      <h3>No products found</h3>
      <p>Try a different search or browse all categories</p>
      <br>
      <a href="products.php" style="color:#e94560; font-weight:600;">View All Products</a>
    </div>
    <?php endif; ?>

  </div>
</div>

<!-- FOOTER -->
<footer>
  <p>&copy; 2026 <span>Vastra by Phuyal</span>. All rights reserved. | Made with ❤️ in Nepal</p>
</footer>

</body>
</html>