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
    // Tiered Supplier Selection
public function getRankedSuppliers($product_id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT s.supplier_id, s.name, s.perf_score,
               sp.unit_price, sp.lead_time_days,
               CASE 
                 WHEN s.perf_score >= 90 THEN 'Tier 1 - Preferred'
                 WHEN s.perf_score >= 70 THEN 'Tier 2 - Standard'
                 ELSE 'Tier 3 - Backup'
               END AS tier
        FROM supplier s
        JOIN supplier_product sp ON s.supplier_id = sp.supplier_id
        WHERE sp.product_id = :pid
        ORDER BY s.perf_score DESC
    ");
    $stmt->execute([':pid' => $product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Automated Invoice Matching
public function matchInvoiceToPO($po_id, $invoiceItems) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM po_item WHERE po_id = :id");
    $stmt->execute([':id' => $po_id]);
    $poItems       = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $discrepancies = [];
    $invoiceMap    = array_column($invoiceItems, null, 'sku');
    foreach ($poItems as $poItem) {
        $sku     = $poItem['sku'];
        $invItem = $invoiceMap[$sku] ?? null;
        if (!$invItem) {
            $discrepancies[] = ['sku' => $sku, 'issue' => 'Missing in invoice'];
            continue;
        }
        if ((int)$invItem['quantity'] !== (int)$poItem['quantity']) {
            $discrepancies[] = ['sku' => $sku, 'issue' => 'Quantity mismatch',
                'expected' => $poItem['quantity'], 'received' => $invItem['quantity']];
        }
        $priceDiff = abs($invItem['unit_price'] - $poItem['unit_price']);
        if ($priceDiff > $poItem['unit_price'] * 0.05) {
            $discrepancies[] = ['sku' => $sku, 'issue' => 'Price mismatch',
                'expected' => $poItem['unit_price'], 'received' => $invItem['unit_price']];
        }
    }
    $matched = empty($discrepancies);
    if ($matched) {
        $db->prepare("UPDATE purchaseorder SET status='invoice_approved' WHERE po_id=?")
           ->execute([$po_id]);
    }
    return ['matched' => $matched, 'discrepancies' => $discrepancies];
}
}