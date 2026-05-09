<?php
require_once __DIR__ . "/../models/AdminModel.php";
require_once __DIR__ . "/../models/ProductModel.php";

class ManagerController extends Controller
{
    private $adminModel;
    private $productModel;

    
        public function __construct()
{
    if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'manager') {
        header('Location: index.php?url=Auth/login');
        exit;
    }

    $this->adminModel   = new AdminModel();
    $this->productModel = new ProductModel();
}


    // public function __construct()
    // {
    //     session_start();    
    //     if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'manager') {
    //         header('Location: index.php?url=Auth/login');    
    //         exit;
    //     }
    //     $this->adminModel   = new AdminModel();
    //     $this->productModel = new ProductModel();
    // }
    

    /* ======================
        MAIN PAGES
    ====================== */

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        // 1. إحصائيات سريعة (Top Stats)
        // إجمالي عدد المنتجات
        $totalSKUs = $this->productModel->getCount(); // هنضيف الفنكشن دي في الموديل
        
        // الأوردرات المفتوحة (Purchase Orders)
        require_once __DIR__ . "/../models/PurchaseOrder.php";
        $poModel = new PurchaseOrder();
        $openPOs = count($poModel->getByStatus('pending') ?? []);

        // 2. الموظفين النشطين (Active Staff)
        require_once __DIR__ . "/../models/PickList.php";
        $plModel = new PickList();
        // جلب الموظفين اللي معاهم قوائم تجميع حالياً
        $activeStaff = $plModel->getActiveStaffWithTasks(); 

        // 3. تنبيهات المخزن (Inventory Alerts)
        // هنجلب المنتجات اللي وصلت للـ minStockLevel
        $alerts = $this->productModel->getLowStockAlerts();

        // 4. إعادة طلب بضاعة (Upcoming Reorders)
        $upcomingReorders = $this->productModel->getUpcomingReorders();

        // إرسال كل الداتا للـ View
        $this->view("manager/dashboard", [
            'totalSKUs' => $totalSKUs,
            'openPOs'   => $openPOs,
            'activeStaff' => $activeStaff,
            'alerts'    => $alerts,
            'upcomingReorders' => $upcomingReorders,
            'capacity'  => 73 // ممكن تحسبها من جدول الـ bins لاحقاً
        ]);
    }

   public function inventory()
    {
        $db = Database::getInstance()->getConnection();
        
        // 1. Query احترافي يجمع بيانات المنتج مع تفاصيل المخزن
        $sql = "SELECT 
                    p.product_id, 
                    p.SKU, 
                    p.name, 
                    p.basePrice, 
                    p.minStockLevel,
                    p.category as prod_cat,
                    IFNULL(SUM(ii.quantity), 0) as total_available,
                    GROUP_CONCAT(DISTINCT z.zone_name SEPARATOR ', ') as zones
                FROM product p
                LEFT JOIN inventory_item ii ON p.product_id = ii.product_id
                LEFT JOIN bin b ON ii.bin_id = b.bin_id
                LEFT JOIN zone z ON b.zone_id = z.zone_id
                GROUP BY p.product_id
                ORDER BY p.product_id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. جلب البنات المتاحة مع اسم الزون عشان تظهر في الـ Modal
        require_once __DIR__ . "/../models/bin.php";
        $binModel = new Bin();
        $bins = $binModel->getBinsWithZoneNames();

        // 3. إرسال البيانات للـ View مرة واحدة فقط
        $this->view("manager/inventory", [
            'products' => $products,
            'bins'     => $bins
        ]);
    }

// عدّل الموجودة
public function procurement()
{
    require_once __DIR__ . "/../models/PurchaseOrder.php";
    $poModel = new PurchaseOrder();
    $orders    = $poModel->getAll();
    $suppliers = $poModel->getAllSuppliers();
    $this->view("manager/procurement", [
        'orders'    => $orders,
        'suppliers' => $suppliers
    ]);
}

