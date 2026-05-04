<?php

class Shipment {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($po_id, $status = "Expected") {
        $sql = "INSERT INTO shipment (po_id, status)
                VALUES (:po_id, :status)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":po_id" => $po_id,
            ":status" => $status
        ]);
    }

    public function getById($shipment_id) {
        $sql = "SELECT * FROM shipment WHERE shipment_id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([":id" => $shipment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByPO($po_id) {
        $sql = "SELECT * FROM shipment WHERE po_id = :po";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([":po" => $po_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($shipment_id, $status) {
        $sql = "UPDATE shipment SET status = :status WHERE shipment_id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ":status" => $status,
            ":id" => $shipment_id
        ]);
    }
}