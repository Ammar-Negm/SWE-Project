<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QC Inspection - WareLogix</title>
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
        <h4 class="mb-0 fw-bold">Inbound QC</h4>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">QC Inspection</h4>
        <span class="badge bg-dark fs-6">PO #PO-20045</span>
      </div>

      <div class="card p-3 mb-4 bg-light text-center border-0">
        <div class="d-flex justify-content-between align-items-center position-relative">
          <div class="progress position-absolute w-100" style="height: 4px; z-index: 1; top: 50%; transform: translateY(-50%);">
            <div class="progress-bar bg-success" style="width: 50%;"></div>
          </div>
          <div class="position-relative z-3 bg-success text-white rounded-pill px-3 py-1 fw-bold">Expected</div>
          <div class="position-relative z-3 bg-success text-white rounded-pill px-3 py-1 fw-bold">At Dock</div>
          <div class="position-relative z-3 bg-warning text-dark rounded-pill px-3 py-1 fw-bold border border-warning shadow-sm">Being Inspected</div>
          <div class="position-relative z-3 bg-secondary text-white rounded-pill px-3 py-1 fw-bold">Stored</div>
        </div>
      </div>

      <form method="POST" action="<?= BASE_URL ?>index.php?url=Staff/QC1">
        <input type="hidden" name="po_id" value="PO-20045">
        <div class="card p-0 mb-4">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
              <thead class="table-light">
                <tr>
                  <th class="text-start">SKU / Product</th>
                  <th>Ordered Qty</th>
                  <th>Received Qty</th>
                  <th>Condition</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-start">
                    <strong class="php-dynamic">SKU-001</strong><br>
                    <small class="text-muted php-dynamic">Electronic Components</small>
                  </td>
                  <td><span class="php-dynamic">500</span></td>
                  <td><input type="number" class="form-control form-control-sm mx-auto" style="width: 80px;" name="recv_qty[]" value="500"></td>
                  <td>
                    <select class="form-select form-select-sm mx-auto" style="width: 120px;" name="condition[]">
                      <option value="good">Good</option>
                      <option value="damaged">Damaged</option>
                    </select>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <input type="radio" class="btn-check" name="status_1" id="approve_1" value="approve" checked>
                      <label class="btn btn-outline-success btn-sm" for="approve_1"><i class="bi bi-check-lg"></i></label>
                      <input type="radio" class="btn-check" name="status_1" id="reject_1" value="reject">
                      <label class="btn btn-outline-danger btn-sm" for="reject_1"><i class="bi bi-x-lg"></i></label>
                    </div>
                  </td>
                </tr>
                </tbody>
            </table>
          </div>
        </div>
        <div class="text-end">
          <button type="submit" class="btn btn-primary px-5 fw-bold">Submit QC Report</button>
        </div>
      </form>
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