// أضف جديدة
public function generatePO()
{
    require_once __DIR__ . "/../models/PurchaseOrder.php";
    $poModel = new PurchaseOrder();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $supplier_id   = $_POST['supplier_id']   ?? '';
        $expected_date = $_POST['expected_date'] ?? null;
        $total_value   = $_POST['total_value']   ?? 0;
        $po_number     = $poModel->generatePoNumber();

        $poModel->createFull($po_number, $supplier_id, $expected_date, $total_value);

        header('Location: index.php?url=Manager/procurement');
        exit;
    }

    $suppliers = $poModel->getAllSuppliers();
    $this->view("manager/procurement", ['suppliers' => $suppliers]);
}


// أضف جديدة
public function viewPO($id)
{
    require_once __DIR__ . "/../models/PurchaseOrder.php";
    $poModel = new PurchaseOrder();
    $order   = $poModel->getById($id);
    $this->view("manager/procurement-view", ['order' => $order]);
}

    public function analytics()
    {
        $this->view("manager/analytics");
    }

    public function supplier()
{
    require_once __DIR__ . "/../models/Supplier.php";
    $s = new Supplier();
    $suppliers = method_exists($s, 'getAll') ? $s->getAll() : [];

    $this->view("manager/supplier-list", ['suppliers' => $suppliers]);
}

    public function zonalOptimizer()
{
    require_once __DIR__ . "/../models/zone.php";
    require_once __DIR__ . "/../models/bin.php";

    $zoneModel = new Zone();
    $binModel  = new Bin();

    $zones = $zoneModel->getAll();
    $bins  = $binModel->getAll();

    $this->view("manager/zonal-optimizer", [
        'zones' => $zones,
        'bins'  => $bins
    ]);
}

    public function systemAdmin()
{
    $users = $this->adminModel->getAllUsers();
    $this->view("manager/system-admin", ['users' => $users]);
}
    public function adduser()
    {
        $this->view("manager/add-user");
    }

    /* ======================
        USER MANAGEMENT
    ====================== */

    public function manageUsers()
    {
        $users = $this->adminModel->getAllUsers();
        $this->view("manager/system-admin", ['users' => $users]);
    }

    public function assignRole($userId, $roleId)
    {
        $this->adminModel->updateUserRole($userId, $roleId);
        header('Location: index.php?url=Manager/manageUsers');
        exit;
    }

    public function viewAuditTrail()
    {
        $logs = $this->adminModel->getAuditLogs();
        $this->view("manager/audit-trail", ['logs' => $logs]);
    }

    public function activateEmergencyMode()
    {
        $this->adminModel->logAction($_SESSION['user_id'], 'EMERGENCY_MODE_ACTIVATED');
        header('Location: index.php?url=Manager/dashboard');
        exit;
    }

    /* ======================
        PRODUCTS
    ====================== */

    public function listProducts()
    {
        $products = $this->productModel->getAll();
        $this->view("manager/products/index", ['products' => $products]);
    }

    public function addProduct() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. Register the product with fallback values to prevent undefined key errors
        $productData = [
            'sku'      => $_POST['sku'] ?? '',
            'name'     => $_POST['name'] ?? '',
            'price'    => $_POST['price'] ?? 0,
            'category' => $_POST['category'] ?? 'Uncategorized', 
            'minStock' => $_POST['minStock'] ?? 0
        ];
        
        $productId = $this->productModel->insert($productData); 

        if ($productId) {
            // 2. Register initial stock in inventory_item
            require_once __DIR__ . "/../models/InventoryItem.php";
            $inventoryModel = new InventoryItem();
            
            $binId = $_POST['bin_id'] ?? null;
            // Default to 0 if initial_qty is somehow missing
            $qty   = $_POST['initial_qty'] ?? 0; 
            
            // Only create an inventory record if a bin was selected and quantity is valid
            if ($binId && $qty > 0) {
                $inventoryModel->create($productId, $binId, $qty, 'Available');
            }
        }

        header('Location: index.php?url=Manager/inventory&success=1');
        exit;
    }
}
  public function editProduct($id = null)
{
    if (!$id) {
        $products = $this->productModel->getAll();
        $this->view("manager/inventory", [
            'products' => $products,
            'error'    => "Product ID is required."
        ]);
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->productModel->update($id, [
            'sku'      => $_POST['sku']      ?? '',
            'name'     => $_POST['name']     ?? '',
            'price'    => $_POST['price']    ?? 0,
            'category' => $_POST['category'] ?? '',
            'minStock' => $_POST['minStock'] ?? 0,
        ]);

        header('Location: index.php?url=Manager/inventory');
        exit;
    }

    $products = $this->productModel->getAll();
    $product  = $this->productModel->getById($id);
    $this->view("manager/inventory", [
        'products' => $products,
        'product'  => $product
    ]);
}

