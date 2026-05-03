<?php

require_once 'core/Database.php';
require_once 'app/models/dad_user.php';
require_once 'app/models/FloorStaff.php';
require_once 'app/models/Supplier.php';
require_once 'app/models/WarehouseManager.php';
require_once 'app/models/PickList.php';
require_once 'app/models/PickTask.php';
require_once 'app/models/InventoryItem.php';

echo "==== START TESTS ====\n\n";

/* =========================
   1. FLOOR STAFF TEST
========================= */

echo "== FloorStaff Tests ==\n";

$staff = $staff = new FloorStaff("Ahmed", "ahmed@test.com", "123456");

$createStaff = $staff->create("08:00", "16:00", 90);
echo "Create Staff: " . ($createStaff ? "OK" : "FAIL") . "\n";

// fake id (عدلها حسب الداتابيز عندك)
$staffId = 1;

$myLists = $staff->getMyAssignedLists($staffId);
echo "Assigned Lists: " . count($myLists) . "\n";


/* =========================
   2. INVENTORY TEST
========================= */

echo "\n== Inventory Tests ==\n";

$inv = new InventoryItem();

$createInv = $inv->create(1, 1, 100);
echo "Create Inventory Item: " . ($createInv ? "OK" : "FAIL") . "\n";

$item = $inv->getById(1);
echo "Get Item Quantity: " . ($item['quantity'] ?? 'NULL') . "\n";


/* =========================
   3. PICK LIST TEST
========================= */

echo "\n== PickList Tests ==\n";

$pickList = new PickList();

$listId = $pickList->create($staffId);
echo "Create PickList ID: " . $listId . "\n";

$pickList->assignStaff($listId, $staffId);
echo "Assign Staff: OK\n";


/* =========================
   4. PICK TASK TEST
========================= */

echo "\n== PickTask Tests ==\n";

$task = new PickTask();

$createTask = $task->create($listId, 1, 5);
echo "Create Task: " . ($createTask ? "OK" : "FAIL") . "\n";

$tasks = $task->getByPickList($listId);
echo "Tasks Count: " . count($tasks) . "\n";


/* =========================
   5. COMPLETE TASK TEST
========================= */

echo "\n== Complete Task Test ==\n";

if (!empty($tasks)) {
    $taskId = $tasks[0]['picktask_id'];

    $result = $task->completeTask($taskId);
    echo "Complete Task: " . ($result ? "OK" : "FAIL") . "\n";
}


/* =========================
   END
========================= */

echo "\n==== END TESTS ====\n";