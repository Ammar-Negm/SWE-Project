<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Batch Pick List - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>
    /* Custom style for sticky footer so it doesn't overlap sidebar on desktop */
    @media (min-width: 768px) {
      .staff-action-bar { left: 260px; }
    }
    @media (max-width: 767.98px) {
      .staff-action-bar { left: 0; }
    }
  </style>
</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
   <nav class="nav flex-column mt-3">
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Staff/dashboard"><i class="bi bi-grid-1x2"></i> My Shift</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/picking"><i class="bi bi-list-check"></i> Batch Pick List</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/packing"><i class="bi bi-box-seam"></i> Packing Station</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/qc"><i class="bi bi-shield-check"></i> QC Inspection</a>
</nav>
    <div class="user-info mt-auto">
      <i class="bi bi-person-circle"></i> Logged in as: <span class="php-dynamic text-success">Floor Staff</span>
    </div>
  </aside>

  <main class="main-content position-relative pb-5">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">Active Pick Task</h4>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Batch Pick List</h4>
        <span class="badge bg-primary fs-6">Order Group #BPG-0047</span>
      </div>

      <div class="list-group shadow-sm mb-5">
        <label class="list-group-item d-flex justify-content-between align-items-center p-3 cursor-pointer">
          <div class="d-flex align-items-center gap-3">
            <input class="form-check-input fs-4 m-0" type="checkbox" value="picked" name="item_1">
            <div>
              <span class="badge bg-dark mb-1 php-dynamic">Zone-A-12</span>
              <h6 class="mb-0 fw-bold php-dynamic">Electronic Components (SKU-001)</h6>
            </div>
          </div>
          <div class="text-end">
            <h5 class="mb-0 fw-bold text-primary-custom php-dynamic">x 2</h5>
            <small class="text-muted">To Pick</small>
          </div>
        </label>
        
        <label class="list-group-item d-flex justify-content-between align-items-center p-3 cursor-pointer">
          <div class="d-flex align-items-center gap-3">
            <input class="form-check-input fs-4 m-0" type="checkbox" value="picked" name="item_2">
            <div>
              <span class="badge bg-dark mb-1 php-dynamic">Zone-B-05</span>
              <h6 class="mb-0 fw-bold php-dynamic">Packaging Tape (SKU-044)</h6>
            </div>
          </div>
          <div class="text-end">
            <h5 class="mb-0 fw-bold text-primary-custom php-dynamic">x 5</h5>
            <small class="text-muted">To Pick</small>
          </div>
        </label>
        </div>
    </div>

    <div class="fixed-bottom bg-white border-top p-3 shadow-lg d-flex justify-content-between align-items-center staff-action-bar" style="z-index: 1020;">
      <div class="w-25">
        <small class="fw-bold mb-1 d-block">Items Confirmed: <span class="php-dynamic">2/10</span></small>
        <div class="progress" style="height: 8px;">
          <div class="progress-bar bg-success" style="width: 20%;"></div>
        </div>
      </div>
      <div class="gap-2 d-flex">
        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#missingItemModal">Report Missing Item</button>
        <form method="POST" action="complete_pick.php">
          <button type="submit" class="btn btn-success fw-bold px-4">Complete Pick</button>
        </form>
      </div>
    </div>

  </main>
</div>

<div class="modal fade" id="missingItemModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="report_missing.php">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Report Missing Item</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">SKU</label>
            <input type="text" class="form-control" name="sku" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Bin Location (Where you checked)</label>
            <input type="text" class="form-control" name="bin_location" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="notes" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Submit Report</button>
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