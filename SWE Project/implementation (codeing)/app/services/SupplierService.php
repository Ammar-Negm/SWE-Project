<?php

require_once __DIR__ . '/../models/InventoryItem.php';
require_once __DIR__ . '/../core/Database.php';

class SupplierService {

    // Supplier QC Workflow
    public function qualityCheck($item_id) {
        $inventory = new InventoryItem();

        $random = rand(1,100);

        if ($random > 80) {
            return $inventory->updateStatus($item_id, 'Rejected');
        } else {
            return $inventory->updateStatus($item_id, 'Approved');
        }
    }

    // Supplier Performance Analytics
    public function supplierScore($supplier_id) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN status='Rejected' THEN 1 ELSE 0 END) as rejected
            FROM inventory_item
            WHERE supplier_id = ?
        ");

        $stmt->execute([$supplier_id]);
        $data = $stmt->fetch();

        if ($data['total'] == 0) return 100;

        return 100 - (($data['rejected'] / $data['total']) * 100);
    }
}