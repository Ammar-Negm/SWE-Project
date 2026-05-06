<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purchase Orders - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
 <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
   <nav class="nav flex-column mt-3">
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Supplier/dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Supplier/orders"><i class="bi bi-cart3"></i> Purchase Orders</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Supplier/invoice"><i class="bi bi-receipt"></i> Invoice Manager</a>
</nav>
    <div class="user-info mt-auto">
      <i class="bi bi-building"></i> Logged in as: <span class="php-dynamic text-info">AlphaParts Ltd.</span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">Purchase Orders</h4>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <h4 class="fw-bold mb-4">My Purchase Orders</h4>
      <div class="card p-0 mb-4">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>PO #</th><th>Items</th><th>Total Value</th><th>Order Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="php-dynamic fw-bold">PO-20046</span></td>
                <td><span class="php-dynamic">12</span></td>
                <td><span class="php-dynamic">$1,100.00</span></td>
                <td><span class="php-dynamic">Oct 15, 2025</span></td>
                <td><span class="badge bg-secondary">Awaiting Confirmation</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-secondary">View Details</button>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#confirmShipmentModal">Confirm Shipment</button>
                </td>
              </tr>
              </tbody>
          </table>
        </div>
      </div>

      <div class="modal fade" id="confirmShipmentModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Confirm Shipment</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="confirm_shipment.php">
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">PO #</label>
                  <input type="text" class="form-control bg-light" name="po_id" value="PO-20046" readonly>
                </div>
                <div class="mb-3">
                  <label class="form-label">Shipping Tracking #</label>
                  <input type="text" class="form-control" name="tracking_number" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Actual Ship Date</label>
                  <input type="date" class="form-control" name="ship_date" required>
                </div>
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" id="partialShipment" name="is_partial" data-bs-toggle="collapse" data-bs-target="#partialQtySection">
                  <label class="form-check-label fw-bold" for="partialShipment">Partial Shipment?</label>
                </div>
                <div class="collapse" id="partialQtySection">
                  <div class="card p-3 bg-light">
                    <label class="form-label text-muted small">Specify Qty Shipped per SKU</label>
                    <div class="input-group input-group-sm mb-2">
                      <span class="input-group-text">SKU-001 (Ordered: 500)</span>
                      <input type="number" class="form-control" name="partial_qty[SKU-001]" placeholder="Shipped Qty">
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Confirmation</button>
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