<?php
class PickList {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($assigned_staff_id = null) {
        $sql = "INSERT INTO pick_list (assigned_staff_id) VALUES (:staff)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":staff" => $assigned_staff_id]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pick_list WHERE pick_list_id = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function assignStaff($pick_list_id, $staff_id) {
        $stmt = $this->db->prepare("UPDATE pick_list SET assigned_staff_id = :s WHERE pick_list_id = :id");
        return $stmt->execute([
            ":id" => $pick_list_id,
            ":s" => $staff_id
        ]);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE pick_list SET status = :s WHERE pick_list_id = :id");
        return $stmt->execute([
            ":id" => $id,
            ":s" => $status
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pick_list WHERE pick_list_id = :id");
        return $stmt->execute([":id" => $id]);
    }

    //sus
    /////////////////////////////////////////////////////////

    // ... الكود القديم ...

    public function getTasks($pick_list_id) {
        $sql = "SELECT pt.*, ii.bin_id, p.name as product_name 
                FROM pick_task pt
                JOIN inventory_item ii ON pt.inv_item_id = ii.inv_item_id
                JOIN product p ON ii.product_id = p.product_id
                WHERE pt.pick_list_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $pick_list_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
