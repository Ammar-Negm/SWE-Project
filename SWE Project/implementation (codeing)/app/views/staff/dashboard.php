<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #F1F5F9; }
    #wrapper { display: flex; width: 100%; height: 100vh; }
    .sidebar { width: 260px; background-color: #1A3C5E; color: #fff; display: flex; flex-direction: column; }
    .sidebar .brand { font-size: 1.5rem; font-weight: 700; padding: 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 1rem 1.5rem; display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: rgba(255,255,255,0.1); border-left: 4px solid #F59E0B; }
    .sidebar .user-info { margin-top: auto; padding: 1rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.9rem; }
    .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
    .top-navbar { background: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
    .card { background-color: #fff; border: none; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
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
      <i class="bi bi-person-circle"></i> Logged in as:
      <span class="text-success"><?= $_SESSION['user_name'] ?? 'Staff' ?></span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <h4 class="mb-0 fw-bold">My Dashboard</h4>
      <a href="index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
    </nav>

    <div class="container-fluid py-4">
      <h4 class="fw-bold mb-4">My Active Tasks</h4>
      <div class="row mb-5">
        <div class="col-md-4 mb-3">
          <div class="card p-4 h-100 border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="fw-bold">Order #ORD-88421</h5>
              <span class="badge bg-danger">High Priority</span>
            </div>
            <p class="mb-1 text-muted"><i class="bi bi-box-seam"></i> Items to Pick: <strong>12</strong></p>
            <p class="mb-3 text-muted"><i class="bi bi-geo-alt"></i> Zone: <strong>A & B</strong></p>
            <button class="btn btn-primary w-100 fw-bold mt-auto">Start Picking</button>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card p-4 h-100 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="fw-bold">Order #ORD-88422</h5>
              <span class="badge bg-primary">Normal</span>
            </div>
            <p class="mb-1 text-muted"><i class="bi bi-box-seam"></i> Items to Pick: <strong>5</strong></p>
            <p class="mb-3 text-muted"><i class="bi bi-geo-alt"></i> Zone: <strong>C</strong></p>
            <button class="btn btn-primary w-100 fw-bold mt-auto">Start Picking</button>
          </div>
        </div>
      </div>

      <h4 class="fw-bold mb-3">My Shift Summary</h4>
      <div class="card p-4">
        <div class="row text-center mb-4">
          <div class="col-4 border-end">
            <h2 class="fw-bold" style="color:#1A3C5E">24</h2>
            <span class="text-muted">Tasks Completed</span>
          </div>
          <div class="col-4 border-end">
            <h2 class="fw-bold" style="color:#1A3C5E">45s</h2>
            <span class="text-muted">Avg. Seconds/Pick</span>
          </div>
          <div class="col-4">
            <h2 class="fw-bold" style="color:#1A3C5E">1.2 km</h2>
            <span class="text-muted">Distance Walked</span>
          </div>
        </div>
        <div class="text-center">
          <button class="btn btn-danger px-5 fw-bold"><i class="bi bi-stop-circle"></i> Clock Out</button>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>