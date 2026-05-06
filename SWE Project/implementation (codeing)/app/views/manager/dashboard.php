<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
?>
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
    <!--
     <nav class="nav flex-column mt-3">
      <a class="nav-link active" href="#"><i class="bi bi-grid-1x2"></i> Dashboard</a>
      <a class="nav-link" href="inventory.php"><i class="bi bi-box-seam"></i> Inventory</a>
      <a class="nav-link" href="#"><i class="bi bi-layers"></i> Zonal Optimizer</a>
      <a class="nav-link" href="#"><i class="bi bi-cart3"></i> Procurement</a>
      <a class="nav-link" href="#"><i class="bi bi-truck"></i> Suppliers</a>
      <a class="nav-link" href="#"><i class="bi bi-graph-up"></i> Analytics</a>
      <a class="nav-link" href="#"><i class="bi bi-gear"></i> System Admin</a>
    </nav> 
    -->
    <nav class="nav flex-column mt-3">
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/inventory"><i class="bi bi-box-seam"></i> Inventory</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/zonaloptimizer"><i class="bi bi-layers"></i> Zonal Optimizer</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/procurement"><i class="bi bi-cart3"></i> Procurement</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/supplier"><i class="bi bi-truck"></i> Suppliers</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/analytics"><i class="bi bi-graph-up"></i> Analytics</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/adduser"><i class="bi bi-person-plus"></i> Add User</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/systemadmin"><i class="bi bi-gear"></i> System Admin</a>
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

    <div class="row">
        <div class="col-lg-8">
          <div class="card p-4 h-100">
            <h5 class="fw-bold mb-3">Recent Inventory Alerts</h5>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Zone</th>
                    <th>Item</th>
                    <th>Alert Type</th>
                    <th>Severity</th>
                    <th>Time</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span class="php-dynamic">A-12</span></td>
                    <td><span class="php-dynamic">SKU-00234 (Chilled Produce)</span></td>
                    <td><span class="php-dynamic">Temp Exceeded (8°C)</span></td>
                    <td><span class="badge bg-danger">Critical</span></td>
                    <td><span class="php-dynamic">10 mins ago</span></td>
                  </tr>
                  <tr>
                    <td><span class="php-dynamic">B-05</span></td>
                    <td><span class="php-dynamic">SKU-00891 (Raw Chemicals)</span></td>
                    <td><span class="php-dynamic">Low Stock</span></td>
                    <td><span class="badge bg-warning text-dark">Warning</span></td>
                    <td><span class="php-dynamic">1 hr ago</span></td>
                  </tr>
                  <tr>
                    <td><span class="php-dynamic">C-22</span></td>
                    <td><span class="php-dynamic">SKU-00445 (Packaging Boxes)</span></td>
                    <td><span class="php-dynamic">Low Stock</span></td>
                    <td><span class="badge bg-warning text-dark">Warning</span></td>
                    <td><span class="php-dynamic">2 hrs ago</span></td>
                  </tr>
                  <tr>
                    <td><span class="php-dynamic">A-02</span></td>
                    <td><span class="php-dynamic">SKU-00112 (Dairy)</span></td>
                    <td><span class="php-dynamic">Expiry Warning (2 Days)</span></td>
                    <td><span class="badge bg-danger">Critical</span></td>
                    <td><span class="php-dynamic">3 hrs ago</span></td>
                  </tr>
                  <tr>
                    <td><span class="php-dynamic">D-15</span></td>
                    <td><span class="php-dynamic">SKU-00999 (Pallet Jack)</span></td>
                    <td><span class="php-dynamic">Weight Discrepancy</span></td>
                    <td><span class="badge bg-secondary">Info</span></td>
                    <td><span class="php-dynamic">5 hrs ago</span></td>
                  </tr>
                  </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3">Active Floor Staff</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <div>
                  <span class="pulse-dot pulse-green me-2"></span>
                  <span class="php-dynamic fw-bold">Ahmed Ali</span>
                  <div class="small text-muted php-dynamic">Picking Order #BPG-0047</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <div>
                  <span class="pulse-dot pulse-green me-2"></span>
                  <span class="php-dynamic fw-bold">Sara Kamal</span>
                  <div class="small text-muted php-dynamic">Packing Station 2</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <div>
                  <span class="pulse-dot pulse-green me-2"></span>
                  <span class="php-dynamic fw-bold">Omar Tarek</span>
                  <div class="small text-muted php-dynamic">QC Inspection Dock A</div>
                </div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <div>
                  <span class="pulse-dot pulse-red me-2"></span>
                  <span class="php-dynamic fw-bold">Mona Zaki</span>
                  <div class="small text-muted php-dynamic">On Break</div>
                </div>
              </li>
              </ul>
          </div>

          <div class="card p-4">
            <h5 class="fw-bold mb-3">Upcoming Reorders</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item px-0">
                <div class="d-flex justify-content-between">
                  <span class="php-dynamic fw-bold">SKU-00445</span>
                  <span class="badge bg-warning text-dark php-dynamic">Due in 2 days</span>
                </div>
                <div class="small text-muted php-dynamic">Supplier: AlphaParts Ltd.</div>
              </li>
              <li class="list-group-item px-0">
                <div class="d-flex justify-content-between">
                  <span class="php-dynamic fw-bold">SKU-00112</span>
                  <span class="badge bg-danger php-dynamic">Due Today</span>
                </div>
                <div class="small text-muted php-dynamic">Supplier: FreshFarm Co.</div>
              </li>
              <li class="list-group-item px-0">
                <div class="d-flex justify-content-between">
                  <span class="php-dynamic fw-bold">SKU-00891</span>
                  <span class="badge bg-secondary php-dynamic">Due in 5 days</span>
                </div>
                <div class="small text-muted php-dynamic">Supplier: ChemSupply Inc.</div>
              </li>
              </ul>
          </div>
        </div>
      </div>

  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

