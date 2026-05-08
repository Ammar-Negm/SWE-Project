<?php
// require_once "Database.php";
require_once __DIR__ . '/../../core/Database.php';

class ShippingLabel
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
    // CREATE / GENERATE — إنشاء شيبينج ليبل لأوردر معين
    // الـ QR code بيتعمل أوتوماتيك من الـ order_id + timestamp
    // -------------------------------------------------------
    public function generate($order_id)
    {
        // توليد tracking number فريد
        $tracking_number = 'TRK-' . strtoupper(uniqid());

        // توليد QR code string من الـ order_id والـ tracking number
        $qr_code = 'QR-' . $order_id . '-' . $tracking_number;

        $sql = "INSERT INTO shipping_label (order_id, qr_code, tracking_number, status) 
                VALUES (:order_id, :qr_code, :tracking_number, 'Generated')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":order_id"        => $order_id,
            ":qr_code"         => $qr_code,
            ":tracking_number" => $tracking_number
        ]);

        // بيرجع الـ ID بتاع الليبل الجديد
        return $this->db->lastInsertId();
    }

    // -------------------------------------------------------
    // READ ALL — جلب كل الليبلات
    // -------------------------------------------------------
    public function getAll()
    {
        $sql  = "SELECT sl.*, o.status AS order_status 
                 FROM shipping_label sl
                 JOIN `order` o ON sl.order_id = o.order_id
                 ORDER BY sl.generated_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ ONE — جلب ليبل بالـ ID
    // -------------------------------------------------------
    public function getById($label_id)
    {
        $sql  = "SELECT sl.*, o.status AS order_status 
                 FROM shipping_label sl
                 JOIN `order` o ON sl.order_id = o.order_id
                 WHERE sl.label_id = :label_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":label_id" => $label_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ BY ORDER — جلب الليبل بتاع أوردر معين
    // كل أوردر عنده ليبل واحد بس — بيرجع row واحدة
    // -------------------------------------------------------
    public function getByOrder($order_id)
    {
        $sql  = "SELECT * FROM shipping_label WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":order_id" => $order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // READ BY TRACKING NUMBER — جلب ليبل بالـ tracking number
    // مفيد لو كلايينت بيتتبع شحنته
    // -------------------------------------------------------
    public function getByTrackingNumber($tracking_number)
    {
        $sql  = "SELECT sl.*, o.status AS order_status 
                 FROM shipping_label sl
                 JOIN `order` o ON sl.order_id = o.order_id
                 WHERE sl.tracking_number = :tracking_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":tracking_number" => $tracking_number]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------------
    // UPDATE STATUS — تغيير حالة الليبل
    // الترتيب: Generated → Printed → Attached → Dispatched
    // -------------------------------------------------------
    public function updateStatus($label_id, $status)
    {
        $allowed = ['Generated', 'Printed', 'Attached', 'Dispatched'];

        // تأكد إن الـ status مسموح بيه
        if (!in_array($status, $allowed)) return false;

        $sql  = "UPDATE shipping_label 
                 SET status = :status 
                 WHERE label_id = :label_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":label_id" => $label_id,
            ":status"   => $status
        ]);
    }

    // -------------------------------------------------------
    // DELETE — حذف ليبل
    // بس لو لسه Generated أو Printed — مش هينحذف لو اتعلق بالباكدج
    // -------------------------------------------------------
    public function delete($label_id)
    {
        $label = $this->getById($label_id);
        if (!$label) return false;

        // مش هينحذف لو اتعلق بالأوردر فعلاً
        if (in_array($label['status'], ['Attached', 'Dispatched'])) return false;

        $sql  = "DELETE FROM shipping_label WHERE label_id = :label_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":label_id" => $label_id]);
    }

    // -------------------------------------------------------
    // LABEL EXISTS — الأوردر ده عنده ليبل بالفعل؟
    // بيتكال قبل generate() عشان نمنع تكرار الليبل
    // -------------------------------------------------------
    public function labelExists($order_id)
    {
        $sql  = "SELECT COUNT(*) FROM shipping_label WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":order_id" => $order_id]);
        return $stmt->fetchColumn() > 0;
    }
}
?>