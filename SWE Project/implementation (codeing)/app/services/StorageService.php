<?php

require_once __DIR__ . '/../models/zone.php';
require_once __DIR__ . '/../models/bin.php';
require_once __DIR__ . '/../models/InventoryItem.php';
require_once __DIR__ . '/../../core/Database.php';


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
// Packing Material Optimizer
// بيختار أنسب مادة تغليف بناءً على وزن وحجم الأوردر
// بيرجع أرخص مادة تغليف مناسبة
public function getBestPackingMaterial($totalWeight, $totalVolume) {
    $db = Database::getInstance()->getConnection();

    // جلب كل المواد المناسبة مرتبة من الأرخص للأغلى
    $stmt = $db->prepare("
        SELECT * FROM packing_material
        WHERE max_weight >= ?
        AND max_volume  >= ?
        ORDER BY unit_cost ASC
        LIMIT 1
    ");
    $stmt->execute([$totalWeight, $totalVolume]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$material) {
        return ['success' => false, 'message' => 'No suitable packing material found'];
    }

    return [
        'success'     => true,
        'material_id' => $material['material_id'],
        'name'        => $material['name'],
        'unit_cost'   => $material['unit_cost']
    ];
}


// Environmental Sensitivity Monitor
// بيشتغل بدون DB — بيتبعتله readings وبيرجع alerts لو في خطر
public function checkEnvironment($zone_id, $temperature, $humidity) {
    
    // Rules ثابتة لكل نوع زون — ممكن تعدل الأرقام حسب مشروعك
    $zoneRules = [
        'default' => ['min_temp' => 0,  'max_temp' => 35, 'min_hum' => 20, 'max_hum' => 80],
        'cold'    => ['min_temp' => -5, 'max_temp' => 8,  'min_hum' => 30, 'max_hum' => 60],
        'dry'     => ['min_temp' => 15, 'max_temp' => 30, 'min_hum' => 10, 'max_hum' => 40],
    ];

    // جلب اسم الزون من الـ DB عشان نعرف نطبق أنهي rule
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT zone_name FROM zone WHERE zone_id = ?");
    $stmt->execute([$zone_id]);
    $zone = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$zone) {
        return ['safe' => false, 'alerts' => [['message' => 'Zone not found']]];
    }

    // اختار الـ rule المناسبة — لو الاسم مش معروف يستخدم default
    $zoneName = strtolower($zone['zone_name']);
    $rule = $zoneRules[$zoneName] ?? $zoneRules['default'];

    $alerts = [];

    if ($temperature < $rule['min_temp'] || $temperature > $rule['max_temp']) {
        $alerts[] = [
            'type'    => 'TEMPERATURE',
            'value'   => $temperature,
            'message' => "Temperature {$temperature}°C out of safe range ({$rule['min_temp']}–{$rule['max_temp']}°C)"
        ];
    }

    if ($humidity < $rule['min_hum'] || $humidity > $rule['max_hum']) {
        $alerts[] = [
            'type'    => 'HUMIDITY',
            'value'   => $humidity,
            'message' => "Humidity {$humidity}% out of safe range ({$rule['min_hum']}–{$rule['max_hum']}%)"
        ];
    }

    return [
        'zone_id'     => $zone_id,
        'zone_name'   => $zone['zone_name'],
        'temperature' => $temperature,
        'humidity'    => $humidity,
        'safe'        => empty($alerts),
        'alerts'      => $alerts
    ];
}

// HAZMAT Guard
// بيشتغل بدون table جديدة — الـ rules محددة في الكود حسب category
// وبيتحقق من الكمية الموجودة في الـ zone مقارنة بالـ limit
public function validateHazmat($product_id, $quantity, $zone_id) {
    $db = Database::getInstance()->getConnection();

    // Hardcoded HAZMAT rules — category => max quantity مسموح بيها في أي zone
    $hazmatRules = [
        'chemical'   => 500,
        'flammable'  => 200,
        'explosive'  => 50,
        'toxic'      => 100,
        'radioactive'=> 10,
    ];

    // جلب category المنتج
    $stmt = $db->prepare("SELECT category FROM product WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        return ['allowed' => false, 'reason' => 'Product not found'];
    }

    $category = strtolower($product['category']);

    // لو الـ category مش في قائمة الـ HAZMAT — المنتج عادي وأوكي
    if (!isset($hazmatRules[$category])) {
        return ['allowed' => true, 'reason' => 'Product is not classified as HAZMAT'];
    }

    $maxAllowed = $hazmatRules[$category];

    // جلب الكمية الموجودة حالياً من نفس الـ category في الـ zone ده
    $stmt = $db->prepare("
        SELECT COALESCE(SUM(ii.quantity), 0) AS current_qty
        FROM inventory_item ii
        JOIN bin b       ON ii.bin_id    = b.bin_id
        JOIN product p   ON ii.product_id = p.product_id
        WHERE b.zone_id   = ?
        AND LOWER(p.category) = ?
    ");
    $stmt->execute([$zone_id, $category]);
    $current = (int)$stmt->fetch(PDO::FETCH_ASSOC)['current_qty'];

    if (($current + $quantity) > $maxAllowed) {
        return [
            'allowed'      => false,
            'reason'       => "Exceeds HAZMAT limit for '{$category}' in this zone",
            'current_qty'  => $current,
            'adding'       => $quantity,
            'max_allowed'  => $maxAllowed
        ];
    }

    return [
        'allowed'     => true,
        'category'    => $category,
        'current_qty' => $current,
        'adding'      => $quantity,
        'remaining'   => $maxAllowed - ($current + $quantity),
        'max_allowed' => $maxAllowed
    ];
}
}