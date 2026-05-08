<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '../core/Database.php';
require_once __DIR__ . '../core/Controller.php';


// ==============================
// HELPER
// ==============================
function ok($label) {
    echo "<tr><td>$label</td><td style='color:green'>✅ PASS</td></tr>";
}
function fail($label, $reason = '') {
    echo "<tr><td>$label</td><td style='color:red'>❌ FAIL — $reason</td></tr>";
}
function section($title) {
    echo "<tr><th colspan='2' style='background:#1A3C5E;color:white;padding:8px'>$title</th></tr>";
}

echo "
<style>
  body { font-family: sans-serif; padding: 2rem; background: #f1f5f9; }
  table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
  td, th { padding: 10px 16px; border-bottom: 1px solid #e2e8f0; text-align: left; }
  h2 { color: #1A3C5E; }
</style>
<h2>⬡ WareLogix — Service Tests</h2>
<table>
";

// ==============================
// 1. DATABASE
// ==============================
section('Database Connection');
try {
    $db = Database::getInstance()->getConnection();
    ok('PDO Connection');
} catch (Exception $e) {
    fail('PDO Connection', $e->getMessage());
    echo "</table>"; exit;
}

// ==============================
// 2. StorageService
// ==============================
section('StorageService');
try {
    require_once __DIR__ . '/app/services/StorageService.php';

    $storage = new StorageService();

    // simulateWeight
    $w = $storage->simulateWeight(10);
    ($w >= 8 && $w <= 12) ? ok('simulateWeight(10) في النطاق 8-12') : fail('simulateWeight', "got $w");

    // validateParcel
    $storage->validateParcel(5, 10)  ? ok('validateParcel(5, 10) = true')  : fail('validateParcel true');
    !$storage->validateParcel(15, 10) ? ok('validateParcel(15, 10) = false') : fail('validateParcel false');

    // checkExpiry — بيرجع array
    $expired = $storage->checkExpiry();
    is_array($expired) ? ok('checkExpiry() returns array') : fail('checkExpiry', 'not array');

    // calculateFit — bin_id = 1 (لو موجود)
    $fit = $storage->calculateFit(2, 3, 4, 1);
    isset($fit['fits']) ? ok('calculateFit() returns fits key') : fail('calculateFit', 'no fits key');

} catch (Exception $e) {
    fail('StorageService', $e->getMessage());
}

// ==============================
// 3. SupplierService
// ==============================
section('SupplierService');
try {
    require_once __DIR__ .'/app/services/SupplierService.php';
    

    $supplierSvc = new SupplierService();

    // supplierScore — supplier_id = 1
    $score = $supplierSvc->supplierScore(1);
    ($score >= 0 && $score <= 100) ? ok("supplierScore(1) = $score") : fail('supplierScore', "got $score");

    // getRankedSuppliers — product_id = 1
    $ranked = $supplierSvc->getRankedSuppliers(1);
    is_array($ranked) ? ok('getRankedSuppliers(1) returns array') : fail('getRankedSuppliers', 'not array');

} catch (Exception $e) {
    fail('SupplierService', $e->getMessage());
}

// ==============================
// 4. PurchaseOrderService
// ==============================
section('PurchaseOrderService');
try {
    require_once __DIR__ .'/app/services/PurchaseOrderService.php';
    
    $poSvc = new PurchaseOrderService();

    // validateInbound
    $poSvc->validateInbound(999, 0)  === false ? ok('validateInbound(999, 0) = false') : fail('validateInbound false');
    
    // checkReorder — product_id = 1
    $reorder = $poSvc->checkReorder(1);
    ok('checkReorder(1) ran without crash — result: ' . ($reorder ? 'PO created' : 'no reorder needed'));

} catch (Exception $e) {
    fail('PurchaseOrderService', $e->getMessage());
}

// ==============================
// 5. OrderService
// ==============================
section('OrderService');
try {
    require_once __DIR__ .'/app/services/OrderService.php';
    $orderSvc = new OrderService();

    // optimizePickRoute
    $tasks = [
        ['shelfLocation' => 'C-12', 'item' => 'X'],
        ['shelfLocation' => 'A-01', 'item' => 'Y'],
        ['shelfLocation' => 'B-05', 'item' => 'Z'],
    ];
    $sorted = $orderSvc->optimizePickRoute($tasks);
    $sorted[0]['shelfLocation'] === 'A-01' ? ok('optimizePickRoute sorted correctly') : fail('optimizePickRoute', 'wrong order');

    // reserveStock — product_id = 1, qty = 1
    $reserved = $orderSvc->reserveStock(1, 1);
    ok('reserveStock(1,1) ran — result: ' . ($reserved ? 'reserved' : 'insufficient stock'));

} catch (Exception $e) {
    fail('OrderService', $e->getMessage());
}

echo "</table>";
echo "<p style='color:#64748b;margin-top:1rem'>Run at: " . date('Y-m-d H:i:s') . "</p>";
?>
