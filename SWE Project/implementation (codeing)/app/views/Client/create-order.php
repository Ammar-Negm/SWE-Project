<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Place New Order - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>
    :root { --client-primary: #10B981; }
    .sidebar-client { background-color: #064E3B !important; }
    .btn-client { background-color: var(--client-primary); border-color: var(--client-primary); color: white; }
    .btn-client:hover { background-color: #059669; color: white; }
    .product-row { transition: all 0.3s ease; }
  </style>
</head>
<body>
<div id="wrapper">
  <aside class="sidebar sidebar-client" id="sidebar">
    <div class="brand">⬡ Client Portal</div>
    <nav class="nav flex-column mt-3">
      <a class="nav-link active" href="<?= BASE_URL ?>Client/createOrder"><i class="bi bi-cart-plus"></i> New Order</a>
    </nav>
    <div class="user-info mt-auto">
      <i class="bi bi-shop"></i> Client: <span class="php-dynamic">Hameed Retail Stores</span>
    </div>
  </aside>

  <main class="main-content">
    <nav class="top-navbar">
      <div class="d-flex align-items-center">
        <button class="btn btn-light d-md-none me-3" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h4 class="mb-0 fw-bold">Create Fulfillment Request</h4>
      </div>
      <a href="index.php?url=Auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
    </nav>
    <div class="container-fluid mt-3">
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                <div>
                    <strong>Order Submitted Successfully!</strong><br>
                    Your request has been sent to the warehouse fulfillment team.
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          
          <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Order Construction</h5>
                <span class="badge bg-light text-dark">Date: <?= date('Y-m-d') ?></span>
            </div>
            
            <form method="POST" action="<?= BASE_URL ?>Client/submitOrder">
              <input type="hidden" name="client_id" value="<?= $_SESSION['user_id'] ?? '' ?>">

              <div id="product-list">
                <div class="row mb-3 product-row align-items-end border-bottom pb-3">
                  <div class="col-md-5">
                    <label class="form-label small fw-bold">Select Product</label>
                    <select class="form-select product-select" name="product_id[]" required onchange="calculateTotals()">
                      <option value="" selected disabled>Choose item...</option>
                      <?php if(isset($products) && is_array($products)): foreach($products as $product): ?>
                          <option value="<?= $product['product_id'] ?>" 
                                  data-price="<?= $product['basePrice'] ?? 0 ?>" 
                                  data-weight="1.0"> <?= htmlspecialchars($product['name']) ?> (SKU: <?= $product['SKU'] ?>) - $<?= number_format($product['basePrice'], 2) ?>
                          </option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label small fw-bold">Quantity</label>
                    <input type="number" class="form-control qty-input" name="qty[]" min="1" value="1" required oninput="calculateTotals()">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label small fw-bold">Subtotal</label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="text" class="form-control bg-light subtotal-display" readonly value="0.00">
                    </div>
                  </div>
                  <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-row" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                  </div>
                </div>
              </div>

              <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-more-products">
                <i class="bi bi-plus-circle"></i> Add Another Product
              </button>

              <hr class="my-4">

              <div class="row justify-content-end">
                <div class="col-md-5">
                  <div class="card bg-light border-0 p-3">
                    <div class="d-flex justify-content-between mb-2">
                      <span class="text-muted">Total Estimated Weight:</span>
                      <strong id="display-weight">0.0 kg</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="fs-5 fw-bold">Total Cost:</span>
                      <h3 class="fw-bold text-success mb-0">$<span id="display-cost">0.00</span></h3>
                    </div>
                    
                    <input type="hidden" name="total_weight" id="input-total-weight" value="0">
                    <input type="hidden" name="total_cost" id="input-total-cost" value="0">
                  </div>
                </div>
              </div>

              <div class="mt-4 text-end">
                <button type="submit" class="btn btn-client px-5 py-2 fw-bold shadow-sm">
                  <i class="bi bi-send-check-fill me-2"></i> Submit Order to Warehouse
                </button>
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
  // تفعيل الـ Sidebar Toggle
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
  });

  // إضافة منتج جديد
  document.getElementById('add-more-products').addEventListener('click', function() {
    const productList = document.getElementById('product-list');
    const firstRow = document.querySelector('.product-row');
    const newRow = firstRow.cloneNode(true);
    
    // تفريغ القيم في السطر الجديد
    newRow.querySelector('select').value = "";
    newRow.querySelector('.qty-input').value = 1;
    newRow.querySelector('.subtotal-display').value = "0.00";
    
    productList.appendChild(newRow);
    calculateTotals();
  });

  // حذف سطر
  function removeRow(btn) {
    const rows = document.querySelectorAll('.product-row');
    if (rows.length > 1) {
        btn.closest('.product-row').remove();
        calculateTotals();
    } else {
        alert("At least one product is required.");
    }
  }

  // حساب الإجمالي (Logic)
  function calculateTotals() {
    let totalCost = 0;
    let totalWeight = 0;

    document.querySelectorAll('.product-row').forEach(row => {
      const select = row.querySelector('.product-select');
      const qty = parseInt(row.querySelector('.qty-input').value) || 0;
      const selectedOption = select.options[select.selectedIndex];
      
      if (selectedOption && selectedOption.value) {
        const price = parseFloat(selectedOption.dataset.price) || 0;
        const weight = parseFloat(selectedOption.dataset.weight) || 0;
        
        const subtotal = price * qty;
        row.querySelector('.subtotal-display').value = subtotal.toFixed(2);
        
        totalCost += subtotal;
        totalWeight += (weight * qty);
      }
    });

    // تحديث العرض في الكارت الصغير
    document.getElementById('display-cost').textContent = totalCost.toFixed(2);
    document.getElementById('display-weight').textContent = totalWeight.toFixed(1) + " kg";
    
    // تحديث الحقول المخفية عشان يتبعتوا للداتابيز
    document.getElementById('input-total-weight').value = totalWeight.toFixed(2);
    document.getElementById('input-total-cost').value = totalCost.toFixed(2);
  }
</script>
</body>
</html>