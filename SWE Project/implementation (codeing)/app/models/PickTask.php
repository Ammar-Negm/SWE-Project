<?php
require_once __DIR__ . '/InventoryItem.php';
require_once __DIR__ . '/AuditLog.php';
class PickTask {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($pick_list_id, $inv_item_id, $quantity) {
        $sql = "INSERT INTO pick_task (pick_list_id, inv_item_id, quantity_to_pick)
                VALUES (:pl, :inv, :qty)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":pl" => $pick_list_id,
            ":inv" => $inv_item_id,
            ":qty" => $quantity
        ]);
    }

    public function getByPickList($pick_list_id) {
        $stmt = $this->db->prepare("SELECT * FROM pick_task WHERE pick_list_id = :id");
        $stmt->execute([":id" => $pick_list_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE pick_task SET status = :s WHERE picktask_id = :id");
        return $stmt->execute([
            ":id" => $id,
            ":s" => $status
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pick_task WHERE picktask_id = :id");
        return $stmt->execute([":id" => $id]);
    }

    // SUS
    ////////////////////////////////////////////////////////

    // ...
   // ... داخل كلاس PickTask

public function completeTask($task_id, $staff_id) { // أضفنا $staff_id هنا
    $this->db->beginTransaction();
    try {
        // 1. جلب بيانات الـ task
        $stmt = $this->db->prepare("SELECT inv_item_id, quantity_to_pick FROM pick_task WHERE picktask_id = :id");
        $stmt->execute([':id' => $task_id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. تحديث حالة الـ Task لـ 'Picked'
        $stmt = $this->db->prepare("UPDATE pick_task SET status = 'Picked' WHERE picktask_id = :id");
        $stmt->execute([':id' => $task_id]);

        // 3. تحديث الكمية في المخزن
        $inventory = new InventoryItem();
        $currentItem = $inventory->getById($task['inv_item_id']);
        $newQty = $currentItem['quantity'] - $task['quantity_to_pick'];
        $inventory->updateQuantity($task['inv_item_id'], $newQty);

        // 4. تسجيل الـ Audit Log (يجب أن يكون داخل الـ try وقبل الـ commit)
        $audit = new AuditLog();
        $audit->record(
            $task['inv_item_id'], 
            'PICKING', 
            -$task['quantity_to_pick'], 
            $staff_id, // الآن المتغير مُعرّف
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