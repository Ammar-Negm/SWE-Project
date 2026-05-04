<?php

class PurchaseOrder {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($supplier_id, $expected_date = null) {
        $sql = "INSERT INTO purchaseorder (supplier_id, expected_delivery_date)
                VALUES (:supplier_id, :expected_date)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":supplier_id" => $supplier_id,
            ":expected_date" => $expected_date
        ]);
    }

    public function getById($po_id) {
        $sql = "SELECT * FROM purchaseorder WHERE po_id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([":id" => $po_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBySupplier($supplier_id) {
        $sql = "SELECT * FROM purchaseorder WHERE supplier_id = :sid";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([":sid" => $supplier_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($po_id, $status) {
        $sql = "UPDATE purchaseorder SET status = :status WHERE po_id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":status" => $status,
            ":id" => $po_id
        ]);
    }
}