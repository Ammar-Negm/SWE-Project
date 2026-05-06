<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
 <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">

</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
    <nav class="nav flex-column mt-3">
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/inventory"><i class="bi bi-box-seam"></i> Inventory</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/zonaloptimizer"><i class="bi bi-layers"></i> Zonal Optimizer</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/procurement"><i class="bi bi-cart3"></i> Procurement</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/supplier"><i class="bi bi-truck"></i> Suppliers</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/analytics"><i class="bi bi-graph-up"></i> Analytics</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/systemadmin"><i class="bi bi-gear"></i> System Admin</a>
</nav>
    <div class="user-info mt-auto">
      <i class="bi bi-person-circle"></i> Logged in as: <span class="php-dynamic text-warning">Manager</span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">Analytics & Reports</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="position-relative cursor-pointer">
          <i class="bi bi-bell fs-5"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">5</span>
        </div>
        <div class="dropdown">
          <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <img src="https://ui-avatars.com/api/?name=Admin&background=1A3C5E&color=fff" class="rounded-circle" width="32">
          </button>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row mb-4">
        <div class="col-md-6 mb-4">
          <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-bold m-0">Warehouse Throughput (Last 30 Days)</h6>
              <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> CSV</button>
            </div>
            <div class="bg-light border rounded d-flex align-items-center justify-content-center h-100" style="min-height: 250px;">
              <span class="text-muted">[ Chart Container ]</span>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 mb-4">
          <div class="card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-bold m-0">Supplier Accuracy Comparison</h6>
              <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> CSV</button>
            </div>
            <div class="bg-light border rounded d-flex align-items-center justify-content-center h-100" style="min-height: 250px;">
              <span class="text-muted">[ Chart Container ]</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card p-4 bg-success text-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold m-0"><i class="bi bi-tree"></i> Sustainability Overview</h5>
          <span class="badge bg-light text-success fs-6">Carbon Audit Passed</span>
        </div>
        <div class="row text-center">
          <div class="col-md-6 border-end border-light">
            <h3 class="fw-bold php-dynamic">12,400 <small class="fs-6 fw-normal">km/month</small></h3>
            <p class="m-0 text-white-50">Total Estimated Shipping Distance</p>
          </div>
          <div class="col-md-6">
            <h3 class="fw-bold php-dynamic">4,200 <small class="fs-6 fw-normal">kWh/month</small></h3>
            <p class="m-0 text-white-50">Simulated Energy Usage</p>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
  });
</script>
</body>
</html>