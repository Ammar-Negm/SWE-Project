<?php
require_once __DIR__ . "/../models/AdminModel.php";
require_once __DIR__ . "/../models/ProductModel.php";

class ManagerController extends Controller
{
    private $adminModel;
    private $productModel;

    
        public function __construct()
        {
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
        $this->view("manager/dashboard");
    }

   public function inventory()
{
    $products = $this->productModel->getAll();
    $this->view("manager/inventory", ['products' => $products]);
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
        $this->view("manager/supplier-list");
    }

    public function zonalOptimizer()
    {
        $this->view("manager/zonal-optimizer");
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

    public function addProduct()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $this->productModel->insert([
                'sku'      => $_POST['sku']      ?? '',
                'name'     => $_POST['name']     ?? '',
                'price'    => $_POST['price']    ?? 0,
                'category' => $_POST['category'] ?? '',
                'minStock' => $_POST['minStock'] ?? 0,
            ]);

            header('Location: index.php?url=Manager/inventory'); // ✅ غيّر
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $products = $this->productModel->getAll(); // ✅ أضف
                $this->view("manager/inventory", [
                    'products' => $products,
                    'error'    => "❌ SKU already exists"
                ]);
                return;
            }
            throw $e;
        }
    }

    $this->inventory(); // ✅ بدل view مباشرة
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
            $s = new Supplier(
                trim($_POST['name']     ?? ''),
                trim($_POST['email']    ?? ''),
                trim($_POST['password'] ?? '')
            );
            $s->create($_POST['perf_score'] ?? 0);
            header('Location: index.php?url=Manager/listSuppliers');
            exit;
        }

        $this->view("manager/suppliers/create");
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
    
}