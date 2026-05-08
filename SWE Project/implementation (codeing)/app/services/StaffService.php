<?php

require_once __DIR__ . '/../models/StaffShiftRecord.php';
require_once __DIR__ . '/../models/FloorStaff.php';
require_once __DIR__ . '/../../core/Database.php';

class StaffService {

    // User Shift / Time-Clock Manager
    // بيسجل دخول الموظف وخروجه وبيحسب ساعات شغله
    // وبيديك ملخص الشفت + productivity score محسوب

    public function clockIn($staff_id) {
        $db = Database::getInstance()->getConnection();

        // تأكد مش عنده شفت مفتوح
        $stmt = $db->prepare("
            SELECT shift_id FROM staff_shift_record
            WHERE staff_id = ? AND logout_time IS NULL
        ");
        $stmt->execute([$staff_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            return [
                'success' => false,
                'message' => 'Staff already clocked in',
                'shift_id' => $existing['shift_id']
            ];
        }

        $shift = new StaffShiftRecord($db);
        $shift_id = $shift->login($staff_id);

        return [
            'success'  => true,
            'shift_id' => $shift_id,
            'message'  => 'Clock-in recorded at ' . date('H:i:s')
        ];
    }

    public function clockOut($staff_id) {
        $db = Database::getInstance()->getConnection();

        // جلب الشفت المفتوح
        $stmt = $db->prepare("
            SELECT shift_id FROM staff_shift_record
            WHERE staff_id = ? AND logout_time IS NULL
        ");
        $stmt->execute([$staff_id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            return ['success' => false, 'message' => 'No active shift found'];
        }

        $shift = new StaffShiftRecord($db);
        $shift->logout($record['shift_id']);

        // جلب بيانات الشفت بعد ما اتحسب
        $stmt = $db->prepare("
            SELECT * FROM staff_shift_record WHERE shift_id = ?
        ");
        $stmt->execute([$record['shift_id']]);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);

        // حساب عدد التاسكات اللي أنجزها في الشفت ده
        $stmt = $db->prepare("
            SELECT COUNT(*) as completed_tasks
            FROM pick_task pt
            JOIN pick_list pl ON pt.pick_list_id = pl.pick_list_id
            WHERE pl.assigned_staff_id = ?
            AND pt.status = 'Picked'
            AND DATE(pt.updated_at) = CURDATE()
        ");
        $stmt->execute([$staff_id]);
        $tasks = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'success'         => true,
            'shift_id'        => $record['shift_id'],
            'hours_worked'    => $summary['hours_worked'],
            'completed_tasks' => $tasks['completed_tasks'],
            'message'         => 'Clock-out recorded at ' . date('H:i:s')
        ];
    }

    // جلب سجل شفتات موظف معين
    public function getShiftHistory($staff_id) {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT shift_id, login_time, logout_time, hours_worked
            FROM staff_shift_record
            WHERE staff_id = ?
            ORDER BY login_time DESC
        ");
        $stmt->execute([$staff_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ملخص كل الموظفين الشغالين دلوقتي
    public function getActiveClockedIn() {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->query("
            SELECT ssr.shift_id, ssr.staff_id, fs.name, ssr.login_time,
                   TIMESTAMPDIFF(MINUTE, ssr.login_time, NOW()) as minutes_worked
            FROM staff_shift_record ssr
            JOIN floorstaff fs ON ssr.staff_id = fs.staff_id
            WHERE ssr.logout_time IS NULL
            ORDER BY ssr.login_time ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}