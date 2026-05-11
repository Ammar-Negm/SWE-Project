<?php
// ============================================================
// FILE: app/models/Shipment.php
// MODIFIED — confirmArrival() now attaches ReorderObserver
// and adds advanceStatus() for the inbound logistics state machine.
// Everything else is identical to the original.
// ============================================================
require_once __DIR__ . '/InventoryItem.php';
require_once __DIR__ . '/AuditLog.php';

class Shipment {
    protected $db;

    // legal inbound state machine transitions
    private array $transitions = [
        'Expected'        => 'AtDock',
        'AtDock'          => 'BeingInspected',
        'BeingInspected'  => 'Stored',
    ];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // UNCHANGED — create()
    public function create($po_id, $status = "Expected") {
        $sql  = "INSERT INTO shipment (po_id, status) VALUES (:po_id, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":po_id" => $po_id, ":status" => $status]);
    }

    // UNCHANGED — getById()
    public function getById($shipment_id) {
        $sql  = "SELECT * FROM shipment WHERE shipment_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $shipment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UNCHANGED — getByPO()
    public function getByPO($po_id) {
        $sql  = "SELECT * FROM shipment WHERE po_id = :po";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":po" => $po_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UNCHANGED — updateStatus() (raw, used internally)
    public function updateStatus($shipment_id, $status) {
        $sql  = "UPDATE shipment SET status = :status WHERE shipment_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":status" => $status, ":id" => $shipment_id]);
    }

    // ★ NEW — advanceStatus()
    // State machine for inbound logistics: Expected → AtDock → BeingInspected → Stored
    // Returns false if transition is not legal.
    public function advanceStatus($shipment_id): bool {
        $shipment = $this->getById($shipment_id);
        if (!$shipment) return false;

        $current = $shipment['status'];
        $next    = $this->transitions[$current] ?? null;

        if (!$next) return false; // terminal or unknown state

        return $this->updateStatus($shipment_id, $next);
    }

    // MODIFIED — confirmArrival()
    // ★ attaches ReorderObserver before updateQuantity()
    public function confirmArrival($shipment_id, $inv_item_id, $qty_received, $supplier_id) {
        // ★ attach ReorderObserver so adding stock can cancel pending reorders
        $inventory = new InventoryItem();
        $inventory->attach(new ReorderObserver());

        $item   = $inventory->getById($inv_item_id);
        $newQty = $item['quantity'] + $qty_received;

        // update quantity — observer fires automatically
        $inventory->updateQuantity($inv_item_id, $newQty);

        // write audit log — unchanged
        $audit = new AuditLog();
        $audit->record(
            $inv_item_id,
            'SUPPLY',
            $qty_received,
            $supplier_id,
            'supplier',
            $shipment_id
        );
    }
}
