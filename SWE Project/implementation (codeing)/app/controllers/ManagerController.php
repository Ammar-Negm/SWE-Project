<?php
require_once __DIR__ . "/../models/AdminModel.php";
require_once __DIR__ . "/../models/ProductModel.php";

class ManagerController extends Controller
{
    private $adminModel;
    private $productModel;

    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
            header('Location: index.php?url=Auth/login');
            exit;
        }
        $this->adminModel   = new AdminModel();
        $this->productModel = new ProductModel();
    }

    public function dashboard()
    {
        $this->view("manager/dashboard");
    }

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
        header('Location: index.php?url=Manager/dashboard&alert=emergency');
        exit;
    }

    public function listProducts()
    {
        $products = $this->productModel->getAll();
        $this->view("manager/products/index", ['products' => $products]);
    }

    public function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->insert([
                'sku'      => $_POST['sku']      ?? '',
                'name'     => $_POST['name']     ?? '',
                'price'    => $_POST['price']    ?? 0,
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
                'sku'      => $_POST['sku']      ?? '',
                'name'     => $_POST['name']     ?? '',
                'price'    => $_POST['price']    ?? 0,
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
}