<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #F1F5F9; }
    #wrapper { display: flex; width: 100%; height: 100vh; }
    .sidebar { width: 260px; background-color: #1A3C5E; color: #fff; display: flex; flex-direction: column; }
    .sidebar .brand { font-size: 1.5rem; font-weight: 700; padding: 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 1rem 1.5rem; display: flex; align-items: center; gap: 10px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: rgba(255,255,255,0.1); border-left: 4px solid #F59E0B; }
    .sidebar .user-info { margin-top: auto; padding: 1rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.9rem; }
    .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
    .top-navbar { background: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
    .card { background-color: #fff; border: none; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
  </style>
</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
    <nav class="nav flex-column mt-3">
      <a class="nav-link active" href="#"><i class="bi bi-grid-1x2"></i> Dashboard</a>
      <a class="nav-link" href="#"><i class="bi bi-box-seam"></i> Inventory</a>
      <a class="nav-link" href="#"><i class="bi bi-layers"></i> Zonal Optimizer</a>
      <a class="nav-link" href="#"><i class="bi bi-cart3"></i> Procurement</a>
      <a class="nav-link" href="#"><i class="bi bi-truck"></i> Suppliers</a>
      <a class="nav-link" href="#"><i class="bi bi-graph-up"></i> Analytics</a>
      <a class="nav-link" href="#"><i class="bi bi-gear"></i> System Admin</a>
    </nav>
    <div class="user-info mt-auto">
      <i class="bi bi-person-circle"></i> Logged in as:
      <span class="text-warning"><?= $_SESSION['user_name'] ?? 'Manager' ?></span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <h4 class="mb-0 fw-bold">Dashboard</h4>
      <a href="index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
    </nav>

    <div class="container-fluid py-4">
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Total SKUs</h6>
            <h3 class="fw-bold">4,821</h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Capacity Used</h6>
            <h3 class="fw-bold">73%</h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3">
            <h6 class="text-muted">Open Purchase Orders</h6>
            <h3 class="fw-bold">12</h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3 border-start border-danger border-4">
            <h6 class="text-muted">Pending Alerts</h6>
            <h3 class="fw-bold text-danger">5</h3>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>