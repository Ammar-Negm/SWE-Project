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
  <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Staff/dashboard"><i class="bi bi-grid-1x2"></i> My Shift</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/picking"><i class="bi bi-list-check"></i> Batch Pick List</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/packing"><i class="bi bi-box-seam"></i> Packing Station</a>
  <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Staff/qc"><i class="bi bi-shield-check"></i> QC Inspection</a>
</nav>
    <div class="user-info mt-auto">
      <i class="bi bi-person-circle"></i> Logged in as: <span class="php-dynamic text-success">Floor Staff</span>
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
      <div class="row">
        <div class="col-lg-7 mb-4">
          <div class="card p-4 h-100">
            <h5 class="fw-bold mb-3">Items for This Order <span class="text-muted fs-6">(#ORD-88421)</span></h5>
            <ul class="list-group">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <input class="form-check-input me-2" type="checkbox" id="item1">
                  <label class="form-check-label fw-bold php-dynamic" for="item1">Electronic Components (SKU-001)</label>
                  <div class="small text-muted php-dynamic">Weight: 0.5 kg</div>
                </div>
                <span class="badge bg-primary rounded-pill php-dynamic">Qty: 2</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <input class="form-check-input me-2" type="checkbox" id="item2">
                  <label class="form-check-label fw-bold php-dynamic" for="item2">Packaging Tape (SKU-044)</label>
                  <div class="small text-muted php-dynamic">Weight: 0.2 kg</div>
                </div>
                <span class="badge bg-primary rounded-pill php-dynamic">Qty: 1</span>
              </li>
              </ul>
          </div>
        </div>

        <div class="col-lg-5 mb-4">
          <div class="card p-4 mb-4 text-center">
            <h6 class="fw-bold text-muted mb-3">Suggested Box Size</h6>
            <div class="iso-box mb-2">📦</div>
            <h5 class="fw-bold text-primary-custom php-dynamic">Medium (30x25x20 cm)</h5>
          </div>

          <div class="card p-4">
            <h6 class="fw-bold text-muted mb-3">Packed Weight Validator</h6>
            <form method="POST" action="<?= BASE_URL ?>index.php?url=Staff/validateWeight">
              <div class="mb-3 text-center">
                <p class="mb-1 text-muted">Expected: <span class="fw-bold text-dark php-dynamic">1.2 kg</span></p>
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
              <input type="hidden" name="order_id" value="<?= htmlspecialchars($packingItems[0]['picktask_id'] ?? '') ?>">
              <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#labelModal">
                <i class="bi bi-printer"></i> Generate Shipping Label
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="modal fade" id="labelModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Shipping Label Generated</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <div class="border p-4 d-inline-block text-start w-100 bg-white" style="border: 2px dashed #000 !important;">
          <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <h3 class="fw-bold m-0">SHIP TO:</h3>
            <h3 class="fw-bold m-0 text-muted">FEDEX</h3>
          </div>
          <p class="mb-1 php-dynamic"><strong>John Doe</strong></p>
          <p class="mb-1 php-dynamic">123 Logistics Ave.</p>
          <p class="mb-3 php-dynamic">Cairo, 11511, Egypt</p>
          <div class="text-center mb-2">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ORD-88421" alt="QR Code" width="100">
          </div>
          <p class="text-center fw-bold fs-5 mb-0 php-dynamic">ORD-88421</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary w-100"><i class="bi bi-printer"></i> Print</button>
      </div>
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