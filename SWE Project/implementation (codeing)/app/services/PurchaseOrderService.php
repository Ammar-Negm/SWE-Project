<?php

require_once __DIR__ . '/../models/PurchaseOrder.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../core/Database.php';

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
}