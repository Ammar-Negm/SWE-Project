<?php

// class StaffController extends Controller
// {
//     public function dashboard()
//     {
//         session_start();
//         $this->view("staff/dashboard");
//     }
// }
class StaffController extends Controller
{
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
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
        $this->view("staff/dashboard");
    }

    public function inventory()
    {
        $this->view("staff/inventory");
    }

    public function orders()
    {
        $this->view("staff/orders");
    }
}