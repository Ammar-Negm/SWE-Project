<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supplier Dashboard - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #F1F5F9; }
    #wrapper { display: flex; width: 100%; height: 100vh; }
    .sidebar { width: 260px; background-color: #1A3C5E; color: #fff; display: flex; flex-direction: column; }
    .sidebar .brand { font-size: 1.5rem; font-weight: 700; padding: 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 1rem 1.5rem; display: flex; align-items: center; gap: 10px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: rgba(255,255,255,0.1); border-left: 4px solid #F59E0B; }
    .sidebar .user-info { margin-top: auto; padding: 1rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.9rem; }
    .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
    .top-navbar { background: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
    .card { background-color: #fff; border: none; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
    .bg-primary-custom { background-color: #1A3C5E !important; }
  </style>
</head>
<body>
<div id="wrapper">
  <aside class="sidebar" id="sidebar">
    <div class="brand">⬡ WareLogix</div>
    
    </nav> 
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
      <a href="index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
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
            <h2 class="fw-bold">4</h2>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card p-3 h-100 shadow-sm border-start border-success border-4">
            <h6 class="text-muted">Pending Invoices</h6>
            <h2 class="fw-bold">2</h2>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 h-100 shadow-sm border-start border-info border-4">
            <h6 class="text-muted">Avg. Lead Time</h6>
            <h2 class="fw-bold">4.2 <span class="fs-6 text-muted">days</span></h2>
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
              <tr>
                <td class="fw-bold">PO-20045</td>
                <td>Oct 12, 2025</td>
                <td>$4,250.00</td>
                <td><span class="badge bg-warning text-dark">In Transit</span></td>
              </tr>
              <tr>
                <td class="fw-bold">PO-20030</td>
                <td>Oct 01, 2025</td>
                <td>$1,800.00</td>
                <td><span class="badge bg-success">Delivered</span></td>
              </tr>
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