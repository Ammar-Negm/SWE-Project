<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/PickList.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../models/PickTask.php';

class OrderService {

    // Order Fulfillment State Machine
    public function safeUpdateStatus($order_id, $newStatus) {
        $order = new Order();
        $current = $order->getById($order_id);

        $flow = [
            'Pending' => 'Picking',
            'Picking' => 'Packing',
            'Packing' => 'Shipped',
            'Shipped' => 'Delivered'
        ];

        if ($flow[$current['status']] === $newStatus) {
            return $order->updateStatus($order_id, $newStatus);
        }

        return false;
    }

    // Batch Picking Route Logic
    public function optimizePickRoute($tasks) {
        usort($tasks, function($a, $b) {
            return strcmp($a['shelfLocation'], $b['shelfLocation']);
        });

        return $tasks;
    }

    // Real-time Task Load Balancer
    public function assignTaskToStaff($task_id) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->query("
            SELECT assigned_staff_id, COUNT(*) as load
            FROM pick_list
            GROUP BY assigned_staff_id
            ORDER BY load ASC
            LIMIT 1
        ");

        $staff = $stmt->fetch();

        $pickList = new PickList();
        return $pickList->assignStaff($task_id, $staff['assigned_staff_id']);
    }

    // Reserved vs Available
public function reserveStock($product_id, $quantityNeeded) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT inv_item_id, quantity FROM inventory_item
        WHERE product_id = :pid AND status = 'Available'
        ORDER BY inv_item_id ASC
    ");
    $stmt->execute([':pid' => $product_id]);
    $items     = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $remaining = $quantityNeeded;
    foreach ($items as $item) {
        if ($remaining <= 0) break;
        if ($item['quantity'] <= $remaining) {
            $db->prepare("UPDATE inventory_item SET status='Reserved' WHERE inv_item_id=?")
               ->execute([$item['inv_item_id']]);
            $remaining -= $item['quantity'];
        } else {
            $newQty = $item['quantity'] - $remaining;
            $db->prepare("UPDATE inventory_item SET quantity=? WHERE inv_item_id=?")
               ->execute([$newQty, $item['inv_item_id']]);
            $db->prepare("
                INSERT INTO inventory_item (product_id, bin_id, quantity, status)
                SELECT product_id, bin_id, ?, 'Reserved' FROM inventory_item WHERE inv_item_id=?
            ")->execute([$remaining, $item['inv_item_id']]);
            $remaining = 0;
        }
    }
    return $remaining === 0;
}

// Cross-Docking
public function processCrossDock($product_id, $incomingQty) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT o.order_id, oi.quantity_needed
        FROM `order` o
        JOIN order_item oi ON o.order_id = oi.order_id
        WHERE oi.product_id = :pid AND o.status = 'Pending'
        ORDER BY o.date ASC
    ");
    $stmt->execute([':pid' => $product_id]);
    $pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $crossDocked   = 0;
    $remaining     = $incomingQty;
    foreach ($pendingOrders as $order) {
        if ($remaining <= 0) break;
        $fulfill = min($order['quantity_needed'], $remaining);
        $db->prepare("
            UPDATE order_item SET quantity_fulfilled = quantity_fulfilled + ?
            WHERE order_id = ? AND product_id = ?
        ")->execute([$fulfill, $order['order_id'], $product_id]);
        $crossDocked += $fulfill;
        $remaining   -= $fulfill;
    }
    return ['crossDocked' => $crossDocked, 'stored' => $remaining];
}
// Pick-Failure Protocol
// لما موظف مش لاقي الأيتم، بيسجل الفشل ويعمل reassign لموظف تاني
public function reportPickFailure($task_id, $staff_id, $reason) {
    $db = Database::getInstance()->getConnection();

    // 1. سجّل الفشل في جدول pick_failure_log
    $db->prepare("
        INSERT INTO pick_failure_log (picktask_id, staff_id, reason, created_at)
        VALUES (?, ?, ?, NOW())
    ")->execute([$task_id, $staff_id, $reason]);

    // 2. غيّر حالة الـ task لـ Failed
    $pickTask = new PickTask();
    $pickTask->updateStatus($task_id, 'Failed');

    // 3. اجلب الـ pick_list_id بتاع الـ task
    $stmt = $db->prepare("SELECT pick_list_id FROM pick_task WHERE picktask_id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. عمل reassign للـ pick list لأقل موظف عنده load (نفس loadbalancer الموجود)
    return $this->assignTaskToStaff($task['pick_list_id']);
}

// Sort-to-Light Simulation
// بيوزع الأوردرات على stations وبيضيء الـ station الصح لكل أوردر
// بيرجع array فيها كل أوردر مرتبط بـ station ومكانه
public function sortToLight($order_ids) {
    $db = Database::getInstance()->getConnection();
    $assignments = [];

    foreach ($order_ids as $order_id) {
        // جلب كل الأيتمز بتاعت الأوردر مع مكانهم في الشيلف
        $stmt = $db->prepare("
            SELECT oi.product_id, ii.bin_id, b.shelfLocation, oi.quantity
            FROM order_item oi
            JOIN inventory_item ii ON oi.product_id = ii.product_id
            JOIN bin b ON ii.bin_id = b.bin_id
            WHERE oi.order_id = ?
            AND ii.status = 'Reserved'
            ORDER BY b.shelfLocation ASC
        ");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // كل أوردر بياخد station رقم = آخر رقم في الـ order_id mod 10
        $station = 'ST-' . str_pad(($order_id % 10) + 1, 2, '0', STR_PAD_LEFT);

        $assignments[] = [
            'order_id'  => $order_id,
            'station'   => $station,
            'light'     => 'ON',  // إشارة إضاءة الـ station
            'items'     => $items
        ];
    }

    // رتّب الـ assignments حسب الـ station عشان الموظف يمشي في route واحد
    usort($assignments, fn($a, $b) => strcmp($a['station'], $b['station']));

    return $assignments;
}

// Warehouse Emergency Mode
// لما يحصل طارئ: بيوقف كل الأوردرات الجديدة
// وبيرجع قائمة بالأوردرات اللي لسه في النص
public function setEmergencyMode($active, $reason = '') {
    $db = Database::getInstance()->getConnection();

    $value = $active ? '1' : '0';

    // حفظ حالة الطوارئ في warehouse_config
    $db->prepare("
        INSERT INTO warehouse_config (config_key, config_value)
        VALUES ('emergency_mode', ?)
        ON DUPLICATE KEY UPDATE config_value = ?
    ")->execute([$value, $value]);

    if ($active) {
        // سجّل سبب الطوارئ
        $db->prepare("
            INSERT INTO warehouse_config (config_key, config_value)
            VALUES ('emergency_reason', ?)
            ON DUPLICATE KEY UPDATE config_value = ?
        ")->execute([$reason, $reason]);

        // جلب كل الأوردرات اللي لسه شغالة (مش Delivered أو Cancelled)
        $stmt = $db->query("
            SELECT order_id, status, client_id
            FROM `order`
            WHERE status NOT IN ('Delivered', 'Cancelled', 'Shipped')
        ");
        $activeOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'emergency'     => true,
            'reason'        => $reason,
            'frozen_orders' => $activeOrders,
            'message'       => 'Warehouse locked. All operations suspended.'
        ];
    }

    return [
        'emergency' => false,
        'message'   => 'Warehouse back to normal operation.'
    ];
}

// بيتكال في أول كل عملية عشان يتأكد مش في طوارئ
public function isEmergencyMode() {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT config_value FROM warehouse_config WHERE config_key = 'emergency_mode'
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['config_value'] === '1';
}
}