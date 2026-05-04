<?php
abstract class User {
    protected $db;
    protected $name;
    protected $email;
    protected $password;

    public function __construct($name = null, $email = null, $password = null) {
        $this->db = Database::getInstance()->getConnection();
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    // دالة عامة لجلب بيانات أي مستخدم بناءً على الجدول والـ ID
    protected function getUserData($table, $idColumn, $id) {
        $sql = "SELECT * FROM $table WHERE $idColumn = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // دالة عامة لحذف أي مستخدم
    protected function deleteRecord($table, $idColumn, $id) {
        $sql = "DELETE FROM $table WHERE $idColumn = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}