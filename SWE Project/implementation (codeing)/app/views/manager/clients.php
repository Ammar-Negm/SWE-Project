<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Management - WareLogix</title>
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
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/supplier"><i class="bi bi-truck"></i> Suppliers</a>
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/analytics"><i class="bi bi-graph-up"></i> Analytics</a>
      <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/clients"><i class="bi bi-people"></i> Clients</a>
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/adduser"><i class="bi bi-person-plus"></i> Add User</a>
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/systemadmin"><i class="bi bi-gear"></i> System Admin</a>
    </nav>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <h4 class="mb-0 fw-bold">Client Directory</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal"><i class="bi bi-plus-lg"></i> Add New Client</button>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="card p-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>ID</th><th>Client Name</th><th>Email</th><th>Total Orders</th><th>Last Order</th><th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="php-dynamic">CL-101</span></td>
                <td><span class="php-dynamic fw-bold">Hameed Retail Stores</span></td>
                <td><span class="php-dynamic">info@hameed-retail.com</span></td>
                <td><span class="php-dynamic">15</span></td>
                <td><span class="php-dynamic">2026-05-01</span></td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View History</button>
                </td>
              </tr>
              </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="modal fade" id="addClientModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?= BASE_URL ?>index.php?url=Manager/add_client">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Register New Client</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Client/Company Name</label><input type="text" class="form-control" name="name" required></div>
          <div class="mb-3"><label class="form-label">Contact Email</label><input type="email" class="form-control" name="email" required></div>
          <div class="mb-3"><label class="form-label">Address</label><textarea class="form-control" name="address"></textarea></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Client</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>