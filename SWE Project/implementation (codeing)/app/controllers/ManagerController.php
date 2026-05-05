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
        $this->view("manager/inventory");
    }

    public function procurement()
    {
        $this->view("manager/procurement");
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

            $this->productModel->insert([
                'sku'      => $_POST['sku'] ?? '',
                'name'     => $_POST['name'] ?? '',
                'price'    => $_POST['price'] ?? 0,
                'category' => $_POST['category'] ?? '',
                'minStock' => $_POST['minStock'] ?? 0,
            ]);

            header('Location: index.php?url=Manager/listProducts');
            exit;
        }

        $this->view("manager/products/create");
    }

    public function editProduct($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->productModel->update($id, [
                'sku'      => $_POST['sku'] ?? '',
                'name'     => $_POST['name'] ?? '',
                'price'    => $_POST['price'] ?? 0,
                'category' => $_POST['category'] ?? '',
                'minStock' => $_POST['minStock'] ?? 0,
            ]);

            header('Location: index.php?url=Manager/listProducts');
            exit;
        }

        $product = $this->productModel->getById($id);
        $this->view("manager/products/edit", ['product' => $product]);
    }

    public function deleteProduct($id)
    {
        $this->productModel->delete($id);
        header('Location: index.php?url=Manager/listProducts');
        exit;
    }
    /* ======================
        ZONES
    ====================== */

    public function listZones()
    {
        require_once __DIR__ . "/../models/ZoneModel.php";
        $zoneModel = new ZoneModel();
        $zones = $zoneModel->getAll();
        $this->view("manager/zones/index", ['zones' => $zones]);
    }

//     public function listZones()
// {
//     require_once __DIR__ . "/../models/ZoneModel.php";
//     $zoneModel = new ZoneModel();
//     $zones = $zoneModel->getAll();
    
//     // مؤقتاً بدل الـ view
//     echo "<pre>";
//     print_r($zones);
//     echo "</pre>";
//     die();
// }
// دا كان للتجريب بس ان الفانكشنز شغاله

    public function addZone()
    {
        require_once __DIR__ . "/../models/ZoneModel.php";
        $zoneModel = new ZoneModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'zone_name'    => trim($_POST['zone_name'] ?? ''),
                'max_capacity' => (int)($_POST['max_capacity'] ?? 0),
            ];

            // تحقق بسيط قبل الإدخال
            if (empty($data['zone_name']) || $data['max_capacity'] <= 0) {
                $this->view("manager/zones/create", [
                    'error' => 'Zone name and capacity are required.',
                    'old'   => $data,
                ]);
                return;
            }

            $zoneModel->insert($data);
            header('Location: index.php?url=Manager/listZones');
            exit;
        }

        $this->view("manager/zones/create");
    }

    public function editZone($id)
    {
        require_once __DIR__ . "/../models/ZoneModel.php";
        $zoneModel = new ZoneModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'zone_name'    => trim($_POST['zone_name'] ?? ''),
                'max_capacity' => (int)($_POST['max_capacity'] ?? 0),
            ];

            $zoneModel->update($id, $data);
            header('Location: index.php?url=Manager/listZones');
            exit;
        }

        $zone = $zoneModel->getById($id);
        $this->view("manager/zones/edit", ['zone' => $zone]);
    }

    public function deleteZone($id)
    {
        require_once __DIR__ . "/../models/ZoneModel.php";
        $zoneModel = new ZoneModel();
        $zoneModel->delete($id);
        header('Location: index.php?url=Manager/listZones');
        exit;
    }

    /* ======================
        BINS
    ====================== */

    public function listBins()
    {
        require_once __DIR__ . "/../models/BinModel.php";
        $binModel = new BinModel();
        $bins = $binModel->getAll();
        $this->view("manager/bins/index", ['bins' => $bins]);
    }

    public function addBin()
    {
        require_once __DIR__ . "/../models/BinModel.php";
        require_once __DIR__ . "/../models/ZoneModel.php";
        $binModel  = new BinModel();
        $zoneModel = new ZoneModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'zone_id'       => (int)($_POST['zone_id'] ?? 0),
                'maxWeight'     => (float)($_POST['maxWeight'] ?? 0),
                'shelfLocation' => trim($_POST['shelfLocation'] ?? ''),
            ];

            if (empty($data['shelfLocation']) || $data['maxWeight'] <= 0) {
                $zones = $zoneModel->getAll();
                $this->view("manager/bins/create", [
                    'error' => 'All fields are required.',
                    'zones' => $zones,
                    'old'   => $data,
                ]);
                return;
            }

            $binModel->insert($data);
            header('Location: index.php?url=Manager/listBins');
            exit;
        }

        $zones = $zoneModel->getAll();
        $this->view("manager/bins/create", ['zones' => $zones]);
    }

    public function editBin($id)
    {
        require_once __DIR__ . "/../models/BinModel.php";
        require_once __DIR__ . "/../models/ZoneModel.php";
        $binModel  = new BinModel();
        $zoneModel = new ZoneModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'zone_id'       => (int)($_POST['zone_id'] ?? 0),
                'maxWeight'     => (float)($_POST['maxWeight'] ?? 0),
                'shelfLocation' => trim($_POST['shelfLocation'] ?? ''),
            ];

            $binModel->update($id, $data);
            header('Location: index.php?url=Manager/listBins');
            exit;
        }

        $bin   = $binModel->getById($id);
        $zones = $zoneModel->getAll();
        $this->view("manager/bins/edit", ['bin' => $bin, 'zones' => $zones]);
    }

    public function deleteBin($id)
    {
        require_once __DIR__ . "/../models/BinModel.php";
        $binModel = new BinModel();
        $binModel->delete($id);
        header('Location: index.php?url=Manager/listBins');
        exit;
    }
}