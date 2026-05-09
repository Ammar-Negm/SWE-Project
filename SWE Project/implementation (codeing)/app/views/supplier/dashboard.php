<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supplier Dashboard - WareLogix</title>
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
      <i class="bi bi-building"></i> Logged in as:
      <span class="text-info"><?= $_SESSION['user_name'] ?? 'Supplier' ?></span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <h4 class="mb-0 fw-bold">Supplier Portal</h4>
      <a href="<?= BASE_URL ?>index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
    </nav>

    <div class="container-fluid py-4">
      <div class="card bg-primary-custom text-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h3 class="fw-bold mb-1"><?= $_SESSION['user_name'] ?? 'Supplier' ?></h3>
            <p class="mb-0 text-white-50">Supplier Portal Dashboard</p>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="card p-3 h-100 shadow-sm border-start border-warning border-4">
            <h6 class="text-muted">Open POs</h6>
            <h2 class="fw-bold"><?= $openPOs ?? 0 ?></h2>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card p-3 h-100 shadow-sm border-start border-success border-4">
            <h6 class="text-muted">Pending Invoices</h6>
            <h2 class="fw-bold"><?= $pendingInvoices ?? 0 ?></h2>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 h-100 shadow-sm border-start border-info border-4">
            <h6 class="text-muted">Avg. Lead Time</h6>
            <h2 class="fw-bold"><?= $avgLeadTime ?? 0 ?> <span class="fs-6 text-muted">days</span></h2>
          </div>
        </div>
      </div>

      <div class="card p-4">
        <h5 class="fw-bold mb-3">Recent Purchase Orders</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>PO #</th><th>Date</th><th>Amount</th><th>Status</th></tr>
            </thead>
            <tbody>
              <?php if (!empty($recentOrders)): ?>
                <?php foreach ($recentOrders as $order): ?>
                  <tr>
                    <td class="fw-bold"><?= htmlspecialchars($order['po_number'] ?? $order['po_id']) ?></td>
                    <td><?= !empty($order['expected_delivery_date']) ? date('M d, Y', strtotime($order['expected_delivery_date'])) : '-' ?></td>
                    <td>$<?= number_format((float)($order['total_value'] ?? 0), 2) ?></td>
                    <td>
                      <span class="badge bg-warning text-dark">
                        <?= htmlspecialchars($order['status'] ?? 'pending') ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">No recent orders found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>