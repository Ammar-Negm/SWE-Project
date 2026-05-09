<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard - WareLogix</title>
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
      <i class="bi bi-person-circle"></i> Logged in as:
      <span class="text-success"><?= $_SESSION['user_name'] ?? 'Staff' ?></span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <h4 class="mb-0 fw-bold">My Dashboard</h4>
      <a href="<?= BASE_URL ?>index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
    </nav>

    <div class="container-fluid py-4">
      <h4 class="fw-bold mb-4">My Active Tasks</h4>

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <div class="row mb-5">
        <?php if (!empty($activeTasks)): ?>
          <?php foreach (array_slice($activeTasks, 0, 6) as $task): ?>
            <div class="col-md-4 mb-3">
              <div class="card p-4 h-100 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h5 class="fw-bold">Task #<?= htmlspecialchars($task['picktask_id']) ?></h5>
                  <span class="badge bg-primary"><?= htmlspecialchars($task['task_status'] ?? 'Open') ?></span>
                </div>
                <p class="mb-1 text-muted">
                  <i class="bi bi-box-seam"></i>
                  Product: <strong><?= htmlspecialchars($task['product_name'] ?? '-') ?></strong>
                </p>
                <p class="mb-3 text-muted">
                  <i class="bi bi-list-ol"></i>
                  Qty to Pick: <strong><?= htmlspecialchars($task['quantity_to_pick'] ?? 0) ?></strong>
                </p>

                <form method="POST" action="<?= BASE_URL ?>index.php?url=Staff/updateTask/<?= $task['picktask_id'] ?>">
                  <input type="hidden" name="action" value="complete">
                  <button class="btn btn-primary w-100 fw-bold mt-auto">Start / Complete Task</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-info">No active tasks assigned yet.</div>
          </div>
        <?php endif; ?>
      </div>

      <h4 class="fw-bold mb-3">My Shift Summary</h4>
      <div class="card p-4">
        <div class="row text-center mb-4">
          <div class="col-4 border-end">
            <h2 class="fw-bold" style="color:#1A3C5E"><?= $completedCount ?? 0 ?></h2>
            <span class="text-muted">Tasks Completed</span>
          </div>
          <div class="col-4 border-end">
            <h2 class="fw-bold" style="color:#1A3C5E"><?= $avgSecondsPerPick ?? 0 ?>s</h2>
            <span class="text-muted">Avg. Seconds/Pick</span>
          </div>
          <div class="col-4">
            <h2 class="fw-bold" style="color:#1A3C5E"><?= $distanceWalked ?? 0 ?> km</h2>
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