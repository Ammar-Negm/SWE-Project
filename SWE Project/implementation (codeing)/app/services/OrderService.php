<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/PickList.php';
require_once __DIR__ . '/../core/Database.php';

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
}