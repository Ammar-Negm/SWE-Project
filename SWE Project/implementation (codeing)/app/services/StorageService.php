<?php

require_once __DIR__ . '/../models/zone.php';
require_once __DIR__ . '/../models/bin.php';
require_once __DIR__ . '/../models/InventoryItem.php';
require_once __DIR__ . '/../core/Database.php';

class StorageService {

    // Smart Zonal Storage Optimizer
    public function smartStore($product_id, $weight, $quantity) {
        $zoneModel = new Zone();
        $binModel  = new Bin();

        $zones = $zoneModel->getAll();

        foreach ($zones as $zone) {

            if (!$zoneModel->hasCapacity($zone['zone_id'], $weight)) continue;

            $bins = $binModel->getAvailableBins($zone['zone_id'], $weight);

            if (!empty($bins)) {
                $bin = $bins[0];

                $inventory = new InventoryItem();
                $inventory->create($product_id, $bin['bin_id'], $quantity);

                $binModel->addWeight($bin['bin_id'], $weight);

                return $bin;
            }
        }

        return false;
    }

    // IoT Weight Sensor Simulation
    public function simulateWeight($expectedWeight) {
        return $expectedWeight + rand(-2, 2);
    }

    // Parcel Validator
    public function validateParcel($weight, $maxWeight) {
        return $weight <= $maxWeight;
    }

    // Perishable Expiry Watchdog
    public function checkExpiry() {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->query("
            SELECT * FROM inventory_item
            WHERE expiry_date <= NOW()
        ");

        return $stmt->fetchAll();
    }
    // Volumetric Capacity Calculator
public function calculateFit($length, $width, $height, $bin_id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT maxWeight, currentWeight FROM bin WHERE bin_id = :id");
    $stmt->execute([':id' => $bin_id]);
    $bin = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$bin) return ['fits' => false, 'error' => 'Bin not found'];
    $itemVolume = $length * $width * $height;
    $remaining  = $bin['maxWeight'] - $bin['currentWeight'];
    return [
        'fits'        => $itemVolume <= $remaining,
        'itemVolume'  => $itemVolume,
        'remaining'   => $remaining,
        'percentFull' => ($bin['currentWeight'] / $bin['maxWeight']) * 100
    ];
}

// Bulk Breakdown (De-palletize)
public function depalletize($product_id, $totalUnits, $unitWeight, $unitsPerBin = 50) {
    $results   = [];
    $remaining = $totalUnits;
    while ($remaining > 0) {
        $batchQty    = min($remaining, $unitsPerBin);
        $batchWeight = $batchQty * $unitWeight;
        $bin = $this->smartStore($product_id, $batchWeight, $batchQty);
        if (!$bin) {
            $results[] = ['status' => 'failed', 'remaining' => $remaining];
            break;
        }
        $results[] = [
            'status'   => 'stored',
            'bin_id'   => $bin['bin_id'],
            'location' => $bin['shelfLocation'],
            'quantity' => $batchQty
        ];
        $remaining -= $batchQty;
    }
    return $results;
}
}