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

        $stmt->execute([
            ':sku'      => $data['sku'],
            ':name'     => $data['name'],
            ':price'    => $data['price'],
            ':category' => $data['category'],
            ':minStock' => $data['minStock'],
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE product
             SET SKU = :sku,
                 name = :name,
                 basePrice = :price,
                 category = :category,
                 minStockLevel = :minStock
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
        try {
            $this->db->beginTransaction();

            // احذف السجلات المرتبطة في inventory_item أولاً
            $stmt1 = $this->db->prepare("DELETE FROM inventory_item WHERE product_id = :id");
            $stmt1->execute([':id' => $id]);

            // ثم احذف المنتج نفسه
            $stmt2 = $this->db->prepare("DELETE FROM product WHERE product_id = :id");
            $stmt2->execute([':id' => $id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM product");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getLowStockAlerts()
    {
        $sql = "SELECT p.name, p.SKU, ii.quantity, p.minStockLevel, z.zone_name
                FROM product p
                JOIN inventory_item ii ON p.product_id = ii.product_id
                JOIN bin b ON ii.bin_id = b.bin_id
                JOIN zone z ON b.zone_id = z.zone_id
                WHERE ii.quantity <= p.minStockLevel
                ORDER BY ii.quantity ASC LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUpcomingReorders()
    {
        $sql = "SELECT p.SKU, p.name, p.basePrice
                FROM product p
                WHERE p.minStockLevel > 0
                ORDER BY p.minStockLevel DESC
                LIMIT 3";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}