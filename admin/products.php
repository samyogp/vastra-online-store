<?php
session_start();
include '../config/db.php';

$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "SELECT p.*, c.category_name FROM products p
          JOIN categories c ON p.category_id = c.category_id
          WHERE 1=1";
if($cat_id > 0) $query .= " AND p.category_id = $cat_id";
if(!empty($search)) $query .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%')";
$query .= " ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);
$cat_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");

if(isset($_GET['msg']) && $_GET['msg']=='added') $msg = "✅ Product added successfully!";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Products | Vastra by Phuyal</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:#f5f5f5; color:#222; }
    .topbar { background:#111; color:#ccc; font-size:13px; text-align:center; padding:7px; }
    nav { background:#fff; display:flex; align-items:center; justify-content:space-between; padding:14px 40px; box-shadow:0 2px 8px rgba(0,0,0,0.08); position:sticky; top:0; z-index:100; }
    .logo { font-size:26px; font-weight:800; color:#111; text-decoration:none; }
    .logo span { color:#e94560; }
    .nav-links { display:flex; gap:28px; list-style:none; }
    .nav-links a { text-decoration:none; color:#333; font-size:15px; font-weight:500; }
    .nav-links a:hover, .nav-links a.active { color:#e94560; }
    .nav-right { display:flex; align-items:center; gap:18px; }
    .nav-right a { text-decoration:none; color:#333; font-size:14px; font-weight:500; }
    .nav-right a:hover { color:#e94560; }
    .admin-btn { background:#e94560; color:#fff !important; padding:8px 18px; border-radius:6px; font-weight:600 !important; }
    .page-title { background:linear-gradient(135deg, #1a1a2e, #0f3460); color:#fff; padding:30px 40px; display:flex; justify-content:space-between; align-items:center; }
    .page-title h1 { font-size:28px; font-weight:800; }
    .page-title a { background:#e94560; color:#fff; padding:10px 22px; border-radius:8px; text-decoration:none; font-weight:600; }
    .msg { background:#e8f8ee; color:#27ae60; padding:12px 40px; font-weight:600; }
    .shop-layout { display:flex; gap:24px; padding:30px 40px; max-width:1300px; margin:0 auto; }
    .sidebar { width:220px; flex-shrink:0; }
    .sidebar-card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:20px; }
    .sidebar-card h3 { font-size:15px; font-weight:700; margin-bottom:14px; color:#111; border-bottom:2px solid #e94560; padding-bottom:8px; }
    .cat-list { list-style:none; }
    .cat-list li { margin-bottom:8px; }
    .cat-list a { text-decoration:none; color:#555; font-size:14px; display:flex; justify-content:space-between; padding:6px 0; }
    .cat-list a:hover, .cat-list a.active { color:#e94560; font-weight:600; }
    .cat-list a .badge { background:#f5f5f5; color:#888; font-size:11px; padding:2px 8px; border-radius:20px; }
    .products-area { flex:1; }
    .product-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(210px, 1fr)); gap:22px; }
    .product-card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.07); transition:transform 0.2s; }
    .product-card:hover { transform:translateY(-4px); }
    .product-img { width:100%; height:210px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; font-size:48px; position:relative; overflow:hidden; }
    .product-img img { width:100%; height:100%; object-fit:cover; display:block; }
    .cat-badge { position:absolute; top:10px; left:10px; background:#e94560; color:#fff; font-size:10px; font-weight:700; padding:3px 8px; border-radius:20px; text-transform:uppercase; }
    .product-info { padding:14px 16px; }
    .product-info h4 { font-size:15px; font-weight:600; margin-bottom:4px; }
    .product-info .price { color:#e94560; font-weight:700; font-size:16px; margin-bottom:6px; }
    .stock-badge { display:inline-block; font-size:11px; font-weight:600; padding:2px 8px; border-radius:20px; margin-bottom:8px; }
    .stock-in  { background:#e8f8ee; color:#27ae60; }
    .stock-low { background:#fff3e0; color:#e67e22; }
    .stock-out { background:#fde8ec; color:#e94560; }
    .size-row { display:flex; flex-wrap:wrap; gap:4px; margin-bottom:10px; }
    .size-pill { font-size:10px; font-weight:700; padding:2px 7px; border-radius:4px; background:#f5f5f5; color:#555; border:1px solid #ddd; }
    .add-cart-btn { width:100%; background:#111; color:#fff; border:none; padding:10px; border-radius:7px; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; display:block; text-align:center; transition:background 0.2s; }
    .add-cart-btn:hover { background:#e94560; }
    .add-cart-btn.disabled { background:#ccc; cursor:not-allowed; pointer-events:none; }
    .edit-btn { display:block; text-align:center; margin-top:6px; padding:7px; border-radius:7px; background:#0f3460; color:#fff; text-decoration:none; font-size:13px; font-weight:600; }
    .edit-btn:hover { background:#1a1a2e; }
    footer { background:#111; color:#aaa; text-align:center; padding:24px; font-size:13px; margin-top:40px; }
    footer span { color:#e94560; }
  </style>
</head>
<body>

<div class="topbar">⚙️ Admin Panel — Vastra by Phuyal</div>

<nav>
  <a href="../index.php" class="logo">Vastra<span>.</span></a>
  <ul class="nav-links">
    <li><a href="index.php">Dashboard</a></li>
    <li><a href="products.php" class="active">Products</a></li>
    <li><a href="orders.php">Orders</a></li>
    <li><a href="users.php">Users</a></li>
  </ul>
  <div class="nav-right">
    <a href="../index.php">View Site</a>
    <a href="logout.php" class="admin-btn">Logout</a>
  </div>
</nav>

<div class="page-title">
  <div>
    <h1>Products</h1>
    <p style="color:#aaa;font-size:14px;margin-top:5px;">Manage your product listings</p>
  </div>
  <a href="add_product.php">+ Add Product</a>
</div>

<?php if(isset($msg)): ?>
<div class="msg"><?= $msg ?></div>
<?php endif; ?>

<div class="shop-layout">
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
          <a href="products.php?cat=<?= $cat['category_id'] ?>" class="<?= $cat_id == $cat['category_id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['category_name']) ?>
            <span class="badge"><?= $cnt ?></span>
          </a>
        </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </aside>

  <div class="products-area">
    <div style="margin-bottom:16px;">
      <p style="color:#888;font-size:14px;"><?= mysqli_num_rows($result) ?> products found</p>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
    <div class="product-grid">
      <?php while($row = mysqli_fetch_assoc($result)): ?>
      <?php
        $stock = isset($row['stock']) ? (int)$row['stock'] : 99;
        if($stock <= 0)      { $sc = 'stock-out'; $st = 'Out of Stock'; }
        elseif($stock <= 10) { $sc = 'stock-low'; $st = 'Low Stock ('.$stock.')'; }
        else                 { $sc = 'stock-in';  $st = 'In Stock'; }
        $sizes_arr = (!empty($row['sizes'])) ? explode(',', $row['sizes']) : [];
      ?>
      <div class="product-card">
        <div class="product-img">
          <?php if(!empty($row['image']) && file_exists('../images/' . $row['image'])): ?>
            <img src="../images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
          <?php else: ?>
            👕
          <?php endif; ?>
          <span class="cat-badge"><?= htmlspecialchars($row['category_name']) ?></span>
        </div>
        <div class="product-info">
          <h4><?= htmlspecialchars($row['product_name']) ?></h4>
          <div class="price">Rs. <?= number_format($row['price'], 0) ?></div>
          <span class="stock-badge <?= $sc ?>"><?= $st ?></span>
          <?php if(!empty($sizes_arr)): ?>
          <div class="size-row">
            <?php foreach($sizes_arr as $sz): if(trim($sz)=='') continue; ?>
              <span class="size-pill"><?= htmlspecialchars(trim($sz)) ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <?php if($stock > 0): ?>
            <a href="../cart.php?add=<?= $row['product_id'] ?>" class="add-cart-btn">🛒 Add to Cart</a>
          <?php else: ?>
            <span class="add-cart-btn disabled">Out of Stock</span>
          <?php endif; ?>
          <a href="edit_product.php?id=<?= $row['product_id'] ?>" class="edit-btn">✏️ Edit</a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:60px;color:#888;">
      <div style="font-size:60px;">📦</div>
      <h3>No products found</h3>
    </div>
    <?php endif; ?>
  </div>
</div>

<footer>
  <p>&copy; 2026 <span>Vastra by Phuyal</span>. All rights reserved.</p>
</footer>

</body>
</html>