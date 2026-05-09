<?php
require_once "dad_user.php";
class Supplier extends User {

    public function getAll()
{
    $sql = "SELECT * FROM supplier ORDER BY supplier_id DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function create($perf_score) {
        $sql = "INSERT INTO supplier (name, email, password, perf_score) 
                VALUES (:name, :email, :password, :perf_score)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":name" => $this->name,
            ":email" => $this->email,
            ":password" => $this->password,
            ":perf_score" => $perf_score
        ]);
    }

    public function getById($id) {
        return $this->getUserData('supplier', 'supplier_id', $id);
    }
// تعديل بيانات المورد
public function update($id, $perf_score = null) {
    // 1. جلب البيانات الحالية للمورد من الداتابيز باستخدام الـ ID
    $currentData = $this->getById($id);
    
    // إذا لم يجد المورد، يرجع false
    if (!$currentData) {
        return false;
    }

    // 2. فحص الحقول: لو الحقل الجديد فاضي (empty)، نستخدم القيمة القديمة من الداتابيز
    // الخصائص المورثة من كلاس User (الأب)
    $newName     = !empty($this->name)     ? $this->name     : $currentData['name'];
    $newEmail    = !empty($this->email)    ? $this->email    : $currentData['email'];
    $newPassword = !empty($this->password) ? $this->password : $currentData['password'];
    
    // الخصائص الفريدة للمورد (الابن)
    $newPerfScore = !empty($perf_score)    ? $perf_score     : $currentData['perf_score'];

    // 3. تنفيذ جملة الـ UPDATE بالقيم النهائية (سواء كانت جديدة أو القديمة المتبقية)
    $sql = "UPDATE supplier 
            SET name = :name, 
                email = :email, 
                password = :password, 
                perf_score = :perf_score
            WHERE supplier_id = :id";
            
    $stmt = $this->db->prepare($sql);
    
    return $stmt->execute([
        ":id"         => $id,
        ":name"       => $newName,
        ":email"      => $newEmail,
        ":password"   => $newPassword,
        ":perf_score" => $newPerfScore
    ]);
}

public function delete($id) {
    return $this->deleteRecord('supplier', 'supplier_id', $id);
}

}