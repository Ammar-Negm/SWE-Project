<?php
// ============================================================
// FILE: app/models/patterns/ReorderObserver.php
// Observer Pattern — Concrete Observer
// Fires when stock drops below min_stock_level.
// Creates a purchase order automatically.
// ============================================================
require_once __DIR__ . '/StockObserverInterface.php';

class ReorderObserver implements StockObserverInterface {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function update(int $inv_item_id, int $product_id, int $newQty): void {
        // 1. get product min_stock_level
        $stmt = $this->db->prepare(
            "SELECT min_stock_level, name FROM product WHERE product_id = :pid"
        );
        $stmt->execute([':pid' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) return;

        // 2. only act if below minimum
        if ($newQty >= (int)$product['min_stock_level']) return;

        // 3. skip if pending reorder already exists
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM purchaseorder WHERE status = 'pending'"
        );
        $stmt->execute();
        if ((int)$stmt->fetchColumn() > 0) return;

        // 4. pick best supplier by perf_score
        $stmt = $this->db->prepare(
            "SELECT supplier_id FROM supplier ORDER BY perf_score DESC LIMIT 1"
        );
        $stmt->execute();
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$supplier) return;

        // 5. create purchase order
        $stmt = $this->db->prepare(
            "INSERT INTO purchaseorder (supplier_id, status, total_value)
             VALUES (:sid, 'pending', 0)"
        );
        $stmt->execute([':sid' => $supplier['supplier_id']]);
        $po_id = $this->db->lastInsertId();

        // 6. log to inventory_audit_log
        $stmt = $this->db->prepare(
            "INSERT INTO inventory_audit_log
             (inv_item_id, action_type, change_amount, performer_id, performer_role, reference_id, quantity_before, quantity_after, created_at)
             VALUES (:item, 'REORDER_TRIGGERED', 0, 0, 'system', :ref, :qty, :qty, NOW())"
        );
        $stmt->execute([
            ':item' => $inv_item_id,
            ':ref'  => $po_id,
            ':qty'  => $newQty
        ]);
    }
}
