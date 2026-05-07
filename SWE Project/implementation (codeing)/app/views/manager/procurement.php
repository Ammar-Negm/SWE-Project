<?php
$orders    = $data['orders']    ?? [];
$suppliers = $data['suppliers'] ?? [];
?>
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

  <!-- Sidebar -->
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
      <i class="bi bi-person-circle"></i> Logged in as: <span class="text-warning">Manager</span>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-content">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">Procurement & Orders</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Procurement & Purchase Orders</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
          <i class="bi bi-file-earmark-plus"></i> Generate PO
        </button>
      </div>

      <!-- Tabs -->
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link active fw-bold" href="#">Active Purchase Orders</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Reorder Triggers</a>
        </li>
      </ul>

      <!-- Table -->
      <div class="card p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>PO #</th>
                <th>Supplier</th>
                <th>Total Value</th>
                <th>Order Date</th>
                <th>Expected</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if(empty($orders)): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No purchase orders yet.</td>
                </tr>
              <?php else: ?>
                <?php foreach($orders as $o): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($o['po_number']) ?></strong></td>
                  <td><?= htmlspecialchars($o['supplier_name']) ?></td>
                  <td>$<?= number_format($o['total_value'], 2) ?></td>
                  <td><?= isset($o['order_date']) ? date('M d, Y', strtotime($o['order_date'])) : 'N/A' ?></td>
                  <td><?= isset($o['expected_delivery_date']) ? date('M d, Y', strtotime($o['expected_delivery_date'])) : 'N/A' ?></td>
                  <td>
                    <?php
                      $status = strtolower($o['status']);
                      $badge = match($status) {
                        'pending'    => 'warning text-dark',
                        'shipped'    => 'info text-dark',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'secondary'
                      };
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($o['status']) ?></span>
                  </td>
                  <td>
                    <a href="index.php?url=Manager/viewPO/<?= $o['po_id'] ?>" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal: Generate PO -->
<div class="modal fade" id="generateModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Generate Purchase Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="index.php?url=Manager/generatePO">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select" required>
              <option value="">Select Supplier...</option>
              <?php foreach($suppliers as $s): ?>
                <option value="<?= $s['supplier_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Expected Delivery Date</label>
            <input type="date" name="expected_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Total Value ($)</label>
            <input type="number" name="total_value" class="form-control" step="0.01" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Generate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
  });
</script>
</body>
</html>