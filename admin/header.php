<?php
// Protect all admin pages
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Vastra Admin</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI',sans-serif;
      background:#f0f2f5;
      display:flex; min-height:100vh;
    }

    /* ── SIDEBAR ── */
    .sidebar {
      width:240px; background:#0d1117;
      display:flex; flex-direction:column;
      position:fixed; top:0; left:0;
      height:100vh; z-index:200;
    }
    .sidebar-brand {
      padding:22px 24px;
      border-bottom:1px solid #21262d;
    }
    .sidebar-brand h2 {
      font-size:22px; font-weight:800; color:#fff;
    }
    .sidebar-brand h2 span { color:#e94560; }
    .sidebar-brand p { color:#8b949e; font-size:11px; margin-top:3px; }

    .sidebar-menu { padding:16px 0; flex:1; }
    .menu-label {
      font-size:10px; font-weight:700; color:#8b949e;
      text-transform:uppercase; letter-spacing:1.5px;
      padding:8px 24px 4px;
    }
    .sidebar-menu a {
      display:flex; align-items:center; gap:12px;
      padding:11px 24px; color:#c9d1d9;
      text-decoration:none; font-size:14px; font-weight:500;
      transition:background 0.15s, color 0.15s;
      border-left:3px solid transparent;
    }
    .sidebar-menu a:hover {
      background:#161b22; color:#fff;
    }
    .sidebar-menu a.active {
      background:#161b22; color:#e94560;
      border-left-color:#e94560;
    }
    .sidebar-menu .icon { font-size:17px; width:22px; text-align:center; }

    .sidebar-footer {
      padding:16px 24px;
      border-top:1px solid #21262d;
    }
    .sidebar-footer a {
      display:flex; align-items:center; gap:10px;
      color:#8b949e; text-decoration:none; font-size:13px;
    }
    .sidebar-footer a:hover { color:#e94560; }

    /* ── MAIN CONTENT ── */
    .main-content {
      margin-left:240px; flex:1;
      display:flex; flex-direction:column; min-height:100vh;
    }

    /* ── TOP BAR ── */
    .topbar {
      background:#fff; padding:14px 30px;
      display:flex; align-items:center;
      justify-content:space-between;
      box-shadow:0 1px 4px rgba(0,0,0,0.08);
      position:sticky; top:0; z-index:100;
    }
    .topbar h1 { font-size:20px; font-weight:700; color:#111; }
    .topbar-right {
      display:flex; align-items:center; gap:16px;
    }
    .admin-badge {
      background:#fde8ec; color:#e94560;
      font-size:12px; font-weight:700;
      padding:4px 14px; border-radius:20px;
    }
    .view-store {
      background:#111; color:#fff;
      padding:7px 16px; border-radius:7px;
      text-decoration:none; font-size:13px; font-weight:600;
    }
    .view-store:hover { background:#333; }

    /* ── PAGE BODY ── */
    .page-body { padding:28px 30px; flex:1; }

    /* ── CARDS ── */
    .stat-grid {
      display:grid; grid-template-columns:repeat(4,1fr);
      gap:20px; margin-bottom:28px;
    }
    .stat-card {
      background:#fff; border-radius:12px;
      padding:22px 24px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
      border-left:4px solid #e94560;
      display:flex; align-items:center; gap:18px;
    }
    .stat-card.blue  { border-left-color:#1C7293; }
    .stat-card.green { border-left-color:#2D7D46; }
    .stat-card.gold  { border-left-color:#F5A623; }
    .stat-icon { font-size:32px; }
    .stat-info h3 { font-size:26px; font-weight:800; color:#111; }
    .stat-info p  { font-size:13px; color:#888; margin-top:2px; }

    /* ── TABLE ── */
    .table-card {
      background:#fff; border-radius:12px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
      overflow:hidden;
    }
    .table-header {
      padding:18px 22px;
      display:flex; align-items:center;
      justify-content:space-between;
      border-bottom:1px solid #f0f0f0;
    }
    .table-header h3 { font-size:16px; font-weight:700; color:#111; }
    .btn-add {
      background:#e94560; color:#fff;
      padding:9px 20px; border-radius:8px;
      text-decoration:none; font-size:13px; font-weight:700;
      border:none; cursor:pointer;
    }
    .btn-add:hover { background:#c73652; }

    table { width:100%; border-collapse:collapse; }
    thead { background:#f8f8f8; }
    th {
      padding:12px 18px; text-align:left;
      font-size:12px; font-weight:700;
      color:#555; text-transform:uppercase;
      letter-spacing:0.5px;
      border-bottom:1px solid #eee;
    }
    td {
      padding:14px 18px; font-size:14px;
      color:#333; border-bottom:1px solid #f5f5f5;
      vertical-align:middle;
    }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#fafafa; }

    /* BADGES */
    .badge {
      padding:4px 12px; border-radius:20px;
      font-size:11px; font-weight:700;
      text-transform:uppercase; letter-spacing:0.5px;
    }
    .badge-pending  { background:#fff3cd; color:#856404; }
    .badge-processing { background:#cce5ff; color:#004085; }
    .badge-delivered { background:#d4edda; color:#155724; }
    .badge-cancelled { background:#f8d7da; color:#721c24; }

    /* ACTION BUTTONS */
    .btn-edit {
      background:#e8f4fd; color:#1C7293;
      border:none; padding:6px 14px;
      border-radius:6px; font-size:12px;
      font-weight:600; cursor:pointer;
      text-decoration:none; display:inline-block;
    }
    .btn-edit:hover { background:#1C7293; color:#fff; }
    .btn-delete {
      background:#fde8ec; color:#e94560;
      border:none; padding:6px 14px;
      border-radius:6px; font-size:12px;
      font-weight:600; cursor:pointer;
      text-decoration:none; display:inline-block;
    }
    .btn-delete:hover { background:#e94560; color:#fff; }

    /* FORMS */
    .form-card {
      background:#fff; border-radius:12px;
      padding:28px; max-width:680px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
    }
    .form-card h3 {
      font-size:17px; font-weight:700;
      margin-bottom:22px; color:#111;
      border-bottom:2px solid #e94560;
      padding-bottom:10px;
    }
    .form-group { margin-bottom:18px; }
    .form-group label {
      display:block; font-size:13px;
      font-weight:600; color:#555; margin-bottom:6px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      width:100%; padding:11px 15px;
      border:1.5px solid #e0e0e0; border-radius:9px;
      font-size:14px; outline:none;
      transition:border-color 0.2s; color:#222;
      font-family:'Segoe UI',sans-serif;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color:#e94560; }
    .form-group textarea { resize:vertical; min-height:90px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .submit-btn {
      background:#e94560; color:#fff; border:none;
      padding:13px 32px; border-radius:9px;
      font-size:15px; font-weight:700; cursor:pointer;
    }
    .submit-btn:hover { background:#c73652; }
    .cancel-btn {
      background:#f0f0f0; color:#555; border:none;
      padding:13px 24px; border-radius:9px;
      font-size:15px; font-weight:600; cursor:pointer;
      text-decoration:none; display:inline-block; margin-left:10px;
    }
    .cancel-btn:hover { background:#ddd; }

    /* ALERT */
    .alert-success {
      background:#d4edda; color:#155724;
      padding:12px 18px; border-radius:8px;
      font-size:14px; margin-bottom:20px;
      border-left:4px solid #28a745;
    }
    .alert-error {
      background:#fde8ec; color:#c0392b;
      padding:12px 18px; border-radius:8px;
      font-size:14px; margin-bottom:20px;
      border-left:4px solid #e94560;
    }

    /* PRODUCT IMG THUMB */
    .prod-thumb {
      width:44px; height:44px; border-radius:8px;
      background:#f0f0f0; display:flex;
      align-items:center; justify-content:center;
      font-size:20px; overflow:hidden;
    }
    .prod-thumb img { width:100%; height:100%; object-fit:cover; }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <h2>Vastra<span>.</span></h2>
    <p>Admin Panel</p>
  </div>
  <nav class="sidebar-menu">
    <div class="menu-label">Main</div>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">
      <span class="icon">📊</span> Dashboard
    </a>
    <div class="menu-label">Store</div>
    <a href="products.php" class="<?= basename($_SERVER['PHP_SELF'])=='products.php'||basename($_SERVER['PHP_SELF'])=='add_product.php'||basename($_SERVER['PHP_SELF'])=='edit_product.php'?'active':'' ?>">
      <span class="icon">👕</span> Products
    </a>
    <a href="categories.php" class="<?= basename($_SERVER['PHP_SELF'])=='categories.php'?'active':'' ?>">
      <span class="icon">🗂️</span> Categories
    </a>
    <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF'])=='orders.php'?'active':'' ?>">
      <span class="icon">📦</span> Orders
    </a>
    <a href="users.php" class="<?= basename($_SERVER['PHP_SELF'])=='users.php'?'active':'' ?>">
      <span class="icon">👥</span> Users
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php">🚪 Logout</a>
  </div>
</aside>

<!-- MAIN -->
<div class="main-content">
  <div class="topbar">
    <h1><?= isset($page_title) ? $page_title : 'Dashboard' ?></h1>
    <div class="topbar-right">
      <span class="admin-badge">👤 <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
      <a href="../index.php" target="_blank" class="view-store">🛍️ View Store</a>
    </div>
  </div>
  <div class="page-body">