<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Model Test Suite</title>
    <style>
        body      { font-family: monospace; background: #0f0f0f; color: #e0e0e0; padding: 30px; }
        h2        { color: #61dafb; border-bottom: 1px solid #333; padding-bottom: 8px; margin-top: 40px; }
        h1        { color: #ffffff; }
        .pass     { color: #4caf50; }
        .fail     { color: #f44336; }
        .info     { color: #aaaaaa; font-size: 12px; margin-left: 20px; }
        .summary  { background: #1e1e1e; border: 1px solid #444; padding: 16px; margin-top: 30px; border-radius: 6px; }
        .section  { background: #1a1a1a; border-left: 3px solid #61dafb; padding: 12px 16px; margin-bottom: 6px; border-radius: 0 4px 4px 0; }
        hr        { border-color: #333; margin: 30px 0; }
        .warn     { color: #ff9800; }
    </style>
</head>
<body>

<?php
// ============================================================
// HOW TO USE:
// 1. Place this file in your project root (same level as Database.php)
// 2. Place Zone.php, Bin.php, Client.php, Order.php, ShippingLabel.php
//    in a models/ folder next to this file
// 3. Open: http://localhost/your_project/test_models.php
// ============================================================

require_once "../core/Database.php";
require_once "../app/models/zone.php";
require_once "../app/models/bin.php";
require_once "../app/models/client.php";
require_once "../app/models/order.php";
require_once "../app/models/shipping_label.php";

// ============================================================
// TEST ENGINE
// ============================================================
$passed = 0;
$failed = 0;
$total  = 0;

function check($label, $condition, $extra = '') {
    global $passed, $failed, $total;
    $total++;
    if ($condition) {
        $passed++;
        echo "<div class='section'><span class='pass'>✅ PASS</span> — {$label}";
        if ($extra) echo "<span class='info'> → {$extra}</span>";
        echo "</div>";
    } else {
        $failed++;
        echo "<div class='section'><span class='fail'>❌ FAIL</span> — {$label}";
        if ($extra) echo "<span class='info warn'> → {$extra}</span>";
        echo "</div>";
    }
}

// shared DB instance for lastInsertId()
$db = Database::getInstance()->getConnection();

// ============================================================
// IDs collected across tests — used for cleanup at the end
// ============================================================
$created_zone_id  = null;
$created_bin_id   = null;
$created_client_id = null;
$created_order_id  = null;
$created_label_id  = null;

echo "<h1>🧪 Model Test Suite</h1>";
echo "<p style='color:#aaa'>Testing: Zone · Bin · Client · Order · ShippingLabel</p>";

// ============================================================
//  ZONE TESTS
// ============================================================
echo "<h2>1 — Zone</h2>";

$zone = new Zone();

// 1.1 create
$r = $zone->create('TEST_ZONE_AUTO', 1000);
$created_zone_id = (int)$db->lastInsertId();
check("create() returns true", $r === true);
check("create() produced a valid ID", $created_zone_id > 0, "zone_id = $created_zone_id");

// 1.2 getAll
$all = $zone->getAll();
check("getAll() returns an array", is_array($all));
check("getAll() is not empty", count($all) > 0, count($all) . " rows found");

// 1.3 getById — valid ID
$row = $zone->getById($created_zone_id);
check("getById() returns a row", $row !== false && is_array($row));
check("getById() has correct zone_name", isset($row['zone_name']) && $row['zone_name'] === 'TEST_ZONE_AUTO', "got: " . ($row['zone_name'] ?? 'null'));
check("getById() has correct max_capacity", isset($row['max_capacity']) && (int)$row['max_capacity'] === 1000, "got: " . ($row['max_capacity'] ?? 'null'));

// 1.4 getById — fake ID
$fake = $zone->getById(999999);
check("getById() returns false for fake ID", $fake === false);

// 1.5 update — change name only
$zone->update($created_zone_id, 'TEST_ZONE_UPDATED', null);
$updated = $zone->getById($created_zone_id);
check("update() changed zone_name", $updated['zone_name'] === 'TEST_ZONE_UPDATED', "got: " . $updated['zone_name']);
check("update() kept max_capacity when null passed", (int)$updated['max_capacity'] === 1000, "got: " . $updated['max_capacity']);

// 1.6 getBins — empty zone
$bins = $zone->getBins($created_zone_id);
check("getBins() returns array for empty zone", is_array($bins));
check("getBins() is empty for new zone", count($bins) === 0, count($bins) . " bins found");

// 1.7 hasCapacity — under limit
$r = $zone->hasCapacity($created_zone_id, 500);
check("hasCapacity() returns true when under limit (500 of 1000)", $r === true);

// 1.8 hasCapacity — over limit
$r = $zone->hasCapacity($created_zone_id, 99999);
check("hasCapacity() returns false when over limit (99999 of 1000)", $r === false);

// 1.9 hasCapacity — fake zone
$r = $zone->hasCapacity(999999, 10);
check("hasCapacity() returns false for fake zone_id", $r === false);


// ============================================================
//  BIN TESTS
// ============================================================
echo "<h2>2 — Bin</h2>";

$bin = new Bin();

// 2.1 create — use the zone we just created
$r = $bin->create($created_zone_id, 500, 'SHELF-TEST-01');
$created_bin_id = (int)$db->lastInsertId();
check("create() returns true", $r === true);
check("create() produced a valid ID", $created_bin_id > 0, "bin_id = $created_bin_id");

// 2.2 getAll
$all = $bin->getAll();
check("getAll() returns an array", is_array($all));
check("getAll() is not empty", count($all) > 0, count($all) . " rows");

// 2.3 getById — valid
$row = $bin->getById($created_bin_id);
check("getById() returns a row", $row !== false && is_array($row));
check("getById() has correct maxWeight", isset($row['maxWeight']) && (int)$row['maxWeight'] === 500, "got: " . ($row['maxWeight'] ?? 'null'));
check("getById() has currentWeight = 0 on creation", isset($row['currentWeight']) && (float)$row['currentWeight'] === 0.0, "got: " . ($row['currentWeight'] ?? 'null'));

// 2.4 getById — fake ID
$fake = $bin->getById(999999);
check("getById() returns false for fake ID", $fake === false);

// 2.5 getByZone
$zBins = $bin->getByZone($created_zone_id);
check("getByZone() returns array", is_array($zBins));
check("getByZone() found our bin", count($zBins) >= 1, count($zBins) . " bins in zone");

// 2.6 update — change shelfLocation only
$bin->update($created_bin_id, null, null, 'SHELF-UPDATED-01');
$updated = $bin->getById($created_bin_id);
check("update() changed shelfLocation", $updated['shelfLocation'] === 'SHELF-UPDATED-01', "got: " . $updated['shelfLocation']);
check("update() kept maxWeight when null passed", (int)$updated['maxWeight'] === 500, "got: " . $updated['maxWeight']);

// 2.7 hasCapacity — under limit (bin is empty, limit 500)
$r = $bin->hasCapacity($created_bin_id, 200);
check("hasCapacity() returns true when under limit (200 of 500)", $r === true);

// 2.8 hasCapacity — over limit
$r = $bin->hasCapacity($created_bin_id, 99999);
check("hasCapacity() returns false when over limit", $r === false);

// 2.9 addWeight
$bin->addWeight($created_bin_id, 150);
$after = $bin->getById($created_bin_id);
check("addWeight() increased currentWeight to 150", (float)$after['currentWeight'] === 150.0, "got: " . $after['currentWeight']);

// 2.10 hasCapacity — after adding weight
$r = $bin->hasCapacity($created_bin_id, 400);
check("hasCapacity() returns false after addWeight (150+400 > 500)", $r === false);

$r = $bin->hasCapacity($created_bin_id, 300);
check("hasCapacity() returns true for weight that still fits (150+300 <= 500)", $r === true);

// 2.11 removeWeight
$bin->removeWeight($created_bin_id, 50);
$after = $bin->getById($created_bin_id);
check("removeWeight() decreased currentWeight to 100", (float)$after['currentWeight'] === 100.0, "got: " . $after['currentWeight']);

// 2.12 getAvailableBins
$available = $bin->getAvailableBins($created_zone_id, 100);
check("getAvailableBins() returns array", is_array($available));
check("getAvailableBins() finds bins with enough space (need 100, free=400)", count($available) >= 1, count($available) . " bins available");

$tooMany = $bin->getAvailableBins($created_zone_id, 99999);
check("getAvailableBins() returns empty when no bin has enough space", count($tooMany) === 0, count($tooMany) . " bins found");

// reset bin weight to 0 for zone capacity test
$bin->removeWeight($created_bin_id, 100);


// ============================================================
//  CLIENT TESTS
// ============================================================
echo "<h2>3 — Client</h2>";

$client = new Client();

// 3.1 create
$r = $client->create('TEST CLIENT AUTO', '99 Test Street, Cairo', 'retail');
$created_client_id = (int)$db->lastInsertId();
check("create() returns true", $r === true);
check("create() produced a valid ID", $created_client_id > 0, "client_id = $created_client_id");

// 3.2 getAll
$all = $client->getAll();
check("getAll() returns an array", is_array($all));
check("getAll() is not empty", count($all) > 0, count($all) . " rows");

// 3.3 getById — valid
$row = $client->getById($created_client_id);
check("getById() returns a row", $row !== false && is_array($row));
check("getById() has correct name", isset($row['name']) && $row['name'] === 'TEST CLIENT AUTO', "got: " . ($row['name'] ?? 'null'));
check("getById() loyalty_points starts at 0", isset($row['loyalty_points']) && (int)$row['loyalty_points'] === 0, "got: " . ($row['loyalty_points'] ?? 'null'));
check("getById() total_orders_placed starts at 0", isset($row['total_orders_placed']) && (int)$row['total_orders_placed'] === 0, "got: " . ($row['total_orders_placed'] ?? 'null'));

// 3.4 getById — fake
$fake = $client->getById(999999);
check("getById() returns false for fake ID", $fake === false);

// 3.5 searchByName — should find our client
$results = $client->searchByName('TEST CLIENT');
check("searchByName() returns array", is_array($results));
check("searchByName() finds partial match", count($results) >= 1, count($results) . " results for 'TEST CLIENT'");

// 3.6 searchByName — no match
$results = $client->searchByName('XYZNONEXISTENTXYZ');
check("searchByName() returns empty for no match", count($results) === 0);

// 3.7 update — change address only
$client->update($created_client_id, null, '100 Updated Street, Alex', null);
$updated = $client->getById($created_client_id);
check("update() changed shipping_address", $updated['shipping_address'] === '100 Updated Street, Alex', "got: " . $updated['shipping_address']);
check("update() kept name when null passed", $updated['name'] === 'TEST CLIENT AUTO', "got: " . $updated['name']);

// 3.8 addLoyaltyPoints
$client->addLoyaltyPoints($created_client_id, 50);
$after = $client->getById($created_client_id);
check("addLoyaltyPoints() increased loyalty_points to 50", (int)$after['loyalty_points'] === 50, "got: " . $after['loyalty_points']);

$client->addLoyaltyPoints($created_client_id, 30);
$after = $client->getById($created_client_id);
check("addLoyaltyPoints() accumulated correctly to 80", (int)$after['loyalty_points'] === 80, "got: " . $after['loyalty_points']);

// 3.9 incrementOrderCount
$client->incrementOrderCount($created_client_id);
$after = $client->getById($created_client_id);
check("incrementOrderCount() increased total_orders_placed to 1", (int)$after['total_orders_placed'] === 1, "got: " . $after['total_orders_placed']);

$client->incrementOrderCount($created_client_id);
$after = $client->getById($created_client_id);
check("incrementOrderCount() accumulated correctly to 2", (int)$after['total_orders_placed'] === 2, "got: " . $after['total_orders_placed']);

// 3.10 getOrders — no orders yet
$orders = $client->getOrders($created_client_id);
check("getOrders() returns array", is_array($orders));
check("getOrders() is empty for new client", count($orders) === 0, count($orders) . " orders");


// ============================================================
//  ORDER TESTS
// ============================================================
echo "<h2>4 — Order</h2>";

$order = new Order();

// 4.1 create
$created_order_id = (int)$order->create($created_client_id);
check("create() returns a valid order ID", $created_order_id > 0, "order_id = $created_order_id");

// 4.2 getById — valid
$row = $order->getById($created_order_id);
check("getById() returns a row", $row !== false && is_array($row));
check("getById() status starts as Pending", isset($row['status']) && $row['status'] === 'Pending', "got: " . ($row['status'] ?? 'null'));
check("getById() total_weight starts at 0", isset($row['total_weight']) && (float)$row['total_weight'] === 0.0, "got: " . ($row['total_weight'] ?? 'null'));
check("getById() total_cost starts at 0.00", isset($row['total_cost']) && (float)$row['total_cost'] === 0.0, "got: " . ($row['total_cost'] ?? 'null'));
check("getById() includes client_name via JOIN", isset($row['client_name']) && !empty($row['client_name']), "got: " . ($row['client_name'] ?? 'null'));

// 4.3 getById — fake
$fake = $order->getById(999999);
check("getById() returns false for fake ID", $fake === false);

// 4.4 getAll
$all = $order->getAll();
check("getAll() returns an array", is_array($all));
check("getAll() is not empty", count($all) > 0, count($all) . " rows");

// 4.5 getByClient
$clientOrders = $order->getByClient($created_client_id);
check("getByClient() returns array", is_array($clientOrders));
check("getByClient() found our order", count($clientOrders) >= 1, count($clientOrders) . " orders for client");

// 4.6 getByStatus — Pending
$pending = $order->getByStatus('Pending');
check("getByStatus('Pending') returns array", is_array($pending));
$found = false;
foreach ($pending as $o) { if ((int)$o['order_id'] === $created_order_id) $found = true; }
check("getByStatus('Pending') includes our new order", $found);

// 4.7 updateStatus — valid
$r = $order->updateStatus($created_order_id, 'Picking');
check("updateStatus('Picking') returns true", $r === true);
$updated = $order->getById($created_order_id);
check("updateStatus() actually changed status in DB", $updated['status'] === 'Picking', "got: " . $updated['status']);

// 4.8 updateStatus — invalid status
$r = $order->updateStatus($created_order_id, 'HACKED_STATUS');
check("updateStatus() returns false for invalid status", $r === false);
$unchanged = $order->getById($created_order_id);
check("updateStatus() did NOT change status after invalid input", $unchanged['status'] === 'Picking', "still: " . $unchanged['status']);

// 4.9 updateTotals
$r = $order->updateTotals($created_order_id, 12.5, 350.00);
check("updateTotals() returns true", $r === true);
$after = $order->getById($created_order_id);
check("updateTotals() saved total_weight correctly", (float)$after['total_weight'] === 12.5, "got: " . $after['total_weight']);
check("updateTotals() saved total_cost correctly", (float)$after['total_cost'] === 350.0, "got: " . $after['total_cost']);

// 4.10 delete — should FAIL because status is 'Picking' not 'Pending'
$r = $order->delete($created_order_id);
check("delete() returns false when status is not Pending", $r === false);
$stillExists = $order->getById($created_order_id);
check("delete() did NOT remove the order", $stillExists !== false);

// 4.11 reset status to Pending so we can test real delete at cleanup
$order->updateStatus($created_order_id, 'Pending');

// 4.12 getPickLists — no pick lists yet
$lists = $order->getPickLists($created_order_id);
check("getPickLists() returns array", is_array($lists));
check("getPickLists() is empty for new order", count($lists) === 0, count($lists) . " pick lists");

// 4.13 getOrders from Client — should now find our order
$clientOrders2 = $client->getOrders($created_client_id);
check("Client::getOrders() now finds the order we created", count($clientOrders2) >= 1, count($clientOrders2) . " orders");


// ============================================================
//  SHIPPING LABEL TESTS
// ============================================================
echo "<h2>5 — ShippingLabel</h2>";

$label = new ShippingLabel();

// 5.1 labelExists — before generating
$exists = $label->labelExists($created_order_id);
check("labelExists() returns false before any label is generated", $exists === false);

// 5.2 generate
$created_label_id = (int)$label->generate($created_order_id);
check("generate() returns a valid label ID", $created_label_id > 0, "label_id = $created_label_id");

// 5.3 labelExists — after generating
$exists = $label->labelExists($created_order_id);
check("labelExists() returns true after generate()", $exists === true);

// 5.4 getById — valid
$row = $label->getById($created_label_id);
check("getById() returns a row", $row !== false && is_array($row));
check("getById() status starts as Generated", isset($row['status']) && $row['status'] === 'Generated', "got: " . ($row['status'] ?? 'null'));
check("getById() has a qr_code", isset($row['qr_code']) && !empty($row['qr_code']), "got: " . ($row['qr_code'] ?? 'null'));
check("getById() has a tracking_number", isset($row['tracking_number']) && !empty($row['tracking_number']), "got: " . ($row['tracking_number'] ?? 'null'));
check("getById() tracking_number starts with TRK-", isset($row['tracking_number']) && str_starts_with($row['tracking_number'], 'TRK-'), "got: " . ($row['tracking_number'] ?? 'null'));
check("getById() includes order_status via JOIN", isset($row['order_status']) && !empty($row['order_status']), "got: " . ($row['order_status'] ?? 'null'));

// 5.5 getById — fake
$fake = $label->getById(999999);
check("getById() returns false for fake ID", $fake === false);

// 5.6 getByOrder
$byOrder = $label->getByOrder($created_order_id);
check("getByOrder() returns a row", $byOrder !== false && is_array($byOrder));
check("getByOrder() found the correct label", isset($byOrder['label_id']) && (int)$byOrder['label_id'] === $created_label_id, "got label_id: " . ($byOrder['label_id'] ?? 'null'));

// 5.7 getByTrackingNumber
$trackingNum = $row['tracking_number'];
$byTracking  = $label->getByTrackingNumber($trackingNum);
check("getByTrackingNumber() returns a row", $byTracking !== false && is_array($byTracking));
check("getByTrackingNumber() found correct label", isset($byTracking['label_id']) && (int)$byTracking['label_id'] === $created_label_id);

// 5.8 getByTrackingNumber — fake
$byFake = $label->getByTrackingNumber('TRK-FAKE-999');
check("getByTrackingNumber() returns false for fake tracking number", $byFake === false);

// 5.9 getAll
$all = $label->getAll();
check("getAll() returns an array", is_array($all));
check("getAll() is not empty", count($all) > 0, count($all) . " rows");

// 5.10 updateStatus — valid
$r = $label->updateStatus($created_label_id, 'Printed');
check("updateStatus('Printed') returns true", $r === true);
$after = $label->getById($created_label_id);
check("updateStatus() changed status in DB", $after['status'] === 'Printed', "got: " . $after['status']);

// 5.11 updateStatus — invalid
$r = $label->updateStatus($created_label_id, 'FAKE_STATUS');
check("updateStatus() returns false for invalid status", $r === false);

// 5.12 delete — should work when status is Printed
$r = $label->delete($created_label_id);
check("delete() returns true when status is Printed", $r === true);
$gone = $label->getById($created_label_id);
check("delete() actually removed the label from DB", $gone === false);

// 5.13 labelExists — after delete
$exists = $label->labelExists($created_order_id);
check("labelExists() returns false after label is deleted", $exists === false);

// 5.14 generate again — to test Attached guard
$created_label_id = (int)$label->generate($created_order_id);
$label->updateStatus($created_label_id, 'Attached');
$r = $label->delete($created_label_id);
check("delete() returns false when status is Attached", $r === false);
$label->updateStatus($created_label_id, 'Generated'); // reset for cleanup


// ============================================================
//  CLEANUP — remove all test data from DB
// ============================================================
echo "<h2>🧹 Cleanup</h2>";

// delete label first (FK)
if ($created_label_id) {
    $label->updateStatus($created_label_id, 'Generated');
    $label->delete($created_label_id);
    echo "<div class='section' style='color:#aaa'>Deleted test label ID: $created_label_id</div>";
}

// delete order (FK to client)
if ($created_order_id) {
    $order->updateStatus($created_order_id, 'Pending');
    $order->delete($created_order_id);
    echo "<div class='section' style='color:#aaa'>Deleted test order ID: $created_order_id</div>";
}

// delete client
if ($created_client_id) {
    $client->delete($created_client_id);
    echo "<div class='section' style='color:#aaa'>Deleted test client ID: $created_client_id</div>";
}

// delete bin (FK to zone)
if ($created_bin_id) {
    $bin->delete($created_bin_id);
    echo "<div class='section' style='color:#aaa'>Deleted test bin ID: $created_bin_id</div>";
}

// delete zone last
if ($created_zone_id) {
    $zone->delete($created_zone_id);
    echo "<div class='section' style='color:#aaa'>Deleted test zone ID: $created_zone_id</div>";
}


// ============================================================
//  FINAL SUMMARY
// ============================================================
echo "<hr>";
echo "<div class='summary'>";
echo "<h2 style='margin-top:0; color: " . ($failed === 0 ? '#4caf50' : '#f44336') . "'>
        " . ($failed === 0 ? '🎉 ALL TESTS PASSED' : "⚠️ {$failed} TEST(S) FAILED") . "
      </h2>";
echo "<p><span class='pass'>✅ Passed: {$passed}</span> &nbsp;|&nbsp;
          <span class='fail'>❌ Failed: {$failed}</span> &nbsp;|&nbsp;
          Total: {$total}</p>";

if ($failed > 0) {
    echo "<p class='warn'>🔍 Scroll up and look at every ❌ line — fix the model method it points to, then re-run this file.</p>";
} else {
    echo "<p style='color:#aaa'>✔ Model layer is clean. Safe to move on to controllers.</p>";
}
echo "</div>";
?>

</body>
</html>