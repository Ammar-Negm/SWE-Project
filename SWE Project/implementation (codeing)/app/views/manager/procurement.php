<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Procurement - WareLogix</title>
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
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/procurement"><i class="bi bi-cart3"></i> Procurement</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/supplier"><i class="bi bi-truck"></i> Suppliers</a>
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
        <h4 class="mb-0 fw-bold">Procurement & Orders</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="position-relative cursor-pointer">
          <a href="index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Procurement & Purchase Orders</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePoModal">
          <i class="bi bi-file-earmark-plus"></i> Generate PO
        </button>
      </div>

      <ul class="nav nav-tabs mb-4" id="procurementTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#active-pos" type="button">Active Purchase Orders</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#triggers" type="button">Reorder Triggers</button>
        </li>
      </ul>

      <div class="tab-content" id="procurementTabsContent">
        <div class="tab-pane fade show active" id="active-pos">
          <div class="card p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>PO #</th><th>Supplier</th><th>Total Value</th>
                    <th>Order Date</th><th>Expected</th><th>Status</th><th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span class="php-dynamic fw-bold">PO-20045</span></td>
                    <td><span class="php-dynamic">AlphaParts Ltd.</span></td>
                    <td><span class="php-dynamic">$4,250.00</span></td>
                    <td><span class="php-dynamic">Oct 12, 2025</span></td>
                    <td><span class="php-dynamic">Oct 18, 2025</span></td>
                    <td><span class="badge bg-warning text-dark">In Transit</span></td>
                    <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
                  </tr>
                  </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="triggers">
          <div class="card p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>SKU</th><th>Product</th><th>Current Stock</th>
                    <th>Suggested Qty</th><th>Supplier</th><th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span class="php-dynamic">SKU-003</span></td>
                    <td><span class="php-dynamic">Raw Chemicals</span></td>
                    <td class="text-danger fw-bold"><span class="php-dynamic">0</span></td>
                    <td><span class="php-dynamic">200</span></td>
                    <td><span class="php-dynamic">ChemSupply Inc.</span></td>
                    <td><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#generatePoModal">Generate PO</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="generatePoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Generate Purchase Order</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="generate_po.php">
              <div class="modal-body">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select class="form-select" name="supplier_id" required>
                      <option value="1">AlphaParts Ltd.</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Required By Date</label>
                    <input type="date" class="form-control" name="required_date" required>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Purchase Order</button>
              </div>
            </form>
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