<?php

class ZoneModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM zone ORDER BY zone_id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM zone WHERE zone_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO zone (zone_name, max_capacity)
              VALUES (:zone_name, :max_capacity)"
        );
        return $stmt->execute([
            ':zone_name'    => $data['zone_name'],
            ':max_capacity' => $data['max_capacity'],
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE zone
              SET zone_name = :zone_name, max_capacity = :max_capacity
              WHERE zone_id = :id"
        );
        return $stmt->execute([
            ':id'           => $id,
            ':zone_name'    => $data['zone_name'],
            ':max_capacity' => $data['max_capacity'],
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM zone WHERE zone_id = :id");
        return $stmt->execute([':id' => $id]);
    }
}