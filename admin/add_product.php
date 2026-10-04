<?php
session_start();
include '../config/db.php';

// Protect page
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error   = '';

// ── Handle POST first (before any HTML) ──────────────────────────
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name        = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $cat_id      = (int)$_POST['category_id'];
    $price       = (float)$_POST['price'];
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $stock       = (int)$_POST['stock_qty'];

    // Sizes — join selected checkboxes
    $sizes = isset($_POST['sizes']) ? implode(',', array_map('trim', $_POST['sizes'])) : 'S,M,L,XL,XXL';
    $sizes = mysqli_real_escape_string($conn, $sizes);

    // Image upload
    $image = '';
    if(!empty($_FILES['image']['name'])) {
        $img_name = basename($_FILES['image']['name']);
        $img_ext  = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','gif','webp'];

        if(in_array($img_ext, $allowed)) {
            $new_name  = time() . '_' . preg_replace('/\s+/', '_', $img_name);
            $upload_to = '../images/' . $new_name;

            if(!is_dir('../images/')) {
                mkdir('../images/', 0777, true);
            }

            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_to)) {
                $image = $new_name;
            } else {
                $error = "Image upload failed. Check images/ folder permissions.";
            }
        } else {
            $error = "Only JPG, PNG, GIF, WEBP files allowed.";
        }
    }

    if(empty($error)) {
       $q = "INSERT INTO products (category_id, product_name, description, price, sizes, image)
      VALUES ($cat_id, '$name', '$description', $price, '$sizes', '$image')";

        if(mysqli_query($conn, $q)) {
            header("Location: products.php?msg=added");
            exit();
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}

// Fetch categories for dropdown
$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");

include 'header.php';
?>

<div class="admin-content">
  <div class="page-header">
    <h1>Add New Product</h1>
    <a href="products.php" class="btn-back">← Back to Products</a>
  </div>

  <?php if(!empty($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <div class="form-card">
    <form method="POST" enctype="multipart/form-data">

      <div class="form-row">
        <div class="form-group">
          <label>Product Name *</label>
          <input type="text" name="product_name" placeholder="e.g. Black Hoodie"
                 value="<?= isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : '' ?>"
                 required>
        </div>
        <div class="form-group">
          <label>Category *</label>
          <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php while($c = mysqli_fetch_assoc($cats)): ?>
              <option value="<?= $c['category_id'] ?>"
                <?= (isset($_POST['category_id']) && $_POST['category_id'] == $c['category_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['category_name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Price (Rs.) *</label>
          <input type="number" name="price" step="0.01" min="0"
                 placeholder="e.g. 1499"
                 value="<?= isset($_POST['price']) ? $_POST['price'] : '' ?>"
                 required>
        </div>
        <div class="form-group">
          <label>Stock Quantity *</label>
          <input type="number" name="stock_qty" min="0"
                 placeholder="e.g. 50"
                 value="<?= isset($_POST['stock_qty']) ? $_POST['stock_qty'] : '' ?>"
                 required>
        </div>
      </div>

      <!-- SIZES -->
      <div class="form-group">
        <label>Available Sizes</label>
        <div class="size-checkboxes">
          <?php
            $all_sizes = ['XS','S','M','L','XL','XXL'];
            $selected_sizes = isset($_POST['sizes']) ? $_POST['sizes'] : ['S','M','L','XL','XXL'];
            foreach($all_sizes as $sz):
          ?>
          <label class="size-label">
            <input type="checkbox" name="sizes[]" value="<?= $sz ?>"
                   <?= in_array($sz, $selected_sizes) ? 'checked' : '' ?>>
            <span><?= $sz ?></span>
          </label>
          <?php endforeach; ?>
        </div>
        <small style="color:#888;">Check all sizes that are available for this product</small>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="3"
                  placeholder="Short product description..."><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
      </div>

      <div class="form-group">
        <label>Product Image</label>
        <input type="file" name="image" accept="image/*" id="imgInput">
        <div id="imgPreview" style="margin-top:10px;"></div>
        <small style="color:#888;">JPG, PNG, WEBP — Max 5MB. Images go to vastra/images/ folder.</small>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-save">✅ Add Product</button>
        <a href="products.php" class="btn-cancel">Cancel</a>
      </div>

    </form>
  </div>
</div>

<script>
document.getElementById('imgInput').addEventListener('change', function() {
  const file = this.files[0];
  if(file) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('imgPreview').innerHTML =
        `<img src="${e.target.result}" style="max-width:200px;max-height:180px;border-radius:8px;border:1px solid #eee;">`;
    };
    reader.readAsDataURL(file);
  }
});
</script>

<style>
.admin-content { padding: 30px; max-width: 800px; }
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.page-header h1 { font-size:22px; font-weight:700; }
.btn-back { color:#555; text-decoration:none; font-size:14px; }
.btn-back:hover { color:#e94560; }
.alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px; }
.alert-danger { background:#fde8ec; color:#c0392b; border-left:4px solid #e94560; }
.form-card { background:#fff; border-radius:12px; padding:28px; box-shadow:0 2px 10px rgba(0,0,0,0.07); }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
.form-group { margin-bottom:20px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#555; margin-bottom:7px; }
.form-group input[type="text"],
.form-group input[type="number"],
.form-group input[type="file"],
.form-group select,
.form-group textarea {
  width:100%; padding:10px 14px; border:1.5px solid #e0e0e0;
  border-radius:8px; font-size:14px; outline:none;
  transition:border-color 0.2s; font-family:inherit;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus { border-color:#e94560; }
.form-group textarea { resize:vertical; }

/* SIZE CHECKBOXES */
.size-checkboxes { display:flex; gap:10px; flex-wrap:wrap; margin-top:4px; }
.size-label { display:flex; align-items:center; cursor:pointer; }
.size-label input[type="checkbox"] { display:none; }
.size-label span {
  padding:8px 16px; border:2px solid #ddd; border-radius:6px;
  font-size:13px; font-weight:600; color:#555;
  transition:all 0.15s; user-select:none;
}
.size-label input:checked + span {
  border-color:#e94560; background:#fde8ec; color:#e94560;
}
.size-label:hover span { border-color:#e94560; }

.form-actions { display:flex; gap:12px; margin-top:10px; }
.btn-save {
  background:#e94560; color:#fff; border:none;
  padding:12px 28px; border-radius:8px;
  font-size:15px; font-weight:600; cursor:pointer;
}
.btn-save:hover { background:#c73652; }
.btn-cancel {
  background:#f5f5f5; color:#555; text-decoration:none;
  padding:12px 22px; border-radius:8px;
  font-size:15px; font-weight:600;
}
</style>

<?php include 'footer.php'; ?>