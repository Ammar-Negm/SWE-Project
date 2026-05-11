<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pattern Tests — Observer & State</title>
<style>
  body     { font-family: monospace; background:#0f0f0f; color:#e0e0e0; padding:30px; }
  h1       { color:#fff; margin-bottom:4px; }
  h2       { color:#61dafb; border-bottom:1px solid #333; padding-bottom:6px; margin-top:36px; }
  .pass    { color:#4caf50; }
  .fail    { color:#f44336; }
  .warn    { color:#ff9800; }
  .info    { color:#aaa; font-size:11px; margin-left:18px; }
  .row     { background:#1a1a1a; border-left:3px solid #61dafb;
             padding:10px 14px; margin-bottom:5px; border-radius:0 4px 4px 0; }
  .summary { background:#1e1e1e; border:1px solid #444;
             padding:16px; margin-top:28px; border-radius:6px; }
  hr       { border-color:#333; margin:28px 0; }
  p.sub    { color:#777; font-size:12px; margin-top:2px; }
</style>
</head>
<body>
<?php
// ============================================================
// URL: http://localhost/your_project/public/test_patterns.php
// ============================================================
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../app/models/patterns/OrderStates.php';
require_once __DIR__ . '/../app/models/patterns/StockObserverInterface.php';
require_once __DIR__ . '/../app/models/patterns/ReorderObserver.php';
require_once __DIR__ . '/../app/models/InventoryItem.php';
require_once __DIR__ . '/../app/models/order.php';

$passed = 0; $failed = 0;

function check($label, $condition, $extra = '') {
    global $passed, $failed;
    if ($condition) { $passed++; $icon = "<span class='pass'>✅ PASS</span>"; }
    else            { $failed++; $icon = "<span class='fail'>❌ FAIL</span>"; }
    echo "<div class='row'>{$icon} — {$label}";
    if ($extra) echo "<span class='info'> → {$extra}</span>";
    echo "</div>";
}

$db = Database::getInstance()->getConnection();
echo "<h1>🧪 Pattern Test Suite</h1>";
echo "<p class='sub'>Observer Pattern · State Pattern</p>";

// ── create a test client ──────────────────────────────────
$db->prepare("INSERT INTO client (name, shipping_address) VALUES ('__TEST_CLIENT__', 'Test St')")->execute();
$client_id = (int)$db->lastInsertId();

// ════════════════════════════════════════════════════════
//  STATE PATTERN
// ════════════════════════════════════════════════════════
echo "<h2>1 — State Pattern (Order lifecycle)</h2>";

$order = new Order();

// 1.1 create
$oid = (int)$order->create($client_id);
check("create() returns valid ID", $oid > 0, "order_id=$oid");
$row = $order->getById($oid);
check("New order starts as Pending", $row['status'] === 'Pending', $row['status']);

// 1.2 legal: Pending → Picking
$r = $order->updateStatus($oid, 'Picking');
check("Pending → Picking (legal)", $r === true);
$row = $order->getById($oid);
check("DB status is Picking", $row['status'] === 'Picking', $row['status']);

// 1.3 illegal: Picking → Delivered (skip)
$r = $order->updateStatus($oid, 'Delivered');
check("Picking → Delivered (illegal skip) returns false", $r === false);
$row = $order->getById($oid);
check("Status unchanged after illegal jump", $row['status'] === 'Picking', $row['status']);

// 1.4 illegal: going backwards
$r = $order->updateStatus($oid, 'Pending');
check("Picking → Pending (backwards) returns false", $r === false);

// 1.5 legal: Picking → Packing
$r = $order->updateStatus($oid, 'Packing');
check("Picking → Packing (legal)", $r === true);

// 1.6 legal: Packing → Shipped
$r = $order->updateStatus($oid, 'Shipped');
check("Packing → Shipped (legal)", $r === true);

// 1.7 legal: Shipped → Delivered
$r = $order->updateStatus($oid, 'Delivered');
check("Shipped → Delivered (legal)", $r === true);
$row = $order->getById($oid);
check("Status is Delivered in DB", $row['status'] === 'Delivered', $row['status']);

// 1.8 terminal: Delivered → anything blocked
$r = $order->updateStatus($oid, 'Picking');
check("Delivered → Picking blocked (terminal)", $r === false);

// 1.9 cancel flow
$oid2 = (int)$order->create($client_id);
$order->updateStatus($oid2, 'Picking');
$r = $order->updateStatus($oid2, 'Cancelled');
check("Picking → Cancelled (legal)", $r === true);
$row = $order->getById($oid2);
check("Status is Cancelled in DB", $row['status'] === 'Cancelled', $row['status']);

// 1.10 Cancelled is terminal
$r = $order->updateStatus($oid2, 'Picking');
check("Cancelled → Picking blocked (terminal)", $r === false);

// 1.11 advance() method
$oid3 = (int)$order->create($client_id);
try {
    $order->advance($oid3);
    $row = $order->getById($oid3);
    check("advance() moves Pending → Picking", $row['status'] === 'Picking', $row['status']);
} catch (Exception $e) {
    check("advance() moves Pending → Picking", false, $e->getMessage());
}

// 1.12 cancelOrder() method
try {
    $order->cancelOrder($oid3);
    $row = $order->getById($oid3);
    check("cancelOrder() sets status to Cancelled", $row['status'] === 'Cancelled', $row['status']);
} catch (Exception $e) {
    check("cancelOrder() sets Cancelled", false, $e->getMessage());
}

// 1.13 invalid string
$oid4 = (int)$order->create($client_id);
$r = $order->updateStatus($oid4, 'HACKED');
check("Invalid status string returns false", $r === false);
$row = $order->getById($oid4);
check("Status unchanged after invalid string", $row['status'] === 'Pending', $row['status']);

// ════════════════════════════════════════════════════════
//  OBSERVER PATTERN
// ════════════════════════════════════════════════════════
echo "<h2>2 — Observer Pattern (Stock level monitoring)</h2>";

// need a product with min_stock_level
$stmt = $db->prepare("SELECT product_id, min_stock_level FROM product WHERE min_stock_level > 0 LIMIT 1");
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='row'><span class='warn'>⚠️ SKIP</span> — No product with min_stock_level > 0 in DB. Add one first.</div>";
} else {
    $pid       = (int)$product['product_id'];
    $minLevel  = (int)$product['min_stock_level'];
    $safeQty   = $minLevel + 10;
    $lowQty    = max(0, $minLevel - 1);

    $stmt = $db->prepare("SELECT bin_id FROM bin LIMIT 1");
    $stmt->execute();
    $bin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bin) {
        echo "<div class='row'><span class='warn'>⚠️ SKIP</span> — No bin in DB.</div>";
    } else {
        $bin_id = (int)$bin['bin_id'];

        // 2.1 attach() works without errors
        $inv = new InventoryItem();
        try {
            $inv->attach(new ReorderObserver());
            check("attach(ReorderObserver) works without error", true);
        } catch (Exception $e) {
            check("attach(ReorderObserver) works without error", false, $e->getMessage());
        }

        // 2.2 create test inventory item
        $inv->create($pid, $bin_id, $safeQty);
        $inv_id = (int)$db->lastInsertId();
        check("Test inventory item created", $inv_id > 0, "inv_item_id=$inv_id");

        // 2.3 baseline PO count
        $stmt = $db->prepare("SELECT COUNT(*) FROM purchaseorder WHERE status='pending'");
        $stmt->execute();
        $baseline = (int)$stmt->fetchColumn();

        // 2.4 update ABOVE min — observer should NOT fire reorder
        $inv2 = new InventoryItem();
        $inv2->attach(new ReorderObserver());
        $inv2->updateQuantity($inv_id, $safeQty);
        $stmt->execute();
        $after_safe = (int)$stmt->fetchColumn();
        check(
            "updateQuantity() above min does NOT trigger reorder",
            $after_safe === $baseline,
            "POs: before=$baseline after=$after_safe"
        );

        // 2.5 updateQuantity WITHOUT observer — no side effects
        $inv3 = new InventoryItem(); // no attach
        $inv3->updateQuantity($inv_id, $lowQty);
        $stmt->execute();
        $after_no_obs = (int)$stmt->fetchColumn();
        check(
            "updateQuantity() without observer fires nothing",
            $after_no_obs === $baseline,
            "PO count unchanged: $after_no_obs"
        );

        // 2.6 reset to safe, then drop below WITH observer
        $inv4 = new InventoryItem();
        $inv4->updateQuantity($inv_id, $safeQty);

        $stmt->execute();
        $baseline2 = (int)$stmt->fetchColumn();

        $stmt2 = $db->prepare("SELECT COUNT(*) FROM supplier");
        $stmt2->execute();
        $has_supplier = (int)$stmt2->fetchColumn() > 0;

        $inv5 = new InventoryItem();
        $inv5->attach(new ReorderObserver());
        $inv5->updateQuantity($inv_id, $lowQty);

        $stmt->execute();
        $after_trigger = (int)$stmt->fetchColumn();

        if ($has_supplier) {
            check(
                "updateQuantity() below min WITH observer triggers PO",
                $after_trigger > $baseline2,
                "POs: before=$baseline2 after=$after_trigger"
            );
        } else {
            echo "<div class='row'><span class='warn'>⚠️ SKIP</span> — No supplier in DB. Observer ran but skipped PO creation (correct).</div>";
        }

        // 2.7 audit log entry written
        $stmt3 = $db->prepare("SELECT COUNT(*) FROM inventory_audit_log WHERE action_type='REORDER_TRIGGERED'");
        $stmt3->execute();
        $audit_count = (int)$stmt3->fetchColumn();
        check("ReorderObserver wrote to inventory_audit_log", $audit_count > 0, "$audit_count entries");

        // cleanup test item
        $inv->delete($inv_id);
        $stmt4 = $db->prepare("SELECT * FROM inventory_item WHERE inv_item_id=:id");
        $stmt4->execute([':id' => $inv_id]);
        check("Cleanup: test inventory item deleted", $stmt4->fetch() === false);
    }
}

// ════════════════════════════════════════════════════════
//  CLEANUP
// ════════════════════════════════════════════════════════
echo "<h2>🧹 Cleanup</h2>";
foreach ([$oid, $oid2, $oid3, $oid4] as $id) {
    if (!$id) continue;
    $db->prepare("UPDATE `order` SET status='Pending' WHERE order_id=:id")->execute([':id'=>$id]);
    $db->prepare("DELETE FROM `order` WHERE order_id=:id")->execute([':id'=>$id]);
}
$db->prepare("DELETE FROM client WHERE client_id=:id")->execute([':id'=>$client_id]);
echo "<div class='row' style='color:#aaa'>Test orders and client deleted</div>";

// ════════════════════════════════════════════════════════
//  SUMMARY
// ════════════════════════════════════════════════════════
$total = $passed + $failed;
echo "<hr>";
echo "<div class='summary'>";
$color = $failed === 0 ? '#4caf50' : '#f44336';
$title = $failed === 0 ? '🎉 ALL TESTS PASSED' : "⚠️ {$failed} TEST(S) FAILED";
echo "<h2 style='margin-top:0;color:{$color}'>{$title}</h2>";
echo "<p><span class='pass'>✅ Passed: {$passed}</span> &nbsp;|&nbsp; <span class='fail'>❌ Failed: {$failed}</span> &nbsp;|&nbsp; Total: {$total}</p>";
if ($failed === 0)
    echo "<p style='color:#aaa'>✔ Both patterns verified. Safe to document in design report.</p>";
else
    echo "<p class='warn'>🔍 Each ❌ shows what value came back. Fix the method it points to and re-run.</p>";
echo "</div>";
?>
</body>
</html>
