<?php

class AuditLog {
    private $db;

    public function __construct() {
        // الاعتماد على نفس طريقة الاتصال المستخدمة في الكلاسات الأخرى
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * تسجيل حركة مخزنية (توريد أو سحب)
     * * @param int    $itemId      ID الصنف في جدول inventory_item
     * @param string $action      'SUPPLY' أو 'PICKING'
     * @param int    $amount      الكمية المتغيرة (موجبة للتوريد، سالبة للسحب)
     * @param int    $performerId ID المورد أو الموظف
     * @param string $role        'supplier' أو 'staff'
     * @param int    $refId       ID الشحنة (Shipment) أو التاسك (PickTask)
     */
    public function record($itemId, $action, $amount, $performerId, $role, $refId) {
        try {
            // 1. جلب الكمية الحالية من جدول inventory_item قبل التعديل (Snapshot)
            $stmt = $this->db->prepare("SELECT quantity FROM inventory_item WHERE inv_item_id = :id");
            $stmt->execute([':id' => $itemId]);
            $before = (int)$stmt->fetchColumn();

            // 2. حساب الكمية الجديدة بعد الحركة
            $after = $before + $amount;

            // 3. إدخال السجل في جدول inventory_audit_log
            $sql = "INSERT INTO inventory_audit_log 
                    (inv_item_id, action_type, change_amount, performer_id, performer_role, reference_id, quantity_before, quantity_after, created_at)
                    VALUES (:item, :action, :amount, :p_id, :p_role, :ref, :before, :after, NOW())";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':item'   => $itemId,
                ':action' => $action,
                ':amount' => $amount,
                ':p_id'   => $performerId,
                ':p_role' => $role,
                ':ref'    => $refId,
                ':before' => $before,
                ':after'  => $after
            ]);

        } catch (Exception $e) {
            // يمكن إضافة سجل أخطاء (Error Log) هنا في حالة فشل التسجيل
            return false;
        }
    }

    /**
     * جلب سجل الحركات لصنف معين (لمراجعة التاريخ)
     */
    public function getItemHistory($itemId) {
        $sql = "SELECT * FROM inventory_audit_log WHERE inv_item_id = :id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $itemId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}