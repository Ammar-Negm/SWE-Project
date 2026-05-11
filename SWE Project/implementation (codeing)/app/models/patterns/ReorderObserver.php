<?php
require_once __DIR__ . '/StockObserverInterface.php';

class ReorderObserver implements StockObserverInterface {

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function update(int $inv_item_id, int $product_id, int $newQty): void {
        try {
            // 1) get product data
            $stmt = $this->db->prepare(
                "SELECT product_id, name, basePrice, minStockLevel
                 FROM product
                 WHERE product_id = :pid"
            );
            $stmt->execute([':pid' => $product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) return;

            $minStock  = (int)($product['minStockLevel'] ?? 0);
            $unitPrice = (float)($product['basePrice'] ?? 0);

            // 2) only trigger if below minimum
            if ($newQty >= $minStock) return;

            // 3) prevent duplicate pending reorder for same product
            $stmt = $this->db->prepare(
                "SELECT COUNT(*)
                 FROM purchaseorder po
                 JOIN purchase_order_items poi ON po.po_id = poi.po_id
                 WHERE po.status = 'pending'
                   AND poi.product_id = :pid"
            );
            $stmt->execute([':pid' => $product_id]);

            if ((int)$stmt->fetchColumn() > 0) return;

            // 4) choose best supplier
            $stmt = $this->db->prepare(
                "SELECT supplier_id
                 FROM supplier
                 ORDER BY perf_score DESC
                 LIMIT 1"
            );
            $stmt->execute();
            $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$supplier) return;

            // 5) reorder quantity
            $reorderQty = max($minStock * 2, 1);
            $totalValue = $reorderQty * $unitPrice;
            $expectedDate = date('Y-m-d', strtotime('+7 days'));

            // 6) generate po number
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM purchaseorder");
            $stmt->execute();
            $nextNumber = ((int)$stmt->fetchColumn()) + 1;
            $poNumber = 'PO-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // 7) create purchase order
            $stmt = $this->db->prepare(
                "INSERT INTO purchaseorder
                 (supplier_id, status, total_cost, expected_delivery_date, po_number, total_value, order_date)
                 VALUES (:sid, 'pending', :total_cost, :expected_date, :po_number, :total_value, NOW())"
            );
            $stmt->execute([
                ':sid'           => $supplier['supplier_id'],
                ':total_cost'    => $totalValue,
                ':expected_date' => $expectedDate,
                ':po_number'     => $poNumber,
                ':total_value'   => $totalValue
            ]);

            $poId = $this->db->lastInsertId();

            // 8) insert PO item
            $stmt = $this->db->prepare(
                "INSERT INTO purchase_order_items
                 (po_id, product_id, quantity_ordered, unit_price)
                 VALUES (:po_id, :product_id, :quantity_ordered, :unit_price)"
            );
            $stmt->execute([
                ':po_id'            => $poId,
                ':product_id'       => $product_id,
                ':quantity_ordered' => $reorderQty,
                ':unit_price'       => $unitPrice
            ]);

            // 9) audit log
            $stmt = $this->db->prepare(
                "INSERT INTO inventory_audit_log
                 (inv_item_id, action_type, change_amount, performer_id, performer_role, reference_id, quantity_before, quantity_after, created_at)
                 VALUES (:item, 'REORDER_TRIGGERED', 0, 0, 'system', :ref, :qty, :qty, NOW())"
            );
            $stmt->execute([
                ':item' => $inv_item_id,
                ':ref'  => $poId,
                ':qty'  => $newQty
            ]);

        } catch (Exception $e) {
            return;
        }
    }
}