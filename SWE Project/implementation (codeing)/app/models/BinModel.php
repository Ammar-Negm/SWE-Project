<?php

class BinModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("
            SELECT b.*, z.zone_name
            FROM bin b
            JOIN zone z ON b.zone_id = z.zone_id
            ORDER BY b.bin_id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM bin WHERE bin_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByZone($zone_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM bin WHERE zone_id = :zone_id");
        $stmt->execute([':zone_id' => $zone_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO bin (zone_id, maxWeight, shelfLocation)
              VALUES (:zone_id, :maxWeight, :shelfLocation)"
        );
        return $stmt->execute([
            ':zone_id'       => $data['zone_id'],
            ':maxWeight'     => $data['maxWeight'],
            ':shelfLocation' => $data['shelfLocation'],
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE bin
              SET zone_id = :zone_id, maxWeight = :maxWeight, shelfLocation = :shelfLocation
              WHERE bin_id = :id"
        );
        return $stmt->execute([
            ':id'            => $id,
            ':zone_id'       => $data['zone_id'],
            ':maxWeight'     => $data['maxWeight'],
            ':shelfLocation' => $data['shelfLocation'],
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM bin WHERE bin_id = :id");
        return $stmt->execute([':id' => $id]);
    }
}