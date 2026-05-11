<?php

class SupplierController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'supplier') {
            header('Location: index.php?url=Auth/login');
            exit;
        }

        require_once __DIR__ . "/../models/PurchaseOrder.php";
        require_once __DIR__ . "/../services/SupplierService.php";
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $poModel = new PurchaseOrder();
        $supplierId = $_SESSION['user_id'];

        $orders = $poModel->getBySupplier($supplierId);

        $openPOs = 0;
        $recentOrders = [];
        $pendingInvoices = 0; // مؤقت
        $avgLeadTime = 4.2;   // مؤقت

        if (!empty($orders)) {
            foreach ($orders as $order) {
                $status = strtolower($order['status'] ?? '');

                if (in_array($status, [
                    'pending',
                    'awaiting confirmation',
                    'awaiting_confirmation',
                    'shipped',
                    'in transit',
                    'in_transit'
                ])) {
                    $openPOs++;
                }
            }

            $recentOrders = array_slice($orders, 0, 5);
        }

        $this->view("supplier/dashboard", compact(
            'openPOs',
            'pendingInvoices',
            'avgLeadTime',
            'recentOrders'
        ));
    }

    public function orders()
    {
        $this->listOrders();
    }

    public function listOrders()
    {
        $poModel = new PurchaseOrder();
        $orders = $poModel->getBySupplier($_SESSION['user_id']);

        $this->view("supplier/purchase-orders", compact('orders'));
    }

    public function invoice()
    {
        $poModel = new PurchaseOrder();
        $orders = $poModel->getBySupplier($_SESSION['user_id']);

        // مفيش جدول invoices حاليًا
        // فهنعمل history وهمي من الـ purchase orders اللي حالتها invoice_approved أو فيها أي حالة مناسبة
        $invoiceHistory = [];

        foreach ($orders as $order) {
            $status = strtolower($order['status'] ?? '');

            if (in_array($status, ['invoice_approved', 'matched', 'discrepancy'])) {
                $invoiceHistory[] = [
                    'invoice_number' => 'INV-' . str_pad($order['po_id'], 3, '0', STR_PAD_LEFT),
                    'po_id'          => $order['po_id'],
                    'invoice_date'   => $order['expected_delivery_date'] ?? date('Y-m-d'),
                    'amount'         => $order['total_value'] ?? 0,
                    'match_status'   => ($status === 'invoice_approved' || $status === 'matched') ? 'matched' : 'discrepancy'
                ];
            }
        }

        $this->view("supplier/invoice-manager", compact('orders', 'invoiceHistory'));
    }

    public function updateOrder($po_id = null)
    {
        if (!$po_id) {
            header('Location: index.php?url=Supplier/orders');
            exit;
        }

        $poModel = new PurchaseOrder();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = trim($_POST['status'] ?? '');
            $allowed = [
                'pending',
                'shipped',
                'delivered',
                'cancelled',
                'awaiting confirmation',
                'awaiting_confirmation',
                'in transit',
                'in_transit',
                'partially shipped',
                'partially_shipped'
            ];

            if (in_array(strtolower($status), $allowed)) {
                $poModel->updateStatus($po_id, $status);
                $_SESSION['success'] = "Order status updated successfully.";
            } else {
                $_SESSION['error'] = "Invalid order status.";
            }
        }

        header('Location: index.php?url=Supplier/orders');
        exit;
    }

    public function confirmShipment()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?url=Supplier/orders');
        exit;
    }

    $po_id = trim($_POST['po_id'] ?? '');
    $tracking_number = trim($_POST['tracking_number'] ?? '');
    $ship_date = trim($_POST['ship_date'] ?? '');
    $is_partial = isset($_POST['is_partial']);

    if (empty($po_id) || empty($tracking_number) || empty($ship_date)) {
        $_SESSION['error'] = "All shipment fields are required.";
        header('Location: index.php?url=Supplier/orders');
        exit;
    }

    $db = Database::getInstance()->getConnection();
    $poModel = new PurchaseOrder();

    try {
        $db->beginTransaction();

        // 1) هات كل items الخاصة بالـ PO
        $stmt = $db->prepare("
            SELECT poi.product_id, poi.quantity_ordered, poi.unit_price
            FROM purchase_order_items poi
            WHERE poi.po_id = :po_id
        ");
        $stmt->execute([':po_id' => $po_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            $db->rollBack();
            $_SESSION['error'] = "No items found for this purchase order.";
            header('Location: index.php?url=Supplier/orders');
            exit;
        }

        require_once __DIR__ . "/../models/AuditLog.php";
        $audit = new AuditLog();

        foreach ($items as $item) {
            $productId = (int)$item['product_id'];
            $qtyToAdd  = (int)$item['quantity_ordered'];

            if ($qtyToAdd <= 0) {
                continue;
            }

            // 2) شوف هل المنتج موجود بالفعل في inventory_item
            $stmt = $db->prepare("
                SELECT inv_item_id, quantity
                FROM inventory_item
                WHERE product_id = :product_id
                ORDER BY inv_item_id DESC
                LIMIT 1
            ");
            $stmt->execute([':product_id' => $productId]);
            $inventoryItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($inventoryItem) {
                // موجود -> زوّد الكمية
                $newQty = (int)$inventoryItem['quantity'] + $qtyToAdd;

                $stmt = $db->prepare("
                    UPDATE inventory_item
                    SET quantity = :qty, status = 'Available'
                    WHERE inv_item_id = :id
                ");
                $stmt->execute([
                    ':qty' => $newQty,
                    ':id'  => $inventoryItem['inv_item_id']
                ]);

                $audit->record(
                    $inventoryItem['inv_item_id'],
                    'SUPPLY',
                    $qtyToAdd,
                    $_SESSION['user_id'],
                    'supplier',
                    $po_id
                );
            } else {
                // مش موجود -> هات أول bin متاح وأنشئ inventory_item جديد
                $stmt = $db->prepare("SELECT bin_id FROM bin ORDER BY bin_id ASC LIMIT 1");
                $stmt->execute();
                $binId = $stmt->fetchColumn();

                if (!$binId) {
                    $db->rollBack();
                    $_SESSION['error'] = "No bin available to receive supplied items.";
                    header('Location: index.php?url=Supplier/orders');
                    exit;
                }

                $stmt = $db->prepare("
                    INSERT INTO inventory_item (product_id, bin_id, quantity, status)
                    VALUES (:product_id, :bin_id, :quantity, 'Available')
                ");
                $stmt->execute([
                    ':product_id' => $productId,
                    ':bin_id'     => $binId,
                    ':quantity'   => $qtyToAdd
                ]);

                $newInvItemId = $db->lastInsertId();

                $audit->record(
                    $newInvItemId,
                    'SUPPLY',
                    $qtyToAdd,
                    $_SESSION['user_id'],
                    'supplier',
                    $po_id
                );
            }
        }

        // 3) حدّث حالة الـ PO
        $newStatus = $is_partial ? 'partially_shipped' : 'shipped';
        $poModel->updateStatus($po_id, $newStatus);

        $db->commit();
        $_SESSION['success'] = "Shipment confirmed and inventory updated successfully.";

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Failed to confirm shipment.";
    }

    header('Location: index.php?url=Supplier/orders');
    exit;
}

    public function submitInvoice()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?url=Supplier/invoice');
        exit;
    }

    $po_id = $_POST['po_id'] ?? '';
    $invoice_number = trim($_POST['invoice_number'] ?? '');
    $invoice_date = trim($_POST['invoice_date'] ?? '');

    if ($po_id === '' || $invoice_number === '' || $invoice_date === '') {
        $_SESSION['error'] = "Please fill all invoice fields.";
        header('Location: index.php?url=Supplier/invoice');
        exit;
    }

    // رفع PDF لو موجود
    if (isset($_FILES['invoice_pdf']) && !empty($_FILES['invoice_pdf']['name'])) {
        $uploadDir = "../public/assets/uploads/invoices/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmp  = $_FILES['invoice_pdf']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['invoice_pdf']['name']);
        $target   = $uploadDir . $fileName;

        move_uploaded_file($fileTmp, $target);
    }

    $_SESSION['success'] = "Invoice submitted successfully.";
    header('Location: index.php?url=Supplier/invoice');
    exit;
}
}