<?php
    require_once __DIR__ . "/../../core/Database.php";
class StaffController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'staff') {
            header('Location: index.php?url=Auth/login');
            exit;
        }

        require_once __DIR__ . "/../models/PickList.php";
        require_once __DIR__ . "/../models/PickTask.php";
        require_once __DIR__ . "/../models/InventoryItem.php";
        require_once __DIR__ . "/../models/AuditLog.php";
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $pickListModel = new PickList();
        $staffId = $_SESSION['user_id'];

        $lists = $pickListModel->getTasksByStaff($staffId);

        $activeTasks = [];
        $completedCount = 0;

        foreach ($lists as $task) {
            if (strtolower($task['task_status']) === 'picked') {
                $completedCount++;
            } else {
                $activeTasks[] = $task;
            }
        }

        $avgSecondsPerPick = 45;
        $distanceWalked = 1.2;

        $this->view("staff/dashboard", compact(
            'activeTasks',
            'completedCount',
            'avgSecondsPerPick',
            'distanceWalked'
        ));
    }

    public function picking()
    {
        $pickListModel = new PickList();
        $staffId = $_SESSION['user_id'];
        $lists = $pickListModel->getTasksByStaff($staffId);

        $this->view("staff/pick-list", compact('lists'));
    }

    public function packing()
{
    $pickListModel = new PickList();
    $staffId = $_SESSION['user_id'];
    $lists = $pickListModel->getTasksByStaff($staffId);

    $packingItems = array_values(array_filter($lists, function ($item) {
        return strtolower($item['task_status']) === 'picked';
    }));

    $expectedWeight = 0;
    foreach ($packingItems as $item) {
        $expectedWeight += (float)($item['quantity_to_pick'] ?? 0);
    }

    $this->view("staff/packing-station", [
        'packingItems'   => $packingItems,
        'expectedWeight' => $expectedWeight
    ]);
}

    public function qc()
{
    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("
        SELECT po.po_id, po.po_number, po.status, poi.product_id, poi.quantity_ordered, p.SKU, p.name
        FROM purchaseorder po
        JOIN purchase_order_items poi ON po.po_id = poi.po_id
        JOIN product p ON poi.product_id = p.product_id
        WHERE po.status IN ('shipped', 'partially_shipped')
        ORDER BY po.po_id DESC
        LIMIT 1
    ");
    $stmt->execute();
    $qcItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $poNumber = !empty($qcItems[0]['po_number'])
        ? $qcItems[0]['po_number']
        : (!empty($qcItems[0]['po_id']) ? $qcItems[0]['po_id'] : '');

    $this->view("staff/qc-inspection", [
        'qcItems'  => $qcItems,
        'poNumber' => $poNumber
    ]);
}

    public function updateTask($task_id = null)
    {
        if (!$task_id) {
            header('Location: index.php?url=Staff/picking');
            exit;
        }

        $taskModel = new PickTask();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'complete') {
                $done = $taskModel->completeTask($task_id, $_SESSION['user_id']);

                if ($done) {
                    $_SESSION['success'] = "Task completed successfully.";
                } else {
                    $_SESSION['error'] = "Failed to complete task.";
                }
            } elseif ($action === 'fail') {
                $taskModel->updateStatus($task_id, 'Failed');
                $_SESSION['error'] = "Task marked as failed.";
            }
        }

        header('Location: index.php?url=Staff/picking');
        exit;
    }

    public function submitQC()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?url=Staff/qc');
        exit;
    }

    $po_id       = $_POST['po_id'] ?? '';
    $recv_qty    = $_POST['recv_qty'] ?? [];
    $conditions  = $_POST['condition'] ?? [];
    $statuses    = $_POST['item_status'] ?? [];

    if ($po_id === '') {
        $_SESSION['error'] = "PO ID is missing.";
        header('Location: index.php?url=Staff/qc');
        exit;
    }

    $db = Database::getInstance()->getConnection();

    try {
        $db->beginTransaction();

        $itemsReceived = 1;
        $allApproved = true;

        foreach ($recv_qty as $index => $qty) {
            $qty = (int)$qty;
            $condition = $conditions[$index] ?? 'good';
            $status = $statuses[$index] ?? 'approve';

            if ($status !== 'approve' || $condition === 'damaged') {
                $allApproved = false;
            }

            if ($qty <= 0) {
                $itemsReceived = 0;
            }
        }

        // سجل shipment
        $stmt = $db->prepare("
            INSERT INTO shipment (status, items_received, po_id)
            VALUES (:status, :items_received, :po_id)
        ");
        $stmt->execute([
            ':status'         => $allApproved ? 'qc_passed' : 'qc_flagged',
            ':items_received' => $itemsReceived,
            ':po_id'          => $po_id
        ]);

        // حدث حالة الـ purchase order
        $stmt = $db->prepare("
            UPDATE purchaseorder
            SET status = :status
            WHERE po_id = :po_id
        ");
        $stmt->execute([
            ':status' => $allApproved ? 'delivered' : 'partially_shipped',
            ':po_id'  => $po_id
        ]);

        $db->commit();
        $_SESSION['success'] = "QC report submitted successfully.";

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Failed to submit QC report.";
    }

    header('Location: index.php?url=Staff/qc');
    exit;
}

    public function validateWeight()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?url=Staff/packing');
        exit;
    }

    $actualWeight   = (float)($_POST['actual_weight'] ?? 0);
    $expectedWeight = (float)($_POST['expected_weight'] ?? 0);

    if ($actualWeight <= 0) {
        $_SESSION['error'] = "Please enter actual weight.";
    } else {
        $diff = abs($actualWeight - $expectedWeight);

        if ($diff <= 1) {
            $_SESSION['success'] = "Weight validated successfully.";
        } else {
            $_SESSION['error'] = "Weight mismatch detected.";
        }
    }

    header('Location: index.php?url=Staff/packing');
    exit;
}
    public function generateLabel()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?url=Staff/packing');
        exit;
    }

    $orderId = trim($_POST['order_id'] ?? '');

    if ($orderId === '') {
        $_SESSION['error'] = "Order ID is missing.";
        header('Location: index.php?url=Staff/packing');
        exit;
    }

    $db = Database::getInstance()->getConnection();

    try {
        $qrCode = 'ORD-' . $orderId;
        $trackingNumber = 'TRK-' . time();

        $stmt = $db->prepare("
            INSERT INTO shipping_label (qr_code, status, generated_at, tracking_number, order_id)
            VALUES (:qr_code, 'generated', NOW(), :tracking_number, :order_id)
        ");
        $stmt->execute([
            ':qr_code'         => $qrCode,
            ':tracking_number' => $trackingNumber,
            ':order_id'        => $orderId
        ]);

        $_SESSION['success'] = "Shipping label generated successfully.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to generate shipping label.";
    }

    header('Location: index.php?url=Staff/packing');
    exit;
}

    public function reportMissing()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=Staff/picking');
            exit;
        }

        $_SESSION['success'] = "Missing item report submitted.";
        header('Location: index.php?url=Staff/picking');
        exit;
    }

    public function completePick()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=Staff/picking');
            exit;
        }

        $_SESSION['success'] = "Pick list completed successfully.";
        header('Location: index.php?url=Staff/picking');
        exit;
    }
}