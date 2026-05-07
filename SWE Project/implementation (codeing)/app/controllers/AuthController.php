<?php
require_once "../app/models/AuthModel.php";

class AuthController extends Controller
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    public function login()
    {
        // لو الفورم اتبعت (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email']    ?? '';
            $password = $_POST['password'] ?? '';
            $role     = $_POST['role']     ?? '';

            $user = $this->authModel->findUserByEmailAndRole($email, $role);

                if ($user && $password === $user['password']) {
                session_start();
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = $role;
                $this->redirectByRole($role);
            } else {
                // ارجع نفس الصفحة مع error
                $this->view("auth/login", ['error' => 'Invalid credentials.']);
            }

        } else {
            // عرض الصفحة فاضية
            $this->view("auth/login");
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . 'Auth/login');
        exit;
    }

    // public function redirectByRole($role)
    // {
    //     switch ($role) {
    //         case 'manager':
    //             header('Location: ' . BASE_URL . 'Manager/dashboard');
    //             break;
    //         case 'staff':
    //             header('Location: ' . BASE_URL . 'Staff/dashboard');
    //             break;
    //         case 'supplier':
    //             header('Location: ' . BASE_URL . 'Supplier/dashboard');
    //             break;
    //     }
    //     exit;
    // }
    public function redirectByRole($role)
{
    switch ($role) {
        case 'manager':
            header('Location: index.php?url=Manager/dashboard');
            break;
        case 'staff':
            header('Location: index.php?url=Staff/dashboard');
            break;
        case 'supplier':
            header('Location: index.php?url=Supplier/dashboard');
            break;
        case 'client':
            header('Location: index.php?url=Client/createOrder');
            break;
    }
    exit;
}
public function index()
{
    $this->login();
}
}