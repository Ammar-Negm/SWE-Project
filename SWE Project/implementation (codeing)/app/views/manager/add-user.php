<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add New User - WareLogix</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<div id="wrapper">
<aside class="sidebar" id="sidebar">
<div class="brand">⬡ WareLogix</div>
<nav class="nav flex-column mt-3">
    <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
    <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/inventory"><i class="bi bi-box-seam"></i> Inventory</a>
    <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/zonaloptimizer"><i class="bi bi-layers"></i> Zonal Optimizer</a>
    <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/procurement"><i class="bi bi-cart3"></i> Procurement</a>
    <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/supplier"><i class="bi bi-truck"></i> Suppliers</a>
    <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/analytics"><i class="bi bi-graph-up"></i> Analytics</a>
    <a class="nav-link active" href="<?= BASE_URL ?>index.php?url=Manager/adduser"><i class="bi bi-person-plus"></i> Add User</a>
    <a class="nav-link" href="<?= BASE_URL ?>index.php?url=Manager/systemadmin"><i class="bi bi-gear"></i> System Admin</a>
</nav>
<div class="user-info mt-auto">
    <i class="bi bi-person-circle"></i> Logged in as: <span class="php-dynamic text-warning">Manager</span>
</div>
</aside>

<main class="main-content">
<nav class="top-navbar">
    <div class="d-flex align-items-center">
    <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
    <h4 class="mb-0 fw-bold">User Management</h4>
    </div>
    <div class="d-flex align-items-center gap-3">
    <div class="position-relative cursor-pointer">
        <i class="bi bi-bell fs-5"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">5</span>
    </div>
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <img src="https://ui-avatars.com/api/?name=Admin&background=1A3C5E&color=fff" class="rounded-circle" width="32">
        </button>
    </div>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
    <div class="col-lg-8">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">Create New User Account</h4>
        <a href="<?= BASE_URL ?>index.php?url=Manager/systemadmin" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Admin</a>
        </div>

        <div class="card p-4 shadow-sm">
        <form method="POST" action="process_add_user.php">
            
            <h6 class="fw-bold text-primary-custom border-bottom pb-2 mb-3">1. Personal Information</h6>
            <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="full_name" required placeholder="e.g., Ahmed Ali">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" required placeholder="user@warelogix.com">
            </div>
            </div>

            <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="text" class="form-control" name="phone" placeholder="+20 100 000 0000">
            </div>
            </div>

            <h6 class="fw-bold text-primary-custom border-bottom pb-2 mb-3">2. Role & Access Level</h6>
            <div class="mb-4">
            <label class="form-label">Assign Role</label>
            <select class="form-select border-primary" name="role" id="roleSelector" required>
                <option value="" selected disabled>Select a role...</option>
                <option value="staff">Floor Staff</option>
                <option value="supplier">Supplier</option>
            </select>
            </div>

            <div class="mb-4 p-3 bg-light rounded d-none" id="staffFields">
            <label class="form-label fw-bold text-success"><i class="bi bi-person-badge"></i> Floor Staff Configuration</label>
            <div class="row">
                <div class="col-md-6">
                <label class="form-label">Assigned Shift</label>
                <select class="form-select" name="shift">
                    <option value="morning">Morning (08:00 AM - 04:00 PM)</option>
                    <option value="evening">Evening (04:00 PM - 12:00 AM)</option>
                    <option value="night">Night (12:00 AM - 08:00 AM)</option>
                </select>
                </div>
                <div class="col-md-6">
                <label class="form-label">Primary Zone</label>
                <select class="form-select" name="primary_zone">
                    <option value="A">Zone A</option>
                    <option value="B">Zone B</option>
                    <option value="C">Zone C</option>
                </select>
                </div>
            </div>
            </div>

            <div class="mb-4 p-3 bg-light rounded d-none" id="supplierFields">
            <label class="form-label fw-bold text-info"><i class="bi bi-building"></i> Supplier Configuration</label>
            <div class="row">
                <div class="col-md-12 mb-2">
                <label class="form-label">Company Name</label>
                <input type="text" class="form-control" name="company_name" placeholder="e.g., AlphaParts Ltd.">
                </div>
                <div class="col-md-12">
                <label class="form-label">Tax ID / Commercial Register</label>
                <input type="text" class="form-control" name="tax_id" placeholder="Optional">
                </div>
            </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 border-top pt-3">
            <button type="reset" class="btn btn-light px-4">Clear Form</button>
            <button type="submit" class="btn btn-primary px-5 fw-bold">Create User</button>
            </div>

        </form>
        </div>

    </div>
    </div>
</div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', function() {
document.getElementById('sidebar').classList.toggle('show');
});

// UX Logic: Show/Hide extra fields based on selected role
const roleSelector = document.getElementById('roleSelector');
const staffFields = document.getElementById('staffFields');
const supplierFields = document.getElementById('supplierFields');

roleSelector.addEventListener('change', function() {
// Hide both initially
staffFields.classList.add('d-none');
supplierFields.classList.add('d-none');

// Show the relevant one
if (this.value === 'staff') {
    staffFields.classList.remove('d-none');
} else if (this.value === 'supplier') {
    supplierFields.classList.remove('d-none');
}
});
</script>
</body>
</html>