

<?php

class SupplierController extends Controller
{
    private $supplierModel;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'supplier') {
            header('Location: index.php?url=Auth/login');
            exit;
        }
        require_once __DIR__ . "/../models/Supplier.php";
        $this->supplierModel = new Supplier();
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $this->view("supplier/dashboard");
    }

    public function invoice()
    {
        $this->view("supplier/invoice-manager");
    }

    /* ======================
        ORDERS (Purchase Orders)
    ====================== */

    public function listOrders()
    {
        require_once __DIR__ . "/../models/PurchaseOrder.php";
        $poModel = new PurchaseOrder();
        // جيب الأوردرات الخاصة بالسبلاير اللي لوج إن
        $orders = $poModel->getBySupplier($_SESSION['user_id']);
        $this->view("supplier/purchase-orders", ['orders' => $orders]);
    }

    public function orders()
    {
        $this->listOrders();
    }

    public function createOrder()
    {
        // السبلاير مش بيعمل PO، ده دور المدير
        // لكن السبلاير يقدر يأكد الشحنة
        header('Location: index.php?url=Supplier/listOrders');
        exit;
    }

    public function updateOrder($po_id)
    {
        require_once __DIR__ . "/../models/PurchaseOrder.php";
        $poModel = new PurchaseOrder();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // السبلاير بيقدر يغير status الشحنة لـ shipped مثلاً
            $status = $_POST['status'] ?? '';

            $allowed = ['pending', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($status, $allowed)) {
                header('Location: index.php?url=Supplier/listOrders');
                exit;
            }

            $poModel->updateStatus($po_id, $status);
        }

        header('Location: index.php?url=Supplier/listOrders');
        exit;
    }

    /* ======================
        SUPPLIERS LIST (للمانجر بس)
    ====================== */

    public function listSuppliers()
    {
        // دي بتيجي من المانجر مش السبلاير
        // بس لو احتاجها هنا:
        $this->view("supplier/dashboard");
    }

    public function addSupplier()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . "/../models/Supplier.php";
            $s = new Supplier(
                $_POST['name']     ?? '',
                $_POST['email']    ?? '',
                $_POST['password'] ?? ''
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
                $_POST['name']     ?? '',
                $_POST['email']    ?? '',
                $_POST['password'] ?? ''
            );
            $s->update($id, $_POST['perf_score'] ?? null);
            header('Location: index.php?url=Manager/listSuppliers');
            exit;
        }

        $s       = new Supplier();
        $supplier = $s->getById($id);
        $this->view("manager/suppliers/edit", ['supplier' => $supplier]);
    }
}



// class SupplierController extends Controller
// {
//     public function dashboard()
//     {
//         $this->view("supplier/dashboard");
//     }
//     public function invoice()
// {
//     $this->view("supplier/invoice-manager");
// }

// public function orders()
// {
//     $this->view("supplier/purchase-orders");
// }
// } 