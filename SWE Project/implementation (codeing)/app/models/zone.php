<?php
require_once "Database.php";

class Zone
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
    // CREATE — إضافة زون جديد
    // -------------------------------------------------------
    public function create($zone_name, $max_capacity)
    {
        $sql = "INSERT INTO zone (zone_name, max_capacity) 
                VALUES (:zone_name, :max_capacity)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":zone_name"    => $zone_name,
            ":max_capacity" => $max_capacity
        ]);
    }

    // -------------------------------------------------------
    // READ ALL — جلب كل الزونات
    // -------------------------------------------------------
    public function getAll()
    {
        $sql  = "SELECT * FROM zone";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ ONE — جلب زون بالـ ID
    // -------------------------------------------------------
    public function getById($zone_id)
    {
        $sql  = "SELECT * FROM zone WHERE zone_id = :zone_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":zone_id" => $zone_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // UPDATE — تعديل بيانات الزون
    // -------------------------------------------------------
    public function update($zone_id, $zone_name = null, $max_capacity = null)
    {
        // 1. جلب البيانات الحالية
        $currentData = $this->getById($zone_id);
        if (!$currentData) return false;

        // 2. لو المدخل جديد استخدمه، لو فاضي استخدم القديم
        $newZoneName    = !empty($zone_name)    ? $zone_name    : $currentData['zone_name'];
        $newMaxCapacity = !empty($max_capacity) ? $max_capacity : $currentData['max_capacity'];

        // 3. تنفيذ الـ UPDATE
        $sql = "UPDATE zone 
                SET zone_name    = :zone_name, 
                    max_capacity = :max_capacity 
                WHERE zone_id = :zone_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":zone_id"      => $zone_id,
            ":zone_name"    => $newZoneName,
            ":max_capacity" => $newMaxCapacity
        ]);
    }

    // -------------------------------------------------------
    // DELETE — حذف زون
    // -------------------------------------------------------
    public function delete($zone_id)
    {
        $sql  = "DELETE FROM zone WHERE zone_id = :zone_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":zone_id" => $zone_id]);
    }

    // -------------------------------------------------------
    // GET BINS — جلب كل البنات اللي جوه الزون ده
    // -------------------------------------------------------
    public function getBins($zone_id)
    {
        $sql  = "SELECT * FROM bin WHERE zone_id = :zone_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":zone_id" => $zone_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // CHECK CAPACITY — الزون فيه مكان ولا لأ؟
    // بيرجع true لو في مكان متاح، false لو عدى الـ max
    // -------------------------------------------------------
    public function hasCapacity($zone_id, $addedWeight)
    {
        // جمع الـ currentWeight لكل البنات جوه الزون
        $sql  = "SELECT SUM(b.currentWeight) AS total_weight 
                 FROM bin b 
                 WHERE b.zone_id = :zone_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":zone_id" => $zone_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentTotal = $result['total_weight'] ?? 0;

        // جلب الـ max_capacity للزون
        $zone = $this->getById($zone_id);
        if (!$zone) return false;

        // المقارنة
        return ($currentTotal + $addedWeight) <= $zone['max_capacity'];
    }
}
?>