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
}