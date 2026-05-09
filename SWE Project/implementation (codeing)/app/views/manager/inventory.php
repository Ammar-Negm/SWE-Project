<?php /** @var array $data */ ?>
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
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/clients"><i class="bi bi-people"></i> Clients</a>
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
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">Inventory Management</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="<?= BASE_URL ?>index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>
    </nav>

    <div class="container-fluid py-4">

      <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success">Operation completed successfully.</div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

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
          <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus-lg"></i> Add Item
          </button>
        </div>
      </div>

      <div class="card p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>SKU</th>
                <th>Product Name</th>
                <th>Zone/Bin</th>
                <th>Qty Available</th>
                <th>Qty Reserved</th>
                <th>Unit Weight</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                  <tr>
                    <td><?= htmlspecialchars($p['SKU'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['name'] ?? '') ?></td>
                    <td>
                      <span class="badge bg-light text-dark">
                        <?= htmlspecialchars(!empty($p['zones']) ? $p['zones'] : 'Not Assigned') ?>
                      </span>
                    </td>
                    <td class="fw-bold text-primary"><?= number_format((float)($p['total_available'] ?? 0)) ?></td>
                    <td>—</td>
                    <td>$<?= number_format((float)($p['basePrice'] ?? 0), 2) ?></td>
                    <td>
                      <?php
                        $available = (float)($p['total_available'] ?? 0);
                        $minStock  = (float)($p['minStockLevel'] ?? 0);
                      ?>
                      <?php if ($available > $minStock): ?>
                        <span class="badge bg-success">In Stock</span>
                      <?php elseif ($available > 0): ?>
                        <span class="badge bg-warning text-dark">Low Stock</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Out of Stock</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <button
                        class="btn btn-sm btn-outline-primary"
                        onclick="openEdit(
                          <?= (int)$p['product_id'] ?>,
                          <?= json_encode($p['SKU'] ?? '') ?>,
                          <?= json_encode($p['name'] ?? '') ?>,
                          <?= json_encode($p['basePrice'] ?? 0) ?>,
                          <?= json_encode($p['prod_cat'] ?? '') ?>,
                          <?= json_encode($p['minStockLevel'] ?? 0) ?>
                        )">
                        <i class="bi bi-pencil"></i>
                      </button>

                      <a href="<?= BASE_URL ?>index.php?url=Manager/deleteProduct/<?= (int)$p['product_id'] ?>"
                         class="btn btn-sm btn-outline-danger"
                         onclick="return confirm('Delete this product?')">
                        <i class="bi bi-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-4">No inventory items found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add Item Modal -->
      <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold">Add New Inventory Item</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="<?= BASE_URL ?>index.php?url=Manager/addProduct">
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">SKU</label>
                  <input type="text" class="form-control" name="sku" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Product Name</label>
                  <input type="text" class="form-control" name="name" required>
                </div>

                <div class="row mb-3">
                  <div class="col-6">
                    <label class="form-label text-primary fw-bold">Target Bin</label>
                    <select class="form-select border-primary" name="bin_id" required>
                      <option value="" selected disabled>Select Bin...</option>
                      <?php if (!empty($bins)): ?>
                        <?php foreach ($bins as $bin): ?>
                          <option value="<?= (int)$bin['bin_id'] ?>">
                            <?= htmlspecialchars(($bin['zone_name'] ?? 'Unknown Zone') . ' - ' . ($bin['shelfLocation'] ?? 'Unknown Shelf')) ?>
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option value="">No Bins found in database</option>
                      <?php endif; ?>
                    </select>
                  </div>

                  <div class="col-6">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" name="price">
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-6">
                    <label class="form-label">Initial Quantity</label>
                    <input type="number" class="form-control" name="initial_qty" required min="1" placeholder="e.g., 50">
                  </div>

                  <div class="col-6">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" name="category" placeholder="e.g., Electronics">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Minimum Stock</label>
                  <input type="number" step="0.01" class="form-control" name="minStock">
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Item</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Edit Item Modal -->
      <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold">Edit Item</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="editForm" action="<?= BASE_URL ?>index.php?url=Manager/editProduct">
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">SKU</label>
                  <input type="text" class="form-control" id="edit_sku" name="sku" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Product Name</label>
                  <input type="text" class="form-control" id="edit_name" name="name" required>
                </div>

                <div class="row mb-3">
                  <div class="col-6">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" id="edit_category" name="category">
                  </div>

                  <div class="col-6">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="edit_price" name="price">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Minimum Stock Level</label>
                  <input type="number" class="form-control" id="edit_minStock" name="minStock">
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Item</button>
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
  const toggleBtn = document.getElementById('sidebarToggle');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('show');
    });
  }

  function openEdit(id, sku, name, price, category, minStock) {
    document.getElementById('edit_sku').value = sku ?? '';
    document.getElementById('edit_name').value = name ?? '';
    document.getElementById('edit_price').value = price ?? '';
    document.getElementById('edit_category').value = category ?? '';
    document.getElementById('edit_minStock').value = minStock ?? '';
    document.getElementById('editForm').action =
      '<?= BASE_URL ?>index.php?url=Manager/editProduct/' + id;

    new bootstrap.Modal(document.getElementById('editItemModal')).show();
  }
</script>
</body>
</html>