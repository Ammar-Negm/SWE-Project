<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/PickList.php';
require_once __DIR__ . '/../../core/Database.php';

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
}