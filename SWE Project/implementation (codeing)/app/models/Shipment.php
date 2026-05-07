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

    // داخل Shipment.php
public function confirmArrival($shipment_id, $inv_item_id, $qty_received, $supplier_id) {
    $inventory = new InventoryItem();
    $item = $inventory->getById($inv_item_id);
    $newQty = $item['quantity'] + $qty_received;

    // 1. تحديث الكمية في المخزن
    $inventory->updateQuantity($inv_item_id, $newQty);

    //audit log start
    $audit = new AuditLog();
    $audit->record(
        $inv_item_id, 
        'SUPPLY', 
        $qty_received,   // الكمية بالموجب لأنها توريد
        $supplier_id,    // الـ ID الخاص بالمورد
        'supplier', 
        $shipment_id     // المرجع هنا هو رقم الشحنة
    );
    //audit log end
}
}