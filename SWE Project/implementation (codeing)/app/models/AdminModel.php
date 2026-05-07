<?php

class AdminModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllUsers()
    {
        $sql = "SELECT manager_id AS id, name, email, 'manager' AS role FROM warehouse_manager
                UNION
                SELECT staff_id, name, email, 'staff' FROM floorstaff
                UNION
                SELECT supplier_id, name, email, 'supplier' FROM supplier";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserRole($userId, $roleId)
    {
        // placeholder لحد ما تتفق مع الفريق على الـ roles table
        return true;
    }

    public function getAuditLogs()
    {
        // placeholder لحد ما تتضاف audit_trail table
        return [];
    }

    public function logAction($userId, $action)
    {
        // placeholder
        return true;
    }
    public function createUser($data)
{
    $sql = "INSERT INTO users (name, email, password, phone, role)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        $data['name'],
        $data['email'],
        $data['password'],
        $data['phone'],
        $data['role']
    ]);
}
}