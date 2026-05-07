<?php

class SupplierController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'supplier') {
            header('Location: index.php?url=Auth/login');
            exit;
        }
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
        $orders  = $poModel->getBySupplier($_SESSION['user_id']);
        $this->view("supplier/purchase-orders", ['orders' => $orders]);
    }

    public function orders()
    {
        $this->listOrders();
    }

    public function updateOrder($po_id)
    {
        require_once __DIR__ . "/../models/PurchaseOrder.php";
        $poModel = new PurchaseOrder();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status  = $_POST['status'] ?? '';
            $allowed = ['pending', 'shipped', 'delivered', 'cancelled'];

            if (in_array($status, $allowed)) {
                $poModel->updateStatus($po_id, $status);
            }
        }

        header('Location: index.php?url=Supplier/listOrders');
        exit;
    }

    /* ======================
        SUPPLIERS CRUD
        (بيتحكم فيها المانجر بس
        بس الـ methods موجودة هنا
        عشان SupplierController هو اللي فيه الـ Supplier model)
    ====================== */

    // public function addSupplier()
    // {
    //     require_once __DIR__ . "/../models/Supplier.php";

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $s = new Supplier(
    //             trim($_POST['name']     ?? ''),
    //             trim($_POST['email']    ?? ''),
    //             trim($_POST['password'] ?? '')
    //         );
    //         $s->create($_POST['perf_score'] ?? 0);
    //         header('Location: index.php?url=Manager/listSuppliers');
    //         exit;
    //     }

    //     $this->view("manager/suppliers/create");
    // }

    // public function editSupplier($id)
    // {
    //     require_once __DIR__ . "/../models/Supplier.php";

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $s = new Supplier(
    //             trim($_POST['name']     ?? ''),
    //             trim($_POST['email']    ?? ''),
    //             trim($_POST['password'] ?? '')
    //         );
    //         $s->update($id, $_POST['perf_score'] ?? null);
    //         header('Location: index.php?url=Manager/listSuppliers');
    //         exit;
    //     }

    //     $s        = new Supplier();
    //     $supplier = $s->getById($id);
    //     $this->view("manager/suppliers/edit", ['supplier' => $supplier]);
    // }
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