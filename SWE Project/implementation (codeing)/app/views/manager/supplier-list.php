<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
    <nav class="nav flex-column mt-3">
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/inventory"><i class="bi bi-box-seam"></i> Inventory</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/zonaloptimizer"><i class="bi bi-layers"></i> Zonal Optimizer</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/procurement"><i class="bi bi-cart3"></i> Procurement</a>
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/supplier"><i class="bi bi-truck"></i> Suppliers</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/analytics"><i class="bi bi-graph-up"></i> Analytics</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/adduser"><i class="bi bi-person-plus"></i> Add User</a>
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
        <h4 class="mb-0 fw-bold">Supplier Directory</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="position-relative cursor-pointer">
          <a href="index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row mb-4 align-items-center">
        <div class="col-md-6">
          <h4 class="fw-bold mb-0">Supplier Directory</h4>
        </div>
        <div class="col-md-6 text-end d-flex gap-2 justify-content-md-end mt-3 mt-md-0">
          <input type="text" class="form-control w-auto" placeholder="Search suppliers...">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal"><i class="bi bi-plus-lg"></i> Add Supplier</button>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card p-4 h-100 text-center">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="text-start">
                <h5 class="fw-bold mb-0 php-dynamic">AlphaParts Ltd.</h5>
                <small class="text-muted"><i class="bi bi-envelope"></i> contact@alphaparts.com</small>
              </div>
              <div class="circular-progress bg-light shadow-sm">
                <span class="php-dynamic">98%</span>
              </div>
            </div>
            <div class="d-flex justify-content-around bg-light p-2 rounded mb-3">
              <div><small class="text-muted d-block">Lead Time</small><strong class="php-dynamic">4.2 Days</strong></div>
              <div><small class="text-muted d-block">Status</small><span class="badge bg-success">Active</span></div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary w-50">Profile</button>
              <a href="procurement.html" class="btn btn-primary w-50">Raise PO</a>
            </div>
          </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Add New Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>index.php?url=Manager/addSupplier">
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Company Name</label><input type="text" class="form-control" name="name" required></div>
            <div class="mb-3"><label class="form-label">Contact Email</label><input type="email" class="form-control" name="email" required></div>
            <div class="mb-3"><label class="form-label">Phone Number</label><input type="text" class="form-control" name="phone"></div>
            <div class="mb-3"><label class="form-label">Expected Lead Time (Days)</label><input type="number" class="form-control" name="lead_time" placeholder="e.g., 4"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Supplier</button>
          </div>
        </form>
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