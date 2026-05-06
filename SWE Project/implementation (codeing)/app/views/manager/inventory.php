<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Management - WareLogix</title>
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
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/inventory"><i class="bi bi-box-seam"></i> Inventory</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/zonaloptimizer"><i class="bi bi-layers"></i> Zonal Optimizer</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/procurement"><i class="bi bi-cart3"></i> Procurement</a>
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
        <h4 class="mb-0 fw-bold">Inventory Management</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="position-relative cursor-pointer">
          <a href="index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row mb-3 align-items-center">
        <div class="col-md-4 mb-2 mb-md-0">
          <input type="text" class="form-control" placeholder="Search by SKU or Name...">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
          <select class="form-select">
            <option value="">Filter by Zone</option>
            <option value="A">Zone A</option>
            <option value="B">Zone B</option>
          </select>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
          <select class="form-select">
            <option value="">Filter by Status</option>
            <option value="instock">In Stock</option>
            <option value="low">Low Stock</option>
          </select>
        </div>
        <div class="col-md-2 text-md-end">
          <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addItemModal"><i class="bi bi-plus-lg"></i> Add Item</button>
        </div>
      </div>

      <div class="card p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>SKU</th><th>Product Name</th><th>Zone/Bin</th>
                <th>Qty Available</th><th>Qty Reserved</th><th>Unit Weight</th>
                <th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="php-dynamic">SKU-001</span></td>
                <td><span class="php-dynamic">Electronic Components</span></td>
                <td><span class="php-dynamic">A-12</span></td>
                <td><span class="php-dynamic">1,200</span></td>
                <td><span class="php-dynamic">150</span></td>
                <td><span class="php-dynamic">0.5 kg</span></td>
                <td><span class="badge bg-success">In Stock</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewItemModal"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editItemModal"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#depalletizeModal">De-palletize</button>
                </td>
              </tr>
              <tr>
                <td><span class="php-dynamic">SKU-002</span></td>
                <td><span class="php-dynamic">Chilled Produce</span></td>
                <td><span class="php-dynamic">C-04</span></td>
                <td><span class="php-dynamic">85</span></td>
                <td><span class="php-dynamic">20</span></td>
                <td><span class="php-dynamic">2.0 kg</span></td>
                <td><span class="badge bg-warning text-dark">Low Stock</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewItemModal"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editItemModal"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#depalletizeModal">De-palletize</button>
                </td>
              </tr>
              </tbody>
          </table>
        </div>
      </div>

      <div class="modal fade" id="depalletizeModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Bulk Breakdown (De-palletize)</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="process_depalletize.php">
              <div class="modal-body">
                <div class="mb-3">
                  <label for="palletId" class="form-label">Pallet ID</label>
                  <input type="text" class="form-control" id="palletId" name="palletId" value="PAL-9923" readonly>
                </div>
                <div class="mb-3">
                  <label for="totalUnits" class="form-label">Total Units</label>
                  <input type="number" class="form-control" id="totalUnits" name="totalUnits" required>
                </div>
                <div class="mb-3">
                  <label for="targetBin" class="form-label">Target Bin</label>
                  <input type="text" class="form-control" id="targetBin" name="targetBin" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Process</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Add New Inventory Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="add_item.php">
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">SKU</label><input type="text" class="form-control" name="sku" required placeholder="e.g., SKU-1234"></div>
            <div class="mb-3"><label class="form-label">Product Name</label><input type="text" class="form-control" name="product_name" required></div>
            <div class="row mb-3">
              <div class="col-6"><label class="form-label">Zone/Bin</label><input type="text" class="form-control" name="zone"></div>
              <div class="col-6"><label class="form-label">Initial Qty</label><input type="number" class="form-control" name="qty"></div>
            </div>
            <div class="mb-3"><label class="form-label">Unit Weight (kg)</label><input type="number" step="0.01" class="form-control" name="weight"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Item</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="viewItemModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold">Item Details: <span class="text-primary-custom">SKU-001</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Name:</strong> Electronic Components</p>
          <p><strong>Location:</strong> Zone A-12</p>
          <p><strong>Available Qty:</strong> 1,200</p>
          <p><strong>Reserved Qty:</strong> 150</p>
          <p><strong>Weight:</strong> 0.5 kg</p>
          <p><strong>Status:</strong> <span class="badge bg-success">In Stock</span></p>
          <hr>
          <small class="text-muted">Last received: 12 Oct 2025 | Supplier: AlphaParts Ltd.</small>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Edit Item (SKU-001)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="edit_item.php">
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Product Name</label><input type="text" class="form-control" name="product_name" value="Electronic Components"></div>
            <div class="mb-3"><label class="form-label">Zone/Bin</label><input type="text" class="form-control" name="zone" value="A-12"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
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