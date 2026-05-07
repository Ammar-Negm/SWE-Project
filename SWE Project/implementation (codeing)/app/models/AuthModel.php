<?php

class AuthModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findUserByEmailAndRole($email, $role)
    {
        $tableMap = [
            'manager'  => ['table' => 'warehouse_manager', 'id' => 'manager_id'],
            'staff'    => ['table' => 'floorstaff',        'id' => 'staff_id'],
            'supplier' => ['table' => 'supplier',          'id' => 'supplier_id'],
            'client'   => ['table' => 'client',            'id' => 'client_id'],
        ];

        if (!isset($tableMap[$role])) return null;

        $table = $tableMap[$role]['table'];
        $idCol = $tableMap[$role]['id'];

        $stmt = $this->db->prepare(
            "SELECT {$idCol} AS id, name, email, password
             FROM {$table}
             WHERE email = :email
             LIMIT 1"
        );

        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}