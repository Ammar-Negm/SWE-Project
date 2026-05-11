<?php
// ============================================================
// FILE: app/models/patterns/StockObserverInterface.php
// Observer Pattern — Interface
// Every observer reacting to stock changes must implement this.
// ============================================================
interface StockObserverInterface {
    public function update(int $inv_item_id, int $product_id, int $newQty): void;
}
