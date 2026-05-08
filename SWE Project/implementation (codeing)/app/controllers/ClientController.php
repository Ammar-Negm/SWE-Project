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
            // 1. استلام البيانات
            $clientId    = $_SESSION['user_id'] ?? $_POST['client_id'];
            $totalWeight = $_POST['total_weight'] ?? 0;
            $totalCost   = $_POST['total_cost'] ?? 0;
            $productIds  = $_POST['product_id'] ?? []; 
            $quantities  = $_POST['qty'] ?? [];

            // 2. استدعاء الموديلات
            require_once __DIR__ . "/../models/Order.php";
            require_once __DIR__ . "/../models/PickList.php";
            require_once __DIR__ . "/../models/PickTask.php";
            require_once __DIR__ . "/../models/Client.php";
            require_once __DIR__ . "/../models/InventoryItem.php"; // الموديل الجديد بتاعك

            $orderModel = new Order();
            $plModel    = new PickList();
            $ptModel    = new PickTask();
            $clientModel = new Client();
            $inventoryModel = new InventoryItem();

            // --- الخطوة الأولى: إنشاء سجل الأوردر ---
            $orderId = $orderModel->create($clientId);
            
            if ($orderId) {
                $orderModel->updateTotals($orderId, $totalWeight, $totalCost);

                // --- الخطوة الثانية: إنشاء Pick List وربطها ---
                $pickListId = $plModel->create(null); 
                $this->dbDirectLink($orderId, $pickListId);

                // --- الخطوة الثالثة: الربط مع المخزن وإنشاء الـ Tasks ---
                foreach ($productIds as $index => $pId) {
                    $qtyRequested = $quantities[$index];

                    // البحث عن inv_item_id للمنتج ده من الداتابيز
                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->prepare("SELECT inv_item_id FROM inventory_item WHERE product_id = :pid AND quantity >= :qty LIMIT 1");
                    $stmt->execute([':pid' => $pId, ':qty' => $qtyRequested]);
                    $inventory = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($inventory) {
                        // دلوقتي بنبعت inv_item_id صحيح موجود فعلاً في الداتابيز
                        $ptModel->create($pickListId, $inventory['inv_item_id'], $qtyRequested);
                    } else {
                        // لو الصنف مش موجود في المخزن بالكمية دي، ممكن تطلع Error للمدير أو العميل
                        // حالياً هنكمل عشان الفلو ما يقفش، بس في الشغل الحقيقي بنعمل "Backorder"
                        continue; 
                    }
                }

                // --- الخطوة الرابعة: تحديث بيانات العميل ---
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