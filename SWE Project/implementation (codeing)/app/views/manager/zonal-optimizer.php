<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Zonal Optimizer - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">

  <style>
    .zonal-grid {
      display: grid;
      grid-template-columns: repeat(6, minmax(90px, 1fr));
      gap: 12px;
    }

    .zone-cell {
      min-height: 90px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      transition: transform 0.2s ease;
    }

    .zone-cell:hover {
      transform: translateY(-2px);
    }

    .zone-green {
      background-color: #35c685;
      color: #fff;
    }

    .zone-yellow {
      background-color: #f5c542;
      color: #212529;
    }

    .zone-red {
      background-color: #dc3545;
      color: #fff;
    }
  </style>
</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
    <nav class="nav flex-column mt-3">
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/inventory"><i class="bi bi-box-seam"></i> Inventory</a>
      <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/zonaloptimizer"><i class="bi bi-layers"></i> Zonal Optimizer</a>
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
        <h4 class="mb-0 fw-bold">Zonal Storage Optimizer</h4>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="<?= BASE_URL ?>index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>
    </nav>

    <div class="container-fluid py-4">

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <h5 class="fw-bold mb-3">Live Warehouse Floor Grid</h5>

      <div class="card p-4 mb-4 overflow-auto">
        <div class="zonal-grid" style="min-width: 600px;">
          <?php if (!empty($zones)): ?>
            <?php foreach ($zones as $zone): ?>
              <?php
                $zoneName  = $zone['zone_name'] ?? 'Unknown Zone';
                $maxCap    = (float)($zone['max_capacity'] ?? 0);
                $current   = (float)($zone['current_load'] ?? 0);
                $totalBins = (int)($zone['total_bins'] ?? 0);
                $util      = (int)($zone['utilization_percent'] ?? 0);

                if ($util >= 85) {
                  $zoneClass = 'zone-red';
                } elseif ($util >= 60) {
                  $zoneClass = 'zone-yellow';
                } else {
                  $zoneClass = 'zone-green';
                }

                $tooltip = htmlspecialchars($zoneName)
                  . '<br>Utilization: ' . $util . '%'
                  . '<br>Current Load: ' . $current
                  . '<br>Max Capacity: ' . $maxCap
                  . '<br>Bins: ' . $totalBins;
              ?>
              <div
                class="zone-cell <?= $zoneClass ?>"
                data-bs-toggle="tooltip"
                data-bs-html="true"
                title="<?= $tooltip ?>">
                <?= htmlspecialchars($zoneName) ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="alert alert-info w-100 mb-0">No zones found.</div>
          <?php endif; ?>
        </div>
      </div>

      <h5 class="fw-bold mb-3">Optimizer Suggestions</h5>

      <div class="row">
        <?php if (!empty($suggestions)): ?>
          <?php foreach ($suggestions as $item): ?>
            <div class="col-md-4 mb-3">
              <div class="card p-3 border-start border-warning border-4">
                <h6 class="fw-bold"><?= htmlspecialchars($item['zone_name'] ?? 'Unknown Zone') ?></h6>
                <p class="mb-1">
                  <span class="badge bg-secondary">
                    Current: <?= htmlspecialchars($item['current'] ?? 'Unknown') ?>
                  </span>
                  <i class="bi bi-arrow-right"></i>
                  <span class="badge bg-success">
                    <?= htmlspecialchars($item['suggested'] ?? 'Suggested Allocation') ?>
                  </span>
                </p>
                <small class="text-muted"><?= htmlspecialchars($item['reason'] ?? 'No reason provided.') ?></small>

                <form method="POST" action="<?= BASE_URL ?>index.php?url=Manager/zonaloptimizer" class="mt-2">
                  <button type="submit" class="btn btn-sm btn-outline-primary w-100">Approve Move</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-info">No optimizer suggestions at the moment.</div>
          </div>
        <?php endif; ?>
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

  document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>
</body>
</html>