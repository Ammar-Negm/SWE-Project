<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Batch Pick List - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
    <nav class="nav flex-column mt-3">
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/dashboard"><i class="bi bi-grid-1x2"></i> My Shift</a>
      <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Staff/picking"><i class="bi bi-list-check"></i> Batch Pick List</a>
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/packing"><i class="bi bi-box-seam"></i> Packing Station</a>
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/qc"><i class="bi bi-shield-check"></i> QC Inspection</a>
    </nav>
    <div class="user-info mt-auto">
      <i class="bi bi-person-circle"></i> Logged in as:
      <span class="text-success"><?= $_SESSION['user_name'] ?? 'Floor Staff' ?></span>
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
      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Batch Pick List</h4>
        <span class="badge bg-primary fs-6">Assigned Tasks</span>
      </div>

      <div class="list-group shadow-sm mb-5">
        <?php if (!empty($lists)): ?>
          <?php foreach ($lists as $item): ?>
            <label class="list-group-item d-flex justify-content-between align-items-center p-3 cursor-pointer">
              <div class="d-flex align-items-center gap-3">
                <input class="form-check-input fs-4 m-0" type="checkbox" value="picked">
                <div>
                  <span class="badge bg-dark mb-1">Task #<?= htmlspecialchars($item['picktask_id']) ?></span>
                  <h6 class="mb-0 fw-bold"><?= htmlspecialchars($item['product_name'] ?? '-') ?> (<?= htmlspecialchars($item['SKU'] ?? '-') ?>)</h6>
                </div>
              </div>
              <div class="text-end">
                <h5 class="mb-0 fw-bold text-primary-custom">x <?= htmlspecialchars($item['quantity_to_pick'] ?? 0) ?></h5>
                <small class="text-muted"><?= htmlspecialchars($item['task_status'] ?? 'Open') ?></small>
              </div>

              <form method="POST" action="<?= BASE_URL ?>index.php?url=Staff/updateTask/<?= $item['picktask_id'] ?>" class="ms-3">
                <input type="hidden" name="action" value="complete">
                <button type="submit" class="btn btn-success btn-sm">Complete</button>
              </form>
            </label>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="list-group-item text-center text-muted py-4">No assigned pick tasks found.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="fixed-bottom bg-white border-top p-3 shadow-lg d-flex justify-content-between align-items-center staff-action-bar" style="z-index: 1020;">
      <div class="w-25">
        <small class="fw-bold mb-1 d-block">
          Items Confirmed:
          <span><?= !empty($lists) ? count($lists) : 0 ?></span>
        </small>
        <div class="progress" style="height: 8px;">
          <div class="progress-bar bg-success" style="width: 20%;"></div>
        </div>
      </div>
      <div class="gap-2 d-flex">
        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#missingItemModal">Report Missing Item</button>
        <form method="POST" action="<?= BASE_URL ?>index.php?url=Staff/completePick">
          <button type="submit" class="btn btn-success fw-bold px-4">Complete Pick</button>
        </form>
      </div>
    </div>
  </main>
</div>

<div class="modal fade" id="missingItemModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="<?= BASE_URL ?>index.php?url=Staff/reportMissing">
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
            <label class="form-label">Bin Location</label>
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
  const toggleBtn = document.getElementById('sidebarToggle');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('show');
    });
  }
</script>
</body>
</html>