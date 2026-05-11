<?php
$packingItems = $data['packingItems'] ?? [];
$expectedWeight = $data['expectedWeight'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Packing Station - WareLogix</title>
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
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/picking"><i class="bi bi-list-check"></i> Batch Pick List</a>
      <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Staff/packing"><i class="bi bi-box-seam"></i> Packing Station</a>
      <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/qc"><i class="bi bi-shield-check"></i> QC Inspection</a>
    </nav>
    <div class="user-info mt-auto">
      <i class="bi bi-person-circle"></i> Logged in as:
      <span class="text-success"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Floor Staff') ?></span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">Packing Station</h4>
      </div>
    </nav>

    <div class="container-fluid py-4">

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger">
          <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-lg-7 mb-4">
          <div class="card p-4 h-100">
            <h5 class="fw-bold mb-3">
              Items Ready for Packing
            </h5>

            <ul class="list-group">
              <?php if (!empty($packingItems)): ?>
                <?php foreach ($packingItems as $index => $item): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <input class="form-check-input me-2" type="checkbox" id="item<?= $index ?>">
                      <label class="form-check-label fw-bold" for="item<?= $index ?>">
                        <?= htmlspecialchars($item['product_name'] ?? 'Unknown Product') ?>
                        (<?= htmlspecialchars($item['SKU'] ?? '-') ?>)
                      </label>
                      <div class="small text-muted">
                        Inventory Item ID: <?= htmlspecialchars($item['inv_item_id'] ?? '-') ?>
                      </div>
                    </div>
                    <span class="badge bg-primary rounded-pill">
                      Qty: <?= (int)($item['quantity_to_pick'] ?? 0) ?>
                    </span>
                  </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="list-group-item text-center text-muted">
                  No picked items ready for packing.
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>

        <div class="col-lg-5 mb-4">
          <div class="card p-4 mb-4 text-center">
            <h6 class="fw-bold text-muted mb-3">Suggested Box Size</h6>
            <div class="iso-box mb-2">📦</div>
            <h5 class="fw-bold text-primary-custom">Medium (30x25x20 cm)</h5>
          </div>

          <div class="card p-4">
            <h6 class="fw-bold text-muted mb-3">Packed Weight Validator</h6>

            <form method="POST" action="<?= BASE_URL ?>index.php?url=Staff/validateWeight">
              <input type="hidden" name="expected_weight" value="<?= htmlspecialchars($expectedWeight) ?>">

              <div class="mb-3 text-center">
                <p class="mb-1 text-muted">
                  Expected:
                  <span class="fw-bold text-dark"><?= number_format((float)$expectedWeight, 2) ?> kg</span>
                </p>
              </div>

              <div class="input-group mb-3">
                <input type="number" step="0.01" class="form-control" name="actual_weight" placeholder="Scale weight" required>
                <span class="input-group-text">kg</span>
                <button class="btn btn-outline-secondary" type="button">Read Scale</button>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3">Validate Weight</button>
            </form>

            <hr>

            <form method="POST" action="<?= BASE_URL ?>index.php?url=Staff/generateLabel">
              <input type="hidden" name="order_id" value="<?= htmlspecialchars($packingItems[0]['pick_list_id'] ?? '') ?>">
              <button type="submit" class="btn btn-success w-100" <?= empty($packingItems) ? 'disabled' : '' ?>>
                <i class="bi bi-printer"></i> Generate Shipping Label
              </button>
            </form>
          </div>
        </div>
      </div>

      <?php if (!empty($packingItems)): ?>
        <div class="card p-4 mt-3">
          <h5 class="fw-bold mb-3">Preview Shipping Label</h5>
          <div class="border p-4 d-inline-block text-start w-100 bg-white" style="border: 2px dashed #000 !important;">
            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
              <h3 class="fw-bold m-0">SHIP TO:</h3>
              <h3 class="fw-bold m-0 text-muted">FEDEX</h3>
            </div>
            <p class="mb-1"><strong>Order Packing Batch</strong></p>
            <p class="mb-1">Warehouse Dispatch</p>
            <p class="mb-3">Generated from Pick List #<?= htmlspecialchars($packingItems[0]['pick_list_id'] ?? '-') ?></p>
            <div class="text-center mb-2">
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ORD-<?= urlencode($packingItems[0]['pick_list_id'] ?? '') ?>" alt="QR Code" width="100">
            </div>
            <p class="text-center fw-bold fs-5 mb-0">ORD-<?= htmlspecialchars($packingItems[0]['pick_list_id'] ?? '-') ?></p>
          </div>
        </div>
      <?php endif; ?>

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
</script>
</body>
</html>