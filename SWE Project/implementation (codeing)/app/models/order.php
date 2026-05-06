<?php
require_once "Database.php";

class Order
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
    // CREATE — إنشاء أوردر جديد لكلايينت
    // -------------------------------------------------------
    public function create($client_id)
    {
        $sql = "INSERT INTO `order` (client_id, status, total_weight, total_cost) 
                VALUES (:client_id, 'Pending', 0, 0.00)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([":client_id" => $client_id]);

        // بيرجع الـ ID بتاع الأوردر الجديد عشان نستخدمه فوراً
        return $this->db->lastInsertId();
    }

    // -------------------------------------------------------
    // READ ALL — جلب كل الأوردرات
    // -------------------------------------------------------
    public function getAll()
    {
        $sql  = "SELECT o.*, c.name AS client_name 
                 FROM `order` o
                 JOIN client c ON o.client_id = c.client_id
                 ORDER BY o.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ ONE — جلب أوردر بالـ ID
    // -------------------------------------------------------
    public function getById($order_id)
    {
        $sql  = "SELECT o.*, c.name AS client_name 
                 FROM `order` o
                 JOIN client c ON o.client_id = c.client_id
                 WHERE o.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":order_id" => $order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ BY STATUS — جلب الأوردرات بحالة معينة
    // مثال: getByStatus('Pending') أو getByStatus('Picking')
    // -------------------------------------------------------
    public function getByStatus($status)
    {
        $sql  = "SELECT o.*, c.name AS client_name 
                 FROM `order` o
                 JOIN client c ON o.client_id = c.client_id
                 WHERE o.status = :status
                 ORDER BY o.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":status" => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ BY CLIENT — جلب كل أوردرات كلايينت معين
    // -------------------------------------------------------
    public function getByClient($client_id)
    {
        $sql  = "SELECT * FROM `order` 
                 WHERE client_id = :client_id 
                 ORDER BY date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":client_id" => $client_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // UPDATE STATUS — تغيير حالة الأوردر (State Machine)
    // الترتيب: Pending → Picking → Packing → Shipped → Delivered
    // -------------------------------------------------------
    public function updateStatus($order_id, $status)
    {
        $allowed = ['Pending', 'Picking', 'Packing', 'Shipped', 'Delivered', 'Cancelled'];

        // تأكد إن الـ status مسموح بيه
        if (!in_array($status, $allowed)) return false;

        $sql  = "UPDATE `order` SET status = :status WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":order_id" => $order_id,
            ":status"   => $status
        ]);
    }

    // -------------------------------------------------------
    // UPDATE TOTALS — تحديث الوزن الكلي والتكلفة الكلية
    // بيتحسب بعد ما يتأكد محتوى الأوردر
    // -------------------------------------------------------
    public function updateTotals($order_id, $total_weight, $total_cost)
    {
        $sql  = "UPDATE `order` 
                 SET total_weight = :total_weight, 
                     total_cost   = :total_cost 
                 WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":order_id"     => $order_id,
            ":total_weight" => $total_weight,
            ":total_cost"   => $total_cost
        ]);
    }

    // -------------------------------------------------------
    // DELETE — حذف أوردر
    // ⚠️ بس لو الأوردر لسه Pending — مش هينحذف لو اتشحن
    // -------------------------------------------------------
    public function delete($order_id)
    {
        // تأكد إن الأوردر لسه Pending قبل الحذف
        $order = $this->getById($order_id);
        if (!$order) return false;
        if ($order['status'] !== 'Pending') return false;

        $sql  = "DELETE FROM `order` WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":order_id" => $order_id]);
    }

    // -------------------------------------------------------
    // GET PICK LISTS — جلب كل بيك ليستس المرتبطة بالأوردر ده
    // بيستخدم جدول الـ picklist_order الوسيط
    // -------------------------------------------------------
    public function getPickLists($order_id)
    {
        $sql  = "SELECT pl.* 
                 FROM pick_list pl
                 JOIN picklist_order plo ON pl.pick_list_id = plo.pick_list_id
                 WHERE plo.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":order_id" => $order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // GET LAST INSERT ID — مساعد داخلي
    // -------------------------------------------------------
    public function getLastId()
    {
        return $this->db->lastInsertId();
    }
}
?>