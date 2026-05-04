<?php

class ProductModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM product ORDER BY product_id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM product WHERE product_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO product (SKU, name, basePrice, category, minStockLevel)
             VALUES (:sku, :name, :price, :category, :minStock)"
        );
        return $stmt->execute([
            ':sku'      => $data['sku'],
            ':name'     => $data['name'],
            ':price'    => $data['price'],
            ':category' => $data['category'],
            ':minStock' => $data['minStock'],
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE product 
             SET SKU=:sku, name=:name, basePrice=:price, category=:category, minStockLevel=:minStock
             WHERE product_id = :id"
        );
        return $stmt->execute([
            ':id'       => $id,
            ':sku'      => $data['sku'],
            ':name'     => $data['name'],
            ':price'    => $data['price'],
            ':category' => $data['category'],
            ':minStock' => $data['minStock'],
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM product WHERE product_id = :id");
        return $stmt->execute([':id' => $id]);
    }
}