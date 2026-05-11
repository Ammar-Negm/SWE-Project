<?php
// ============================================================
// FILE: app/models/PickTask.php
// MODIFIED — completeTask() now attaches ReorderObserver
// so stock drop automatically triggers reorder if needed.
// Everything else is identical to the original.
// ============================================================
require_once __DIR__ . '/InventoryItem.php';
require_once __DIR__ . '/AuditLog.php';
require_once __DIR__ . '/patterns/ReorderObserver.php';

class PickTask {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // UNCHANGED — create()
    public function create($pick_list_id, $inv_item_id, $quantity) {
        $sql = "INSERT INTO pick_task (pick_list_id, inv_item_id, quantity_to_pick)
                VALUES (:pl, :inv, :qty)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":pl"  => $pick_list_id,
            ":inv" => $inv_item_id,
            ":qty" => $quantity
        ]);
    }

    // UNCHANGED — getByPickList()
    public function getByPickList($pick_list_id) {
        $stmt = $this->db->prepare("SELECT * FROM pick_task WHERE pick_list_id = :id");
        $stmt->execute([":id" => $pick_list_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UNCHANGED — updateStatus()
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE pick_task SET status = :s WHERE picktask_id = :id");
        return $stmt->execute([":id" => $id, ":s" => $status]);
    }

    // UNCHANGED — delete()
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pick_task WHERE picktask_id = :id");
        return $stmt->execute([":id" => $id]);
    }

    // MODIFIED — completeTask()
    // ★ attaches ReorderObserver before updateQuantity()
    // so if stock drops below min, a PO is created automatically
    public function completeTask($task_id, $staff_id) {
    $this->db->beginTransaction();

    try {
        // 1. get task data
        $stmt = $this->db->prepare(
            "SELECT inv_item_id, quantity_to_pick FROM pick_task WHERE picktask_id = :id"
        );
        $stmt->execute([':id' => $task_id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            $this->db->rollBack();
            return false;
        }

        // 2. get current inventory item
        $inventory = new InventoryItem();
        $currentItem = $inventory->getById($task['inv_item_id']);

        if (!$currentItem) {
            $this->db->rollBack();
            return false;
        }

        $newQty = (int)$currentItem['quantity'] - (int)$task['quantity_to_pick'];

        if ($newQty < 0) {
            $this->db->rollBack();
            return false;
        }

        // 3. update inventory quantity
        $inventory = new InventoryItem();
        $inventory->attach(new ReorderObserver());
        $inventory->updateQuantity($task['inv_item_id'], $newQty);

        // 4. update task status
        $stmt = $this->db->prepare("UPDATE pick_task SET status = 'Picked' WHERE picktask_id = :id");
        $stmt->execute([':id' => $task_id]);

        // 5. write audit log
        $audit = new AuditLog();
        $audit->record(
            $task['inv_item_id'],
            'PICKING',
            -((int)$task['quantity_to_pick']),
            $staff_id,
            'staff',
            $task_id
        );

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        return false;
    }
}
}
