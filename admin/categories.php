<?php
session_start();
include '../config/db.php';

// All redirects/logic BEFORE header include
$error   = '';
$success = '';

// DELETE category
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Check if category has products
    $check = mysqli_num_rows(mysqli_query($conn, "SELECT product_id FROM products WHERE category_id=$id"));
    if($check > 0) {
        $error = "Cannot delete! This category has $check product(s). Remove those products first.";
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE category_id=$id");
        header("Location: categories.php?msg=deleted");
        exit();
    }
}

// ADD category
if(isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['category_name']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['description']));
    if(empty($name)) {
        $error = "Category name is required.";
    } else {
        $check = mysqli_num_rows(mysqli_query($conn, "SELECT category_id FROM categories WHERE category_name='$name'"));
        if($check > 0) {
            $error = "Category '$name' already exists.";
        } else {
            mysqli_query($conn, "INSERT INTO categories (category_name, description) VALUES ('$name','$desc')");
            header("Location: categories.php?msg=added");
            exit();
        }
    }
}

// EDIT category
if(isset($_POST['edit_category'])) {
    $id   = (int)$_POST['category_id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['category_name']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['description']));
    if(empty($name)) {
        $error = "Category name is required.";
    } else {
        mysqli_query($conn, "UPDATE categories SET category_name='$name', description='$desc' WHERE category_id=$id");
        header("Location: categories.php?msg=updated");
        exit();
    }
}

// Message
$msg = '';
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 'added')   $msg = 'success|Category added successfully!';
    if($_GET['msg'] == 'updated') $msg = 'success|Category updated successfully!';
    if($_GET['msg'] == 'deleted') $msg = 'error|Category deleted successfully!';
}

// Fetch edit category if set
$edit_cat = null;
if(isset($_GET['edit'])) {
    $edit_cat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM categories WHERE category_id=".(int)$_GET['edit']));
}

$page_title = 'Manage Categories';
include 'header.php';

// Fetch all categories with product count
$categories = mysqli_query($conn, "SELECT c.*, COUNT(p.product_id) as product_count 
                                    FROM categories c 
                                    LEFT JOIN products p ON c.category_id = p.category_id 
                                    GROUP BY c.category_id 
                                    ORDER BY c.category_id ASC");
?>

<?php if(!empty($error)): ?>
  <div class="alert-error">⚠️ <?= $error ?></div>
<?php endif; ?>

<?php if($msg): $parts = explode('|', $msg); ?>
  <div class="alert-<?= $parts[0] ?>"><?= $parts[1] ?></div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 380px; gap:24px; align-items:flex-start;">

  <!-- LEFT: CATEGORIES TABLE -->
  <div class="table-card">
    <div class="table-header">
      <h3>🗂️ All Categories (<?= mysqli_num_rows($categories) ?>)</h3>
    </div>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Category Name</th>
          <th>Description</th>
          <th>Products</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($categories)): ?>
        <tr>
          <td><?= $row['category_id'] ?></td>
          <td>
            <strong style="font-size:15px;"><?= htmlspecialchars($row['category_name']) ?></strong>
          </td>
          <td style="color:#888; font-size:13px;">
            <?= !empty($row['description']) ? htmlspecialchars($row['description']) : '<span style="color:#ccc;">—</span>' ?>
          </td>
          <td style="text-align:center;">
            <span style="background:#e8f4fd; color:#1C7293; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
              <?= $row['product_count'] ?> product<?= $row['product_count'] != 1 ? 's' : '' ?>
            </span>
          </td>
          <td>
            <a href="categories.php?edit=<?= $row['category_id'] ?>" class="btn-edit">✏️ Edit</a>
            &nbsp;
            <?php if($row['product_count'] == 0): ?>
              <a href="categories.php?delete=<?= $row['category_id'] ?>"
                 class="btn-delete"
                 onclick="return confirm('Delete category \'<?= htmlspecialchars($row['category_name']) ?>\'?')">
                🗑️ Delete
              </a>
            <?php else: ?>
              <span style="color:#ccc; font-size:12px; cursor:not-allowed;"
                    title="Remove all products first">🗑️ Delete</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- RIGHT: ADD / EDIT FORM -->
  <div class="form-card" style="max-width:100%;">
    <?php if($edit_cat): ?>
      <!-- EDIT FORM -->
      <h3>✏️ Edit Category</h3>
      <form method="POST" action="categories.php">
        <input type="hidden" name="category_id" value="<?= $edit_cat['category_id'] ?>">
        <div class="form-group">
          <label>Category Name <span style="color:#e94560">*</span></label>
          <input type="text" name="category_name"
                 value="<?= htmlspecialchars($edit_cat['category_name']) ?>" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" placeholder="Short description..."><?= htmlspecialchars($edit_cat['description']) ?></textarea>
        </div>
        <button type="submit" name="edit_category" class="submit-btn">💾 Update Category</button>
        <a href="categories.php" class="cancel-btn">Cancel</a>
      </form>

    <?php else: ?>
      <!-- ADD FORM -->
      <h3>➕ Add New Category</h3>
      <form method="POST" action="categories.php">
        <div class="form-group">
          <label>Category Name <span style="color:#e94560">*</span></label>
          <input type="text" name="category_name" placeholder="e.g. Women" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" placeholder="Short description of this category..."></textarea>
        </div>
        <button type="submit" name="add_category" class="submit-btn">➕ Add Category</button>
      </form>
    <?php endif; ?>
  </div>

</div>

<?php include 'footer.php'; ?>