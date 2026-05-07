<?php

require_once __DIR__ . '/../models/PurchaseOrder.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../../core/Database.php';

class PurchaseOrderService {

    // B2B Purchase Order Generator
    public function generatePO($supplier_id) {
        $po = new PurchaseOrder();
        return $po->create($supplier_id);
    }

    // Automated Stock Reorder Trigger
    public function checkReorder($product_id) {
        $productModel = new ProductModel();
        $product = $productModel->getById($product_id);

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT SUM(quantity) as total 
            FROM inventory_item 
            WHERE product_id = ?
        ");
        $stmt->execute([$product_id]);

        $total = $stmt->fetch()['total'];

        // if ($total <= $product['minStockLevel']) {
        //     return $this->generatePO(1); // supplier dynamic later
        // }

        // return false;
        if (!$product) {
    return false;
}

if ($total <= $product['minStockLevel']) {
    return $this->generatePO(1); // supplier dynamic later
}

return false;
    }

    // Inbound Logistics Gatekeeper
    public function validateInbound($po_id, $actualWeight) {
        $po = new PurchaseOrder();
        $data = $po->getById($po_id);

        if (!$data) return false;
        if ($actualWeight <= 0) return false;

        return true;
    }
    // Partial Shipment Reconciliation
public function receivePartialShipment($po_id, $receivedItems) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM po_item WHERE po_id = :po_id");
    $stmt->execute([':po_id' => $po_id]);
    $expectedItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $missing       = [];
    $received      = array_column($receivedItems, 'quantity', 'product_id');
    foreach ($expectedItems as $expected) {
        $pid    = $expected['product_id'];
        $recQty = $received[$pid] ?? 0;
        if ($recQty < $expected['quantity']) {
            $missing[] = [
                'product_id'   => $pid,
                'expected_qty' => $expected['quantity'],
                'received_qty' => $recQty,
                'shortage'     => $expected['quantity'] - $recQty
            ];
        }
        if ($recQty > 0) {
            $storage = new StorageService();
            $storage->smartStore($pid, 1, $recQty);
        }
    }
    $newStatus = empty($missing) ? 'delivered' : 'partial';
    $db->prepare("UPDATE purchaseorder SET status=? WHERE po_id=?")
       ->execute([$newStatus, $po_id]);
    return ['status' => $newStatus, 'missing' => $missing];
}
}