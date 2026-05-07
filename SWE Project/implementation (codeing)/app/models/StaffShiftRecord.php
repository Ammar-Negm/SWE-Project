<?php

class StaffShiftRecord {
    // الخصائص (Properties) مطابقة لأعمدة الجدول
    public $shift_id;
    public $staff_id;
    public $login_time;
    public $logout_time;
    public $hours_worked;

    private $db;

    // عند إنشاء الكائن، بنمرر اتصال قاعدة البيانات
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * تسجيل دخول (Login)
     */
    public function login($staff_id) {
        $sql = "INSERT INTO staff_shift_record (staff_id, login_time) VALUES (:staff_id, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':staff_id', $staff_id);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId(); // بيرجع رقم الشفت اللي اتفتح
        }
        return false;
    }

    /**
     * تسجيل خروج (Logout) وحساب الساعات
     */
    public function logout($shift_id) {
        // أولاً: هنجيب وقت الدخول عشان نحسب الفرق
        $sql = "SELECT login_time FROM staff_shift_record WHERE shift_id = :shift_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':shift_id' => $shift_id]);
        $shift = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($shift) {
            $login_time = new DateTime($shift['login_time']);
            $logout_time = new DateTime(); // الوقت الحالي
            
            // حساب فرق الساعات
            $interval = $login_time->diff($logout_time);
            $hours = $interval->h + ($interval->i / 60) + ($interval->days * 24);

            // تحديث الجدول
            $updateSql = "UPDATE staff_shift_record 
                          SET logout_time = NOW(), hours_worked = :hours 
                          WHERE shift_id = :shift_id";
            $updateStmt = $this->db->prepare($updateSql);
            return $updateStmt->execute([
                ':hours' => round($hours, 2),
                ':shift_id' => $shift_id
            ]);
        }
        return false;
    }
}