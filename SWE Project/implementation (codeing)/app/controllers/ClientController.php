<?php
// استدعاء الموديلات المطلوبة
require_once __DIR__ . "/../models/client.php";
require_once __DIR__ . "/../models/order.php";
require_once __DIR__ . "/../models/PickList.php";
require_once __DIR__ . "/../models/PickTask.php";

class ClientController extends Controller {
    private $clientModel;
    private $orderModel;
    private $pickListModel;
    private $pickTaskModel;

    public function __construct() {
        // بنجهز الـ Objects من الموديلات
        $this->clientModel   = new client();
        $this->orderModel    = new order();
        $this->pickListModel = new PickList();
        $this->pickTaskModel = new PickTask();
    }

    // 1. عرض صفحة عمل الأوردر
    public function createOrder() {
        // ممكن هنا نجيب المنتجات من الداتابيز عشان العميل يختار منها
        require_once __DIR__ . "/../models/ProductModel.php";
        $productModel = new ProductModel();
        $products = $productModel->getAll();
        
        $this->view("client/create-order", ['products' => $products]);
    }

    // 2. معالجة بيانات الأوردر (أهم جزء)
    public function submitOrder()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $clientId    = $_SESSION['user_id'] ?? ($_POST['client_id'] ?? null);
        $totalWeight = $_POST['total_weight'] ?? 0;
        $totalCost   = $_POST['total_cost'] ?? 0;
        $productIds  = $_POST['product_id'] ?? [];
        $quantities  = $_POST['qty'] ?? [];

        require_once __DIR__ . "/../models/order.php";
        require_once __DIR__ . "/../models/PickList.php";
        require_once __DIR__ . "/../models/PickTask.php";
        require_once __DIR__ . "/../models/client.php";
        require_once __DIR__ . "/../models/FloorStaff.php";

        $orderModel   = new Order();
        $plModel      = new PickList();
        $ptModel      = new PickTask();
        $clientModel  = new Client();
        $staffModel   = new FloorStaff('', '', '');
        $db           = Database::getInstance()->getConnection();

        $orderId = $orderModel->create($clientId);

        if ($orderId) {
            $orderModel->updateTotals($orderId, $totalWeight, $totalCost);

            // pick list لكل staff داخل نفس order
            $staffPickLists = [];

            // index لكل zone علشان round robin
            $zoneIndexes = [];

            foreach ($productIds as $index => $pId) {
                $qtyRequested = (int)($quantities[$index] ?? 0);

                if (!$pId || $qtyRequested <= 0) {
                    continue;
                }

                // هات inventory item + zone
                $sql = "SELECT 
                            ii.inv_item_id,
                            ii.product_id,
                            ii.quantity,
                            z.zone_name
                        FROM inventory_item ii
                        JOIN bin b ON ii.bin_id = b.bin_id
                        JOIN zone z ON b.zone_id = z.zone_id
                        WHERE ii.product_id = :pid
                        AND ii.quantity >= :qty
                        ORDER BY ii.inv_item_id ASC
                        LIMIT 1";

                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':pid' => $pId,
                    ':qty' => $qtyRequested
                ]);
                $inventory = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$inventory) {
                    continue;
                }

                $zoneName = $inventory['zone_name'];

                // هات كل staff في نفس الزون
                $staffMembers = $staffModel->getByPrimaryZone($zoneName);

                if (empty($staffMembers)) {
                    continue;
                }

                // لو أول مرة نقابل الزون، ابدأ من أول staff
                if (!isset($zoneIndexes[$zoneName])) {
                    $zoneIndexes[$zoneName] = 0;
                }

                // اختار staff الحالي
                $currentIndex = $zoneIndexes[$zoneName] % count($staffMembers);
                $assignedStaff = $staffMembers[$currentIndex];
                $staffId = $assignedStaff['staff_id'];

                // زوّد المؤشر للمنتج اللي بعده في نفس الزون
                $zoneIndexes[$zoneName]++;

                // اعمل pick list مرة واحدة فقط لكل staff في نفس order
                if (!isset($staffPickLists[$staffId])) {
                    $pickListId = $plModel->create($staffId);
                    $this->dbDirectLink($orderId, $pickListId);
                    $staffPickLists[$staffId] = $pickListId;
                } else {
                    $pickListId = $staffPickLists[$staffId];
                }

                // أنشئ task
                $ptModel->create($pickListId, $inventory['inv_item_id'], $qtyRequested);
            }

            $clientModel->incrementOrderCount($clientId);
            $clientModel->addLoyaltyPoints($clientId, floor($totalCost / 100));

            header('Location: index.php?url=Client/createOrder&status=success');
            exit;
        }
    }
}

    // فانكشن مساعدة للربط السريع في الجدول الوسيط
    private function dbDirectLink($orderId, $plId) {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO picklist_order (pick_list_id, order_id) VALUES (?, ?)";
        $db->prepare($sql)->execute([$plId, $orderId]);
    }

    // فانكشن مساعدة لربط الأوردر بالـ PickList في الجدول الوسيط
    private function linkOrderToPickList($orderId, $pickListId) {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO picklist_order (pick_list_id, order_id) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$pickListId, $orderId]);
    }
}