<?php
session_start();
include '../config/db.php';
$page_title = 'Dashboard';
include 'header.php';

// Stats
$total_products = mysqli_num_rows(mysqli_query($conn, "SELECT product_id FROM products"));
$total_orders   = mysqli_num_rows(mysqli_query($conn, "SELECT order_id FROM orders"));
$total_users    = mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM users"));
$revenue_row    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status != 'Cancelled'"));
$total_revenue  = $revenue_row['total'] ?? 0;

// Recent orders
$recent_orders = mysqli_query($conn, "SELECT o.*, u.full_name FROM orders o 
                                       JOIN users u ON o.user_id = u.user_id 
                                       ORDER BY o.order_date DESC LIMIT 5");

// Recent products
$recent_products = mysqli_query($conn, "SELECT p.*, c.category_name FROM products p 
                                         JOIN categories c ON p.category_id = c.category_id 
                                         ORDER BY p.created_at DESC LIMIT 5");
?>

<!-- STAT CARDS -->
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon">👕</div>
    <div class="stat-info">
      <h3><?= $total_products ?></h3>
      <p>Total Products</p>
    </div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon">📦</div>
    <div class="stat-info">
      <h3><?= $total_orders ?></h3>
      <p>Total Orders</p>
    </div>
  </div>
  <div class="stat-card green">
    <div class="stat-icon">👥</div>
    <div class="stat-info">
      <h3><?= $total_users ?></h3>
      <p>Registered Users</p>
    </div>
  </div>
  <div class="stat-card gold">
    <div class="stat-icon">💰</div>
    <div class="stat-info">
      <h3>Rs. <?= number_format($total_revenue, 0) ?></h3>
      <p>Total Revenue</p>
    </div>
  </div>
</div>

<!-- TWO COLUMN LAYOUT -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

  <!-- RECENT ORDERS -->
  <div class="table-card">
    <div class="table-header">
      <h3>📦 Recent Orders</h3>
      <a href="orders.php" style="color:#e94560; font-size:13px; text-decoration:none; font-weight:600;">View All →</a>
    </div>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($recent_orders)): ?>
        <tr>
          <td><strong>#<?= $row['order_id'] ?></strong></td>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td>Rs. <?= number_format($row['total_amount'], 0) ?></td>
          <td>
            <span class="badge badge-<?= strtolower($row['status']) ?>">
              <?= $row['status'] ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- RECENT PRODUCTS -->
  <div class="table-card">
    <div class="table-header">
      <h3>👕 Recent Products</h3>
      <a href="add_product.php" class="btn-add">+ Add Product</a>
    </div>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($recent_products)): ?>
        <tr>
          <td>
            <div style="display:flex; align-items:center; gap:10px;">
              <div class="prod-thumb">
                <?php if(!empty($row['image']) && file_exists('../images/'.$row['image'])): ?>
                  <img src="../images/<?= htmlspecialchars($row['image']) ?>" alt="">
                <?php else: ?>👕<?php endif; ?>
              </div>
              <?= htmlspecialchars($row['product_name']) ?>
            </div>
          </td>
          <td><?= htmlspecialchars($row['category_name']) ?></td>
          <td>Rs. <?= number_format($row['price'], 0) ?></td>
          <td>
            <span style="color:<?= $row['stock_qty'] < 10 ? '#e94560' : '#2D7D46' ?>; font-weight:600;">
              <?= $row['stock_qty'] ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</div>

<?php include 'footer.php'; ?>