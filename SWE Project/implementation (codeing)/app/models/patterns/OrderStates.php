<?php
// ============================================================
// FILE: app/models/patterns/OrderStates.php
// State Pattern — Interface + all 6 concrete state classes
//
// Flow: Pending → Picking → Packing → Shipped → Delivered
//       Any state → Cancelled (except Delivered)
// ============================================================

interface OrderStateInterface {
    public function advance(Order $order): void;
    public function cancel(Order $order): void;
    public function getName(): string;
}

class PendingState implements OrderStateInterface {
    public function advance(Order $order): void { $order->setState(new PickingState()); }
    public function cancel(Order $order): void  { $order->setState(new CancelledState()); }
    public function getName(): string { return 'Pending'; }
}

class PickingState implements OrderStateInterface {
    public function advance(Order $order): void { $order->setState(new PackingState()); }
    public function cancel(Order $order): void  { $order->setState(new CancelledState()); }
    public function getName(): string { return 'Picking'; }
}

class PackingState implements OrderStateInterface {
    public function advance(Order $order): void { $order->setState(new ShippedState()); }
    public function cancel(Order $order): void  { $order->setState(new CancelledState()); }
    public function getName(): string { return 'Packing'; }
}

class ShippedState implements OrderStateInterface {
    public function advance(Order $order): void { $order->setState(new DeliveredState()); }
    public function cancel(Order $order): void  { $order->setState(new CancelledState()); }
    public function getName(): string { return 'Shipped'; }
}

class DeliveredState implements OrderStateInterface {
    public function advance(Order $order): void {
        throw new Exception("Order already delivered. No further transitions allowed.");
    }
    public function cancel(Order $order): void {
        throw new Exception("Cannot cancel a delivered order.");
    }
    public function getName(): string { return 'Delivered'; }
}

class CancelledState implements OrderStateInterface {
    public function advance(Order $order): void {
        throw new Exception("Cannot advance a cancelled order.");
    }
    public function cancel(Order $order): void { /* already cancelled — silent */ }
    public function getName(): string { return 'Cancelled'; }
}
