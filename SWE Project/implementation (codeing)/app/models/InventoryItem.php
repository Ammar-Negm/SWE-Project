<?php
// ============================================================
// FILE: app/models/InventoryItem.php
// MODIFIED — Observer Pattern added
//
// Changes from original:
//   + require_once for Observer files
//   + private $observers = [] property
//   + attach() method
//   + notify() private method
//   + updateQuantity() now calls notify() after DB update
//
// Everything else is identical to the original.
// ============================================================
require_once __DIR__ . '/patterns/StockObserverInterface.php';
require_once __DIR__ . '/patterns/ReorderObserver.php';

class InventoryItem {
    private $db;

    // ★ Observer Pattern — holds all attached observers
    private array $observers = [];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ★ attach() — register an observer
    // Usage: $inv->attach(new ReorderObserver());
    public function attach(StockObserverInterface $observer): void {
        $this->observers[] = $observer;
    }

    // ★ notify() — fire all observers (private, called by updateQuantity only)
    private function notify(int $inv_item_id, int $product_id, int $newQty): void {
        foreach ($this->observers as $observer) {
            $observer->update($inv_item_id, $product_id, $newQty);
        }
    }

    // UNCHANGED — create()
    public function create($product_id, $bin_id, $quantity, $status = 'Available') {
    // هل المنتج موجود بالفعل في نفس الـ bin؟
    $check = $this->db->prepare("
        SELECT inv_item_id, quantity 
        FROM inventory_item 
        WHERE product_id = :product_id AND bin_id = :bin_id
        LIMIT 1
    ");
    $check->execute([
        ":product_id" => $product_id,
        ":bin_id"     => $bin_id
    ]);

    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // لو موجود، حدّث الكمية بدل insert
        $newQty = (int)$existing['quantity'] + (int)$quantity;

        $update = $this->db->prepare("
            UPDATE inventory_item
            SET quantity = :quantity, status = :status
            WHERE inv_item_id = :id
        ");

        return $update->execute([
            ":quantity" => $newQty,
            ":status"   => $status,
            ":id"       => $existing['inv_item_id']
        ]);
    }

    // لو مش موجود، اعمل insert جديد
    $sql = "INSERT INTO inventory_item (product_id, bin_id, quantity, status)
            VALUES (:product_id, :bin_id, :quantity, :status)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ":product_id" => $product_id,
        ":bin_id"     => $bin_id,
        ":quantity"   => $quantity,
        ":status"     => $status
    ]);
}

    // UNCHANGED — getById()
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM inventory_item WHERE inv_item_id = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // MODIFIED — updateQuantity()
    // ★ Calls notify() after DB update so observers fire automatically
    public function updateQuantity($id, $quantity) {
        // original DB update — unchanged
        $stmt = $this->db->prepare("UPDATE inventory_item SET quantity = :q WHERE inv_item_id = :id");
        $result = $stmt->execute([":id" => $id, ":q" => $quantity]);

        // ★ fire observers if update succeeded and observers are attached
        if ($result && count($this->observers) > 0) {
            $item = $this->getById($id);
            if ($item) {
                $this->notify((int)$id, (int)$item['product_id'], (int)$quantity);
            }
        }

        return $result;
    }

    // UNCHANGED — updateStatus()
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE inventory_item SET status = :s WHERE inv_item_id = :id");
        return $stmt->execute([":id" => $id, ":s" => $status]);
    }

    // UNCHANGED — delete()
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM inventory_item WHERE inv_item_id = :id");
        return $stmt->execute([":id" => $id]);
    }
}
