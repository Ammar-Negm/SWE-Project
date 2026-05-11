<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Admin - WareLogix</title>
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
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/clients"><i class="bi bi-people"></i> Clients</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/adduser"><i class="bi bi-person-plus"></i> Add User</a>
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/systemadmin"><i class="bi bi-gear"></i> System Admin</a>
</nav>
    <div class="user-info mt-auto">
      <i class="bi bi-person-circle"></i> Logged in as: <span class="php-dynamic text-warning">Manager</span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">System Administration</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="position-relative cursor-pointer">
          <a href="<?= BASE_URL ?>index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <h4 class="fw-bold mb-4">System Administration</h4>

      <div class="accordion" id="adminAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
              Role & Access Control
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#adminAccordion">
            <div class="accordion-body p-0">
              <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Role</th><th>Accessible Modules</th><th>Action</th></tr></thead>
                <tbody>
                  <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                      <tr>
                        <td><?= htmlspecialchars($user['role'] ?? 'User') ?></td>
                        <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoleModal">
                            Edit
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="3" class="text-center text-muted">No users found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
              IoT Device Heartbeat Monitor
            </button>
          </h2>
          <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#adminAccordion">
            <div class="accordion-body">
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="card p-3 border-0 shadow-sm bg-light">
                    <div class="d-flex justify-content-between">
                      <h6 class="mb-1"><i class="bi bi-upc-scan me-2"></i>Scanner #1</h6>
                      <span class="pulse-dot pulse-green mt-1"></span>
                    </div>
                    <small class="text-muted">Last Ping: Just now</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card p-3 border-0 shadow-sm bg-light border-danger">
                    <div class="d-flex justify-content-between">
                      <h6 class="mb-1"><i class="bi bi-upc-scan me-2"></i>Scanner #4</h6>
                      <span class="pulse-dot pulse-red mt-1"></span>
                    </div>
                    <small class="text-danger">Last Ping: 45 mins ago (Offline)</small>
                  </div>
                </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Edit Role Permissions: <span class="text-primary-custom">Manager</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>index.php?url=Manager/systemadmin">
          <div class="modal-body">
            <p class="text-muted small">Select the modules this role can access:</p>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="mod1" checked>
              <label class="form-check-label" for="mod1">Dashboard & Analytics</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="mod2" checked>
              <label class="form-check-label" for="mod2">Inventory Management</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="mod3" checked>
              <label class="form-check-label" for="mod3">Procurement & POs</label>
            </div>
            <div class="form-check form-switch mb-2">
              <input class="form-check-input" type="checkbox" id="mod4" checked>
              <label class="form-check-label" for="mod4">System Administration</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Role</button>
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