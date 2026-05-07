<?php

require_once 'core/Database.php';
require_once 'app/models/InventoryItem.php';
require_once 'app/models/AuditLog.php';
require_once 'app/models/Shipment.php';
require_once 'app/models/PickTask.php';
require_once 'app/models/PurchaseOrder.php';
require_once 'app/models/Supplier.php';

echo "==== START REAL INTEGRATION TEST ====\n\n";

$db = Database::getInstance()->getConnection();
$inv = new InventoryItem();
$ship = new Shipment();
$po = new PurchaseOrder();
$audit = new AuditLog();

try {
  // 1. نضمن وجود مورد (Supplier)
  // لو مفيش مورد رقم 1، هنكارييت واحد للتجربة
  $db->query("INSERT IGNORE INTO user (id, name, email, password, role) VALUES (1, 'Test Supplier', 'supplier@test.com', '123', 'supplier')");
  $db->query("INSERT IGNORE INTO supplier (supplier_id, user_id, name, email, password, perf_score) VALUES (1, 1, 'Test Supplier', 'supplier@test.com', '123', 100)");

  // 2. نضمن وجود طلب شراء (Purchase Order)
  // ده اللي كان مسبب المشكلة، لازم الـ PO يكون موجود قبل الـ Shipment
  $checkPo = $db->query("SELECT po_id FROM purchaseorder WHERE po_id = 1");
  if (!$checkPo->fetch()) {
    echo "Creating Purchase Order #1...\n";
    $po->create(1, date('Y-m-d')); // بنكارييت PO للمورد رقم 1
  }

  // 3. نضمن وجود منتج (Inventory Item)
  $db->query("INSERT IGNORE INTO product (product_id, name, sku, category, price) VALUES (1, 'Test Product', 'SKU001', 'General', 10)");
  $db->query("INSERT IGNORE INTO bin (bin_id, zone_id, maxWeight, shelfLocation) VALUES (1, 1, 1000, 'A-1-1')");

  if (!$inv->getById(1)) {
    echo "Creating Inventory Item #1...\n";
    $inv->create(1, 1, 100);
  }

  /* =========================================
       التجربة الأولى: توريد (SUPPLY)
    ========================================= */
  echo "== Step 1: Creating a real Shipment ==\n";
  // دلوقتي الشحنة هتتكرى عادي لأن PO رقم 1 بقى موجود
  $ship->create(1, "Expected");
  $myShipmentId = $db->lastInsertId();
  echo "Shipment Created with ID: $myShipmentId\n";

  echo "== Step 2: Recording Supply Audit ==\n";
  $qtyReceived = 50;
  // مورد رقم 1 ورد 50 قطعة
  $res1 = $audit->record(1, 'SUPPLY', $qtyReceived, 1, 'supplier', $myShipmentId);
  echo "Supply Audit Log: " . ($res1 ? "SUCCESS" : "FAILED") . "\n\n";

  /* =========================================
       التجربة الثانية: سحب (PICKING)
    ========================================= */
  echo "== Step 3: Recording Picking Audit ==\n";
  $qtyPicked = 10;
  $myTaskId = 99; // رقم وهمي للتاسك

  // موظف رقم 1 سحب 10 قطع
  $res2 = $audit->record(1, 'PICKING', -$qtyPicked, 1, 'staff', $myTaskId);
  echo "Picking Audit Log: " . ($res2 ? "SUCCESS" : "FAILED") . "\n\n";

  /* =========================================
       عرض النتائج النهائية
    ========================================= */
  echo "== Step 4: Final Audit History for Item #1 ==\n";
  $logs = $audit->getItemHistory(1);
  foreach ($logs as $l) {
    echo "[{$l['created_at']}] Action: {$l['action_type']} | Change: {$l['change_amount']} | Before: {$l['quantity_before']} | After: {$l['quantity_after']} | Role: {$l['performer_role']}\n";
  }
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage();
}
