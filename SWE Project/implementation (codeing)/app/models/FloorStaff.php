<?php
require_once "dad_user.php";
class FloorStaff extends User {
    public function create($shift_start, $shift_end, $productivity_score) {
        $sql = "INSERT INTO floorstaff (name, email, password, shift_start, shift_end, productivity_score) 
                VALUES (:name, :email, :password, :shift_start, :shift_end, :productivity_score)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":name" => $this->name,
            ":email" => $this->email,
            ":password" => $this->password,
            ":shift_start" => $shift_start,
            ":shift_end" => $shift_end,
            ":productivity_score" => $productivity_score
        ]);
    }

    // تعديل بيانات الموظف
public function update($id, $shift_start = null, $shift_end = null, $productivity_score = null) {
    // 1. جلب البيانات الحالية من الداتابيز
    $currentData = $this->getById($id);
    if (!$currentData) return false;

    // 2. مقارنة البيانات: لو المدخل جديد استخدمه، لو فاضي استخدم القديم
    $newName = !empty($this->name) ? $this->name : $currentData['name'];
    $newEmail = !empty($this->email) ? $this->email : $currentData['email'];
    $newPassword = !empty($this->password) ? $this->password : $currentData['password'];
    
    $newShiftStart = !empty($shift_start) ? $shift_start : $currentData['shift_start'];
    $newShiftEnd = !empty($shift_end) ? $shift_end : $currentData['shift_end'];
    $newProdScore = !empty($productivity_score) ? $productivity_score : $currentData['productivity_score'];

    // 3. تنفيذ الـ Update بالقيم النهائية
    $sql = "UPDATE floorstaff 
            SET name = :name, email = :email, password = :password, 
                shift_start = :shift_start, shift_end = :shift_end, productivity_score = :productivity_score
            WHERE staff_id = :id";
            
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ":id" => $id,
        ":name" => $newName,
        ":email" => $newEmail,
        ":password" => $newPassword,
        ":shift_start" => $newShiftStart,
        ":shift_end" => $newShiftEnd,
        ":productivity_score" => $newProdScore
    ]);
}

    // حذف موظف (بيستخدم الدالة اللي في الأب)
    public function delete($id) {
        return $this->deleteRecord('floorstaff', 'staff_id', $id);
    }

    public function getById($id) {
        return $this->getUserData('floorstaff', 'staff_id', $id);
    }
}