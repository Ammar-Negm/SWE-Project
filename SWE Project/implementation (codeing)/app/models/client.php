<?php
require_once "Database.php";

class Client
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
    // CREATE — إضافة كلايينت جديد
    // -------------------------------------------------------
    public function create($name, $shipping_address, $client_type = null)
    {
        $sql = "INSERT INTO client (name, shipping_address, client_type, loyalty_points, total_orders_placed) 
                VALUES (:name, :shipping_address, :client_type, 0, 0)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":name"             => $name,
            ":shipping_address" => $shipping_address,
            ":client_type"      => $client_type
        ]);
    }

    // -------------------------------------------------------
    // READ ALL — جلب كل الكلايينتس
    // -------------------------------------------------------
    public function getAll()
    {
        $sql  = "SELECT * FROM client";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ ONE — جلب كلايينت بالـ ID
    // -------------------------------------------------------
    public function getById($client_id)
    {
        $sql  = "SELECT * FROM client WHERE client_id = :client_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":client_id" => $client_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // SEARCH BY NAME — البحث عن كلايينت باسمه
    // -------------------------------------------------------
    public function searchByName($name)
    {
        $sql  = "SELECT * FROM client WHERE name LIKE :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":name" => "%" . $name . "%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // UPDATE — تعديل بيانات الكلايينت
    // -------------------------------------------------------
    public function update($client_id, $name = null, $shipping_address = null, $client_type = null)
    {
        // 1. جلب البيانات الحالية
        $currentData = $this->getById($client_id);
        if (!$currentData) return false;

        // 2. لو المدخل جديد استخدمه، لو فاضي استخدم القديم
        $newName            = !empty($name)             ? $name             : $currentData['name'];
        $newShippingAddress = !empty($shipping_address) ? $shipping_address : $currentData['shipping_address'];
        $newClientType      = !empty($client_type)      ? $client_type      : $currentData['client_type'];

        // 3. تنفيذ الـ UPDATE
        $sql = "UPDATE client 
                SET name             = :name, 
                    shipping_address = :shipping_address, 
                    client_type      = :client_type 
                WHERE client_id = :client_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":client_id"        => $client_id,
            ":name"             => $newName,
            ":shipping_address" => $newShippingAddress,
            ":client_type"      => $newClientType
        ]);
    }

    // -------------------------------------------------------
    // DELETE — حذف كلايينت
    // -------------------------------------------------------
    public function delete($client_id)
    {
        $sql  = "DELETE FROM client WHERE client_id = :client_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":client_id" => $client_id]);
    }

    // -------------------------------------------------------
    // ADD LOYALTY POINTS — إضافة نقاط ولاء بعد كل أوردر
    // -------------------------------------------------------
    public function addLoyaltyPoints($client_id, $points)
    {
        $sql  = "UPDATE client 
                 SET loyalty_points = loyalty_points + :points 
                 WHERE client_id = :client_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":client_id" => $client_id,
            ":points"    => $points
        ]);
    }

    // -------------------------------------------------------
    // INCREMENT ORDER COUNT — زيادة عداد الأوردرات بعد كل أوردر جديد
    // -------------------------------------------------------
    public function incrementOrderCount($client_id)
    {
        $sql  = "UPDATE client 
                 SET total_orders_placed = total_orders_placed + 1 
                 WHERE client_id = :client_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":client_id" => $client_id]);
    }

    // -------------------------------------------------------
    // GET ORDERS — جلب كل الأوردرات بتاعت الكلايينت ده
    // -------------------------------------------------------
    public function getOrders($client_id)
    {
        $sql  = "SELECT * FROM `order` WHERE client_id = :client_id ORDER BY date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":client_id" => $client_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>