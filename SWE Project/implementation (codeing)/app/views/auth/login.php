<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- <link rel="stylesheet" href="style.css"> -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">

</head>
<body class="login-bg">

  <div class="card p-5" style="width: 100%; max-width: 400px;">
    <div class="text-center mb-4">
      <h2 class="text-primary-custom fw-bold">⬡ WareLogix</h2>
      <p class="text-muted">Intelligent Fulfillment, Simplified.</p>
    </div>

    <!-- <form method="POST" action="process_login.php"> -->
      <form method="POST" action="<?= BASE_URL ?>Auth/login">
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" required placeholder="user@warelogix.com">
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <div class="mb-4">
        <label for="role" class="form-label">Select Role</label>
        <select class="form-select" id="role" name="role">
          <option value="manager">Manager</option>
          <option value="staff">Floor Staff</option>
          <option value="supplier">Supplier</option>
        </select>
      </div>
      <?php if (!empty($data['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($data['error']) ?>
    </div>
<?php endif; ?>
      <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Sign In</button>
    </form>
  </div>
<style>
  body {
    background-color: #1A3C5E;
    background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.03) 0px, rgba(255,255,255,0.03) 2px, transparent 2px, transparent 50px);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
  }
  .text-primary-custom { color: #1A3C5E !important; }
  .btn-primary { background-color: #1A3C5E; border-color: #1A3C5E; }
  .btn-primary:hover { background-color: #122b44; border-color: #122b44; }
</style>
</body>
</html>