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
public function getAll() {
    $sql = "SELECT po.*, s.name as supplier_name 
            FROM purchaseorder po
            JOIN supplier s ON po.supplier_id = s.supplier_id
            ORDER BY po.po_id DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAllSuppliers() {
    $sql = "SELECT supplier_id, name FROM supplier";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function generatePoNumber() {
    $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM purchaseorder");
    $count = $stmt->fetch()['cnt'];
    return 'PO-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
}

public function createFull($po_number, $supplier_id, $expected_date, $total_value) {
    $sql = "INSERT INTO purchaseorder (po_number, supplier_id, expected_delivery_date, total_value, status)
            VALUES (:po_number, :supplier_id, :expected_date, :total_value, 'pending')";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':po_number'     => $po_number,
        ':supplier_id'   => $supplier_id,
        ':expected_date' => $expected_date,
        ':total_value'   => $total_value,
    ]);
}

//----------------------------------------------

public function getByStatus($status) {
    $sql = "SELECT * FROM purchaseorder WHERE status = :status";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':status' => $status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}