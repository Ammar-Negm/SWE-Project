<?php
// ============================================================
// FILE: app/models/order.php
// MODIFIED — State Pattern added
//
// Changes from original:
//   + require_once for OrderStates.php
//   + private $state property (OrderStateInterface)
//   + setState() method
//   + loadState() method
//   + advance() method
//   + cancelOrder() method
//   + updateStatus() now validates via state transition map
//     (returns false for illegal jumps — same interface as before)
//
// Everything else is identical to the original.
// ============================================================
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/patterns/OrderStates.php';

class Order {

    private $db;

    // ★ State Pattern — current state object
    private OrderStateInterface $state;

    public function __construct() {
        $this->db    = Database::getInstance()->getConnection();
        $this->state = new PendingState(); // default in-memory state
    }

    // ★ setState() — called by state classes + by loadState()
    public function setState(OrderStateInterface $state): void {
        $this->state = $state;
    }

    // ★ loadState() — rebuild state object from DB string
    // Call before advance() or cancelOrder() on existing orders
    public function loadState(int $order_id): void {
        $row = $this->getById($order_id);
        if (!$row) return;
        $this->state = $this->mapStringToState($row['status']);
    }

    // ★ private helper — maps DB string → state object
    private function mapStringToState(string $status): OrderStateInterface {
        return match($status) {
            'Picking'   => new PickingState(),
            'Packing'   => new PackingState(),
            'Shipped'   => new ShippedState(),
            'Delivered' => new DeliveredState(),
            'Cancelled' => new CancelledState(),
            default     => new PendingState(),
        };
    }

    // ★ advance() — move to next legal state and save to DB
    public function advance(int $order_id): void {
        $this->loadState($order_id);
        $this->state->advance($this); // state sets $this->state to next state
        $sql  = "UPDATE `order` SET status = :s WHERE order_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':s' => $this->state->getName(), ':id' => $order_id]);
    }

    // ★ cancelOrder() — cancel from any legal state and save to DB
    public function cancelOrder(int $order_id): void {
        $this->loadState($order_id);
        $this->state->cancel($this);
        $sql  = "UPDATE `order` SET status = :s WHERE order_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':s' => $this->state->getName(), ':id' => $order_id]);
    }

    // MODIFIED — updateStatus()
    // ★ Now validates via legal transition map instead of plain whitelist.
    // Same return signature as original (true/false) — controllers unchanged.
    public function updateStatus($order_id, $status) {
        $transitions = [
            'Pending'   => ['Picking',   'Cancelled'],
            'Picking'   => ['Packing',   'Cancelled'],
            'Packing'   => ['Shipped',   'Cancelled'],
            'Shipped'   => ['Delivered', 'Cancelled'],
            'Delivered' => [],
            'Cancelled' => [],
        ];

        // load current state from DB
        $this->loadState((int)$order_id);
        $current = $this->state->getName();
        $allowed = $transitions[$current] ?? [];

        // reject illegal transition
        if (!in_array($status, $allowed)) return false;

        // apply + persist
        $this->setState($this->mapStringToState($status));
        $sql  = "UPDATE `order` SET status = :status WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":order_id" => $order_id, ":status" => $status]);
    }

    // UNCHANGED — create()
    public function create($client_id) {
        $sql  = "INSERT INTO `order` (client_id, status, total_weight, total_cost) 
                 VALUES (:client_id, 'Pending', 0, 0.00)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":client_id" => $client_id]);
        return $this->db->lastInsertId();
    }

    // UNCHANGED — getAll()
    public function getAll() {
        $sql  = "SELECT o.*, c.name AS client_name 
                 FROM `order` o
                 JOIN client c ON o.client_id = c.client_id
                 ORDER BY o.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UNCHANGED — getById()
    public function getById($order_id) {
        $sql  = "SELECT o.*, c.name AS client_name 
                 FROM `order` o
                 JOIN client c ON o.client_id = c.client_id
                 WHERE o.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":order_id" => $order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UNCHANGED — getByStatus()
    public function getByStatus($status) {
        $sql  = "SELECT o.*, c.name AS client_name 
                 FROM `order` o
                 JOIN client c ON o.client_id = c.client_id
                 WHERE o.status = :status
                 ORDER BY o.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":status" => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UNCHANGED — getByClient()
    public function getByClient($client_id) {
        $sql  = "SELECT * FROM `order` WHERE client_id = :client_id ORDER BY date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":client_id" => $client_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UNCHANGED — updateTotals()
    public function updateTotals($order_id, $total_weight, $total_cost) {
        $sql  = "UPDATE `order` SET total_weight = :total_weight, total_cost = :total_cost WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":order_id"     => $order_id,
            ":total_weight" => $total_weight,
            ":total_cost"   => $total_cost
        ]);
    }

    // UNCHANGED — delete()
    public function delete($order_id) {
        $order = $this->getById($order_id);
        if (!$order) return false;
        if ($order['status'] !== 'Pending') return false;
        $sql  = "DELETE FROM `order` WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([":order_id" => $order_id]);
    }

    // UNCHANGED — getPickLists()
    public function getPickLists($order_id) {
        $sql  = "SELECT pl.* FROM pick_list pl
                 JOIN picklist_order plo ON pl.pick_list_id = plo.pick_list_id
                 WHERE plo.order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":order_id" => $order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UNCHANGED — getLastId()
    public function getLastId() {
        return $this->db->lastInsertId();
    }
}
?>
