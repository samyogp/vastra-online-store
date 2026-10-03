<?php
session_start();
include '../config/db.php';
$page_title = 'Manage Users';
include 'header.php';

$users = mysqli_query($conn, "SELECT u.*, 
    (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.user_id) as total_orders,
    (SELECT SUM(total_amount) FROM orders o WHERE o.user_id = u.user_id) as total_spent
    FROM users u ORDER BY u.created_at DESC");
?>

<div class="table-card">
  <div class="table-header">
    <h3>👥 All Registered Users (<?= mysqli_num_rows($users) ?>)</h3>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Orders</th>
        <th>Total Spent</th>
        <th>Joined</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = mysqli_fetch_assoc($users)): ?>
      <tr>
        <td><?= $row['user_id'] ?></td>
        <td>
          <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; border-radius:50%; background:#e94560;
                        display:flex; align-items:center; justify-content:center;
                        color:#fff; font-weight:700; font-size:15px;">
              <?= strtoupper(substr($row['full_name'], 0, 1)) ?>
            </div>
            <strong><?= htmlspecialchars($row['full_name']) ?></strong>
          </div>
        </td>
        <td style="color:#555;"><?= htmlspecialchars($row['email']) ?></td>
        <td style="color:#555;"><?= $row['phone'] ? htmlspecialchars($row['phone']) : '<span style="color:#ccc;">—</span>' ?></td>
        <td style="text-align:center;">
          <span style="background:#e8f4fd; color:#1C7293; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
            <?= $row['total_orders'] ?>
          </span>
        </td>
        <td>
          <strong style="color:<?= $row['total_spent'] > 0 ? '#2D7D46' : '#888' ?>;">
            Rs. <?= number_format($row['total_spent'] ?? 0, 0) ?>
          </strong>
        </td>
        <td style="font-size:13px; color:#888;">
          <?= date('M d, Y', strtotime($row['created_at'])) ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include 'footer.php'; ?>