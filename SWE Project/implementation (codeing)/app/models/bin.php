<?php
require_once "Database.php";

class Bin
{
    private $db;

    // -------------------------------------------------------
    // Constructor — gets the PDO connection from the Singleton
    // -------------------------------------------------------
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // -------------------------------------------------------
    // CREATE — إضافة بن جديد لزون معين
    // -------------------------------------------------------
    public function create($zone_id, $maxWeight, $shelfLocation)
    {
        $sql = "INSERT INTO bin (zone_id, maxWeight, shelfLocation, currentWeight) 
                VALUES (:zone_id, :maxWeight, :shelfLocation, 0)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":zone_id"       => $zone_id,
            ":maxWeight"     => $maxWeight,
            ":shelfLocation" => $shelfLocation
        ]);
    }

    // -------------------------------------------------------
    // READ ALL — جلب كل البنات
    // -------------------------------------------------------
    public function getAll()
    {
        $sql  = "SELECT * FROM bin";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ ONE — جلب بن بالـ ID
    // -------------------------------------------------------
    public function getById($bin_id)
    {
        $sql  = "SELECT * FROM bin WHERE bin_id = :bin_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":bin_id" => $bin_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ BY ZONE — جلب كل البنات اللي جوه زون معين
    // -------------------------------------------------------
    public function getByZone($zone_id)
    {
        $sql  = "SELECT * FROM bin WHERE zone_id = :zone_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":zone_id" => $zone_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // UPDATE — تعديل بيانات البن
    // -------------------------------------------------------
    public function update($bin_id, $zone_id = null, $maxWeight = null, $shelfLocation = null)
    {
        // 1. جلب البيانات الحالية
        $currentData = $this->getById($bin_id);
        if (!$currentData) return false;

        // 2. لو المدخل جديد استخدمه، لو فاضي استخدم القديم
        $newZoneId       = !empty($zone_id)       ? $zone_id       : $currentData['zone_id'];
        $newMaxWeight    = !empty($maxWeight)      ? $maxWeight     : $currentData['maxWeight'];
        $newShelfLocation = !empty($shelfLocation) ? $shelfLocation : $currentData['shelfLocation'];

        // 3. تنفيذ الـ UPDATE
        $sql = "UPDATE bin 
                SET zone_id       = :zone_id, 
                    maxWeight     = :maxWeight, 
                    shelfLocation = :shelfLocation 
                WHERE bin_id = :bin_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":bin_id"        => $bin_id,
            ":zone_id"       => $newZoneId,
            ":maxWeight"     => $newMaxWeight,
            ":shelfLocation" => $newShelfLocation
        ]);
    }

    // -------------------------------------------------------
    // DELETE — حذف بن
    // -------------------------------------------------------
    public function delete($bin_id)
    {
        $sql  = "DELETE FROM bin WHERE bin_id = :bin_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":bin_id" => $bin_id]);
    }

    // -------------------------------------------------------
    // ADD WEIGHT — زيادة الوزن الحالي للبن لما بضاعة تتحط فيه
    // -------------------------------------------------------
    public function addWeight($bin_id, $weight)
    {
        $sql  = "UPDATE bin 
                 SET currentWeight = currentWeight + :weight 
                 WHERE bin_id = :bin_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":bin_id" => $bin_id,
            ":weight" => $weight
        ]);
    }

    // -------------------------------------------------------
    // REMOVE WEIGHT — تقليل الوزن لما بضاعة تتشال من البن
    // -------------------------------------------------------
    public function removeWeight($bin_id, $weight)
    {
        $sql  = "UPDATE bin 
                 SET currentWeight = currentWeight - :weight 
                 WHERE bin_id = :bin_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":bin_id" => $bin_id,
            ":weight" => $weight
        ]);
    }

    // -------------------------------------------------------
    // HAS CAPACITY — البن فيه مكان لوزن إضافي؟
    // بيرجع true لو ينفع، false لو هيعدي الـ maxWeight
    // -------------------------------------------------------
    public function hasCapacity($bin_id, $addedWeight)
    {
        $bin = $this->getById($bin_id);
        if (!$bin) return false;

        return ($bin['currentWeight'] + $addedWeight) <= $bin['maxWeight'];
    }

    // -------------------------------------------------------
    // GET AVAILABLE BINS — جلب البنات اللي فيها مكان لوزن معين
    // مفيد في UC-1 و UC-2 عشان نقترح مكان للبضاعة الجديدة
    // -------------------------------------------------------
    public function getAvailableBins($zone_id, $requiredWeight)
    {
        $sql  = "SELECT * FROM bin 
                 WHERE zone_id = :zone_id 
                 AND (maxWeight - currentWeight) >= :requiredWeight";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":zone_id"        => $zone_id,
            ":requiredWeight" => $requiredWeight
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>