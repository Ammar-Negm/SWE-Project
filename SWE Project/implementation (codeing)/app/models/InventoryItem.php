<?php
class InventoryItem {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($product_id, $bin_id, $quantity, $status = 'Available') {
        $sql = "INSERT INTO inventory_item (product_id, bin_id, quantity, status)
                VALUES (:product_id, :bin_id, :quantity, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":product_id" => $product_id,
            ":bin_id" => $bin_id,
            ":quantity" => $quantity,
            ":status" => $status
        ]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM inventory_item WHERE inv_item_id = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($id, $quantity) {
        $stmt = $this->db->prepare("UPDATE inventory_item SET quantity = :q WHERE inv_item_id = :id");
        return $stmt->execute([
            ":id" => $id,
            ":q" => $quantity
        ]);
    }
    

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE inventory_item SET status = :s WHERE inv_item_id = :id");
        return $stmt->execute([
            ":id" => $id,
            ":s" => $status
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM inventory_item WHERE inv_item_id = :id");
        return $stmt->execute([":id" => $id]);
    }
}