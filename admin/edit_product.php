<?php
session_start();
include '../config/db.php';
$page_title = 'Edit Product';
include 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE product_id=$id"));

if(!$product) {
    header("Location: products.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name   = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $cat_id = (int)$_POST['category_id'];
    $desc   = mysqli_real_escape_string($conn, trim($_POST['description']));
    $price  = (float)$_POST['price'];
    $stock  = (int)$_POST['stock_qty'];
    $image  = $product['image']; // keep old image by default

    // Handle new image upload
    if(!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $new_image = time() . '_' . basename($_FILES['image']['name']);
            if(move_uploaded_file($_FILES['image']['tmp_name'], '../images/' . $new_image)) {
                $image = $new_image;
            }
        } else {
            $error = "Only JPG, PNG, WEBP images allowed.";
        }
    }

    if(empty($error)) {
        $sql = "UPDATE products SET 
                    category_id='$cat_id',
                    product_name='$name',
                    description='$desc',
                    price=$price,
                    stock_qty=$stock,
                    image='$image'
                WHERE product_id=$id";
        if(mysqli_query($conn, $sql)) {
            header("Location: products.php?msg=updated");
            exit();
        } else {
            $error = "Update failed. Please try again.";
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
?>

<div style="margin-bottom:16px;">
  <a href="products.php" style="color:#888; text-decoration:none; font-size:14px;">← Back to Products</a>
</div>

<div class="form-card">
  <h3>✏️ Edit Product — <?= htmlspecialchars($product['product_name']) ?></h3>

  <?php if(!empty($error)): ?>
    <div class="alert-error">⚠️ <?= $error ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label>Product Name <span style="color:#e94560">*</span></label>
      <input type="text" name="product_name"
             value="<?= htmlspecialchars($product['product_name']) ?>" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Category <span style="color:#e94560">*</span></label>
        <select name="category_id" required>
          <?php while($cat = mysqli_fetch_assoc($categories)): ?>
            <option value="<?= $cat['category_id'] ?>"
              <?= $cat['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['category_name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Price (Rs.) <span style="color:#e94560">*</span></label>
        <input type="number" name="price" step="0.01" min="0"
               value="<?= $product['price'] ?>" required>
      </div>
    </div>

    <div class="form-group">
      <label>Description</label>
      <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Stock Quantity <span style="color:#e94560">*</span></label>
        <input type="number" name="stock_qty" min="0"
               value="<?= $product['stock_qty'] ?>" required>
      </div>
      <div class="form-group">
        <label>Product Image</label>
        <?php if(!empty($product['image'])): ?>
          <div style="margin-bottom:8px;">
            <span style="font-size:12px; color:#888;">Current: </span>
            <span style="font-size:12px; color:#333;"><?= htmlspecialchars($product['image']) ?></span>
          </div>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
        <small style="color:#888; font-size:12px;">Leave blank to keep current image</small>
      </div>
    </div>

    <div style="margin-top:8px;">
      <button type="submit" class="submit-btn">💾 Update Product</button>
      <a href="products.php" class="cancel-btn">Cancel</a>
    </div>
  </form>
</div>

<?php include 'footer.php'; ?>