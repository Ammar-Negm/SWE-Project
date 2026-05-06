<?php

class StaffController extends Controller
{
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'staff') {
            header('Location: index.php?url=Auth/login');
            exit;
        }
    }

    public function index()    { $this->dashboard(); }
    public function dashboard(){ $this->view("staff/dashboard"); }
    public function packing()  { $this->view("staff/packing-station"); }
    public function picking()  { $this->view("staff/pick-list"); }
    public function qc()       { $this->view("staff/qc-inspection"); }

    /* ======================
        TASKS
    ====================== */

    public function listTasks()
    {
        require_once __DIR__ . "/../models/PickList.php";
        $pickListModel = new PickList();
        $lists = $pickListModel->getTasksByStaff($_SESSION['user_id']);
        $this->view("staff/pick-list", ['lists' => $lists]);
    }

    public function updateTask($task_id)
    {
        require_once __DIR__ . "/../models/PickTask.php";
        require_once __DIR__ . "/../models/InventoryItem.php";
        $taskModel = new PickTask();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'complete') {
                $taskModel->completeTask($task_id);
            } elseif ($action === 'fail') {
                $taskModel->updateStatus($task_id, 'Failed');
            }
        }

        header('Location: index.php?url=Staff/listTasks');
        exit;
    }
}

// class StaffController extends Controller
// {
//     public function dashboard()
//     {
//         session_start();
//         $this->view("staff/dashboard");
//     }
// }

// class StaffController extends Controller
// {
//     public function __construct()
//     {
//         session_start();
//         // if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff')
//         if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'staff')
//              {
//             header('Location: index.php?url=Auth/login');
//             exit;
//         }
//     }
//     public function index()
//     {
//         $this->dashboard();
//     }

//     public function dashboard()
//     {
//         $this->view("staff/dashboard");
//     }

//     public function packing()
//     {
//         $this->view("staff/packing-station");
//     }

//     public function picking()
//     {
//         $this->view("staff/pick-list");
//     }
//     public function qc()
//     {
//         $this->view("staff/qc-inspection");
//     }
// }
