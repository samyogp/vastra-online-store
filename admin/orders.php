<?php
session_start();
include '../config/db.php';
$page_title = 'Manage Orders';

// DELETE order
if(isset($_GET['delete'])) {
    $oid = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id=$oid");
    mysqli_query($conn, "DELETE FROM orders WHERE order_id=$oid");
    header("Location: orders.php?msg=deleted");
    exit();
}

// Update order status
if(isset($_POST['update_status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE order_id=$oid");
    header("Location: orders.php?msg=updated");
    exit();
}

$msg = '';
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 'updated') $msg = 'success|Order status updated successfully!';
    if($_GET['msg'] == 'deleted') $msg = 'error|Order deleted successfully!';
}

$orders = mysqli_query($conn, "SELECT o.*, u.full_name, u.email, u.phone 
                                FROM orders o 
                                JOIN users u ON o.user_id = u.user_id 
                                ORDER BY o.order_date DESC");

include 'header.php';
?>

<?php if($msg): $parts = explode('|', $msg); ?>
  <div class="alert-<?= $parts[0] ?>"><?= $parts[1] ?></div>
<?php endif; ?>

<div class="table-card">
  <div class="table-header">
    <h3>📦 All Orders (<?= mysqli_num_rows($orders) ?>)</h3>
  </div>
  <table>
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Items</th>
        <th>Total</th>
        <th>Address</th>
        <th>Date</th>
        <th>Status</th>
        <th>Update Status</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = mysqli_fetch_assoc($orders)):
        $items_count = mysqli_num_rows(mysqli_query($conn, "SELECT item_id FROM order_items WHERE order_id=".$row['order_id']));
      ?>
      <tr>
        <td><strong>#<?= $row['order_id'] ?></strong></td>
        <td>
          <strong><?= htmlspecialchars($row['full_name']) ?></strong>
          <div style="font-size:12px; color:#888;"><?= htmlspecialchars($row['email']) ?></div>
          <?php if($row['phone']): ?>
            <div style="font-size:12px; color:#888;"><?= htmlspecialchars($row['phone']) ?></div>
          <?php endif; ?>
        </td>
        <td style="text-align:center;">
          <span style="background:#e8f4fd; color:#1C7293; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:700;">
            <?= $items_count ?> item<?= $items_count > 1 ? 's' : '' ?>
          </span>
        </td>
        <td><strong style="color:#e94560;">Rs. <?= number_format($row['total_amount'], 0) ?></strong></td>
        <td style="max-width:140px; font-size:13px; color:#666;">
          <?= htmlspecialchars(substr($row['delivery_address'], 0, 40)) ?>...
        </td>
        <td style="font-size:13px; color:#888;">
          <?= date('M d, Y', strtotime($row['order_date'])) ?><br>
          <span style="font-size:11px;"><?= date('h:i A', strtotime($row['order_date'])) ?></span>
        </td>
        <td>
          <span class="badge badge-<?= strtolower($row['status']) ?>">
            <?= $row['status'] ?>
          </span>
        </td>
        <td>
          <form method="POST" style="display:flex; gap:6px; align-items:center;">
            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
            <select name="status" style="padding:6px 8px; border:1.5px solid #ddd; border-radius:7px; font-size:12px; outline:none;">
              <option value="Pending"    <?= $row['status']=='Pending'    ?'selected':'' ?>>Pending</option>
              <option value="Processing" <?= $row['status']=='Processing' ?'selected':'' ?>>Processing</option>
              <option value="Delivered"  <?= $row['status']=='Delivered'  ?'selected':'' ?>>Delivered</option>
              <option value="Cancelled"  <?= $row['status']=='Cancelled'  ?'selected':'' ?>>Cancelled</option>
            </select>
            <button type="submit" name="update_status" class="btn-edit" style="padding:6px 10px;">✅</button>
          </form>
        </td>
        <td>
          <a href="orders.php?delete=<?= $row['order_id'] ?>"
             class="btn-delete"
             onclick="return confirm('Delete order #<?= $row['order_id'] ?>? This cannot be undone!')">
            🗑️ Delete
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include 'footer.php'; ?>
