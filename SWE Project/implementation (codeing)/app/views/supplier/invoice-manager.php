<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice Manager - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/Php project/SWE-Project/SWE Project/implementation (codeing)/public/assets/css/style.css">
</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
    <nav class="nav flex-column mt-3">
  <a class="nav-link active" href="index.php?url=Supplier/dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
  <a class="nav-link" href="index.php?url=Supplier/orders"><i class="bi bi-cart3"></i> Purchase Orders</a>
  <a class="nav-link" href="index.php?url=Supplier/invoice"><i class="bi bi-receipt"></i> Invoice Manager</a>
</nav>
    <div class="user-info mt-auto">
      <i class="bi bi-building"></i> Logged in as: <span class="php-dynamic text-info">AlphaParts Ltd.</span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">Invoice Manager</h4>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <ul class="nav nav-tabs mb-4" id="invoiceTabs" role="tablist">
        <li class="nav-item">
          <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#submit-invoice" type="button">Submit Invoice</button>
        </li>
        <li class="nav-item">
          <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#invoice-history" type="button">Invoice History</button>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="submit-invoice">
          <div class="card p-4">
            <form method="POST" action="submit_invoice.php" enctype="multipart/form-data">
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label">Related PO #</label>
                  <select class="form-select" name="po_id" required>
                    <option value="">Select PO...</option>
                    <option value="PO-20045">PO-20045</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Invoice #</label>
                  <input type="text" class="form-control" name="invoice_number" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Invoice Date</label>
                  <input type="date" class="form-control" name="invoice_date" required>
                </div>
              </div>
              
              <label class="form-label mt-3">Line Items</label>
              <table class="table table-bordered table-sm mb-3">
                <thead class="table-light"><tr><th>SKU</th><th>Unit Price</th><th>Qty</th></tr></thead>
                <tbody>
                  <tr>
                    <td><input type="text" class="form-control form-control-sm" name="sku[]" value="SKU-001"></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="price[]" value="5.00"></td>
                    <td><input type="number" class="form-control form-control-sm" name="qty[]" value="500"></td>
                  </tr>
                </tbody>
              </table>
              
              <div class="d-flex justify-content-between align-items-end mb-4">
                <div class="w-50">
                  <label class="form-label">Upload PDF Invoice</label>
                  <input class="form-control" type="file" name="invoice_pdf" accept=".pdf" required>
                </div>
                <h4 class="mb-0">Total: <span class="fw-bold text-primary-custom">$2,500.00</span></h4>
              </div>
              <button type="submit" class="btn btn-primary w-100 fw-bold">Submit Invoice</button>
            </form>
          </div>
        </div>

        <div class="tab-pane fade" id="invoice-history">
          <div class="card p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr><th>Invoice #</th><th>Related PO</th><th>Date</th><th>Amount</th><th>Match Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span class="fw-bold php-dynamic">INV-099</span></td>
                    <td><span class="php-dynamic">PO-20030</span></td>
                    <td><span class="php-dynamic">Oct 02, 2025</span></td>
                    <td><span class="php-dynamic">$1,800.00</span></td>
                    <td><span class="badge bg-success">Matched ✓</span></td>
                    <td><button class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> PDF</button></td>
                  </tr>
                  <tr>
                    <td><span class="fw-bold php-dynamic">INV-102</span></td>
                    <td><span class="php-dynamic">PO-20045</span></td>
                    <td><span class="php-dynamic">Oct 14, 2025</span></td>
                    <td><span class="php-dynamic">$4,250.00</span></td>
                    <td><span class="badge bg-warning text-dark">Discrepancy ⚠</span></td>
                    <td><button class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> PDF</button></td>
                  </tr>
                  </tbody>
              </table>
            </div>
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