public function deleteProduct($id)
{
    $this->productModel->delete($id);
    header('Location: index.php?url=Manager/inventory'); // غيّر listProducts لـ inventory
    exit;
}


    /* ======================
    ZONES
====================== */

public function listZones()
{
    require_once __DIR__ . "/../models/zone.php";
    $zoneModel = new Zone();
    $zones = $zoneModel->getAll();
    $this->view("manager/zones/index", ['zones' => $zones]);
}

public function addZone()
{
    require_once __DIR__ . "/../models/zone.php";
    $zoneModel = new Zone();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $zone_name    = trim($_POST['zone_name'] ?? '');
        $max_capacity = (int)($_POST['max_capacity'] ?? 0);

        if (empty($zone_name) || $max_capacity <= 0) {
            $this->view("manager/zones/create", [
                'error' => 'Zone name and capacity are required.',
                'old'   => ['zone_name' => $zone_name, 'max_capacity' => $max_capacity],
            ]);
            return;
        }

        $zoneModel->create($zone_name, $max_capacity);
        header('Location: index.php?url=Manager/listZones');
        exit;
    }

    $this->view("manager/zones/create");
}

public function editZone($id)
{
    require_once __DIR__ . "/../models/zone.php";
    $zoneModel = new Zone();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $zone_name    = trim($_POST['zone_name'] ?? '');
        $max_capacity = (int)($_POST['max_capacity'] ?? 0);
        $zoneModel->update($id, $zone_name, $max_capacity);
        header('Location: index.php?url=Manager/listZones');
        exit;
    }

    $zone = $zoneModel->getById($id);
    $this->view("manager/zones/edit", ['zone' => $zone]);
}

public function deleteZone($id)
{
    require_once __DIR__ . "/../models/zone.php";
    $zoneModel = new Zone();
    $zoneModel->delete($id);
    header('Location: index.php?url=Manager/listZones');
    exit;
}


/* ======================
    BINS
====================== */

public function listBins()
{
    require_once __DIR__ . "/../models/bin.php";
    $binModel = new Bin();
    $bins = $binModel->getAll();
    $this->view("manager/bins/index", ['bins' => $bins]);
}

public function addBin()
{
    require_once __DIR__ . "/../models/bin.php";
    require_once __DIR__ . "/../models/zone.php";
    $binModel  = new Bin();
    $zoneModel = new Zone();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $zone_id       = (int)($_POST['zone_id'] ?? 0);
        $maxWeight     = (float)($_POST['maxWeight'] ?? 0);
        $shelfLocation = trim($_POST['shelfLocation'] ?? '');

        if (empty($shelfLocation) || $maxWeight <= 0 || $zone_id <= 0) {
            $zones = $zoneModel->getAll();
            $this->view("manager/bins/create", [
                'error' => 'All fields are required.',
                'zones' => $zones,
                'old'   => compact('zone_id', 'maxWeight', 'shelfLocation'),
            ]);
            return;
        }

        $binModel->create($zone_id, $maxWeight, $shelfLocation);
        header('Location: index.php?url=Manager/listBins');
        exit;
    }

    $zones = $zoneModel->getAll();
    $this->view("manager/bins/create", ['zones' => $zones]);
}

public function editBin($id)
{
    require_once __DIR__ . "/../models/bin.php";
    require_once __DIR__ . "/../models/zone.php";
    $binModel  = new Bin();
    $zoneModel = new Zone();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $zone_id       = (int)($_POST['zone_id'] ?? 0);
        $maxWeight     = (float)($_POST['maxWeight'] ?? 0);
        $shelfLocation = trim($_POST['shelfLocation'] ?? '');
        $binModel->update($id, $zone_id, $maxWeight, $shelfLocation);
        header('Location: index.php?url=Manager/listBins');
        exit;
    }

    $bin   = $binModel->getById($id);
    $zones = $zoneModel->getAll();
    $this->view("manager/bins/edit", ['bin' => $bin, 'zones' => $zones]);
}

