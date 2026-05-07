<?php
require_once "dad_user.php";
class WarehouseManager extends User {
    public function create() {
        $sql = "INSERT INTO warehouse_manager (name, email, password) 
                VALUES (:name, :email, :password)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":name" => $this->name,
            ":email" => $this->email,
            ":password" => $this->password,
        ]);
    }

    public function getById($id) {
        return $this->getUserData('warehouse_manager', 'manager_id', $id);
    }


    // تعديل بيانات مدير المخزن


public function delete($id) {
    return $this->deleteRecord('warehouse_manager', 'manager_id', $id);
}
//بفكر اشيل حوار التعديل ده 
//////////////////////////////////////////////////////
public function update($id) {
    // 1. جلب البيانات الحالية للمدير من الداتابيز للتأكد من وجوده وللمقارنة
    $currentData = $this->getById($id);
    
    if (!$currentData) {
        return false;
    }

    // 2. التحقق من القيم: لو الخاصية في الكائن (Object) فاضية، نستخدم القيمة المخزنة حالياً
    // بنستخدم !empty للتأكد إن المستخدم دخل نص جديد فعلاً
    $newName     = !empty($this->name)     ? $this->name     : $currentData['name'];
    $newEmail    = !empty($this->email)    ? $this->email    : $currentData['email'];
    $newPassword = !empty($this->password) ? $this->password : $currentData['password'];

    // 3. تنفيذ الاستعلام (SQL Query) لتحديث الجدول الخاص بالمديرين
    $sql = "UPDATE warehouse_manager 
            SET name = :name, 
                email = :email, 
                password = :password
            WHERE manager_id = :id";
            
    $stmt = $this->db->prepare($sql);
    
    return $stmt->execute([
        ":id"       => $id,
        ":name"     => $newName,
        ":email"    => $newEmail,
        ":password" => $newPassword
    ]);
}

}