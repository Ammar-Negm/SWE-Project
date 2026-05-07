<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Place New Order - WareLogix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../style.css">
  <style>
    /* تمييز بوابة العميل بلون مختلف */
    :root { --client-primary: #10B981; }
    .sidebar-client { background-color: #064E3B !important; }
    .btn-client { background-color: var(--client-primary); border-color: var(--client-primary); color: white; }
    .btn-client:hover { background-color: #059669; color: white; }
  </style>
</head>
<body>
<div id="wrapper">
  <aside class="sidebar sidebar-client" id="sidebar">
    <div class="brand">⬡ Client Portal</div>
    <nav class="nav flex-column mt-3">
      <a class="nav-link active" href="create-order.html"><i class="bi bi-cart-plus"></i> New Order</a>
      <a class="nav-link" href="order-history.html"><i class="bi bi-clock-history"></i> My Orders</a>
      <a class="nav-link" href="#"><i class="bi bi-person-lines-fill"></i> My Profile</a>
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
    </nav>

    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          
          <div class="card shadow-sm p-4">
            <h5 class="fw-bold mb-4">Order Construction</h5>
            
            <form method="POST" action="submit_order.php">
              <input type="hidden" name="client_id" value="101">

              <div id="product-list">
                <div class="row mb-3 product-row align-items-end">
                  <div class="col-md-5">
                    <label class="form-label small fw-bold">Select Product</label>
                    <select class="form-select" name="product_id[]" required>
                      <option value="" selected disabled>Choose item...</option>
                      <option value="1" data-price="5.00" data-weight="0.5">Electronic Components (SKU-001) - $5.00</option>
                      <option value="2" data-price="15.00" data-weight="2.0">Chilled Produce (SKU-002) - $15.00</option>
                      <option value="3" data-price="50.00" data-weight="25.0">Raw Chemicals (SKU-003) - $50.00</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label small fw-bold">Quantity</label>
                    <input type="number" class="form-control" name="qty[]" min="1" value="1" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label small fw-bold">Subtotal</label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="text" class="form-control bg-light" readonly value="0.00">
                    </div>
                  </div>
                  <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-row"><i class="bi bi-trash"></i></button>
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
    const firstRow = document.querySelector('.product-row');
    const newRow = firstRow.cloneNode(true);
    // تفريغ القيم في السطر الجديد
    newRow.querySelector('select').value = "";
    newRow.querySelector('input[type="number"]').value = 1;
    newRow.querySelector('input[readonly]').value = "0.00";
    document.getElementById('product-list').appendChild(newRow);
  });

  // حذف منتج (Event Delegation)
  document.getElementById('product-list').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
      const rows = document.querySelectorAll('.product-row');
      if (rows.length > 1) {
        e.target.closest('.product-row').remove();
        calculateTotals();
      }
    }
  });

  // حساب الإجمالي (Logic)
  document.getElementById('product-list').addEventListener('change', calculateTotals);
  document.getElementById('product-list').addEventListener('input', calculateTotals);

  function calculateTotals() {
    let totalCost = 0;
    let totalWeight = 0;

    document.querySelectorAll('.product-row').forEach(row => {
      const select = row.querySelector('select');
      const qty = parseInt(row.querySelector('input[type="number"]').value) || 0;
      const selectedOption = select.options[select.selectedIndex];
      
      if (selectedOption.value) {
        const price = parseFloat(selectedOption.dataset.price);
        const weight = parseFloat(selectedOption.dataset.weight);
        
        const subtotal = price * qty;
        row.querySelector('input[readonly]').value = subtotal.toFixed(2);
        
        totalCost += subtotal;
        totalWeight += (weight * qty);
      }
    });

    // تحديث العرض
    document.getElementById('display-cost').textContent = totalCost.toFixed(2);
    document.getElementById('display-weight').textContent = totalWeight.toFixed(1) + " kg";
    
    // تحديث الحقول المخفية للـ Form
    document.getElementById('input-total-weight').value = totalWeight;
    document.getElementById('input-total-cost').value = totalCost;
  }
</script>
</body>
</html>