public function deleteBin($id)
{
    require_once __DIR__ . "/../models/bin.php";
    $binModel = new Bin();
    $binModel->delete($id);
    header('Location: index.php?url=Manager/listBins');
    exit;
}
public function addSupplier()
{
    require_once __DIR__ . "/../models/Supplier.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            $_SESSION['error'] = "Name and email are required.";
            header('Location: index.php?url=Manager/supplier');
            exit;
        }

        // بما إن الفورم مفيهوش password حاليًا
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);

        $s = new Supplier($name, $email, $defaultPassword);
        $s->create(100);

        $_SESSION['success'] = "Supplier added successfully.";
        header('Location: index.php?url=Manager/supplier');
        exit;
    }

    $this->view("manager/supplier-list");
}

    public function editSupplier($id)
    {
        require_once __DIR__ . "/../models/Supplier.php";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $s = new Supplier(
                trim($_POST['name']     ?? ''),
                trim($_POST['email']    ?? ''),
                trim($_POST['password'] ?? '')
            );
            $s->update($id, $_POST['perf_score'] ?? null);
            header('Location: index.php?url=Manager/listSuppliers');
            exit;
        }

        $s        = new Supplier();
        $supplier = $s->getById($id);
        $this->view("manager/suppliers/edit", ['supplier' => $supplier]);
    }
    public function add_user()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $role     = $_POST['role'] ?? '';
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone    = trim($_POST['phone'] ?? '');

        if ($role === 'staff') {
            require_once __DIR__ . "/../models/FloorStaff.php";
            $u = new FloorStaff($name, $email, $password);
            $u->create(
                $_POST['shift_start'] ?? '08:00:00',
                $_POST['shift_end']   ?? '16:00:00',
                0
            );
        } elseif ($role === 'supplier') {
            require_once __DIR__ . "/../models/Supplier.php";
            $s = new Supplier($name, $email, $password);
            $s->create(100);
        }

        header('Location: index.php?url=Manager/systemAdmin');
        exit;
    }

    $this->view("manager/add-user");
    }
    
    /* ======================
        CLIENT MANAGEMENT (Updated for Your Model)
    ====================== */

    public function clients()
    {
        // استدعاء ملف الموديل بتاعك
        require_once __DIR__ . "/../models/client.php"; 
        $clientModel = new Client();
        
        // استخدام فانكشن getAll اللي في الموديل بتاعك
        $clients = $clientModel->getAll();
        
        $this->view("manager/clients", ['clients' => $clients]);
    }

    public function addClient()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . "/../models/client.php";
            $clientModel = new Client();

            // استلام البيانات من الفورم (تأكد أن أسماء الـ inputs في الـ HTML مطابقة)
            $name = trim($_POST['name'] ?? '');
            $address = trim($_POST['shipping_address'] ?? '');
            $type = trim($_POST['client_type'] ?? 'Regular'); // قيمة افتراضية مثلاً

            if (!empty($name) && !empty($address)) {
                // استخدام فانكشن create اللي في الموديل بتاعك
                $clientModel->create($name, $address, $type);
                header('Location: index.php?url=Manager/clients');
                exit;
            } else {
                $clients = $clientModel->getAll();
                $this->view("manager/clients", [
                    'clients' => $clients,
                    'error'   => "Name and Shipping Address are required!"
                ]);
            }
        }
    }

    public function deleteClient($id)
    {
        require_once __DIR__ . "/../models/client.php";
        $clientModel = new Client();
        
        // استخدام فانكشن delete اللي في الموديل بتاعك
        $clientModel->delete($id);
        header('Location: index.php?url=Manager/clients');
        exit;
    }

    // فانكشن إضافية لو حبيت تعرض تاريخ أوردرات عميل معين
    public function clientHistory($id)
    {
        require_once __DIR__ . "/../models/client.php";
        $clientModel = new Client();
        
        $client = $clientModel->getById($id);
        $orders = $clientModel->getOrders($id); // الفانكشن دي موجودة في موديلك وجاهزة
        
        $this->view("manager/client-history", [
            'client' => $client,
            'orders' => $orders
        ]);
    }


    

}

