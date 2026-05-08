<?php

class StaffController extends Controller
{
    public function __construct()
    {

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
   public function validateWeight()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $actualWeight = floatval($_POST['actual_weight']);
        $expectedWeight = 1.2;

        // فرق مسموح بسيط
        if (abs($actualWeight - $expectedWeight) <= 0.2) {

            header("Location: " . BASE_URL . "index.php?url=Staff/packing&status=validated");
            exit;

        } else {

            header("Location: " . BASE_URL . "index.php?url=Staff/packing&status=invalid");
            exit;
        }
    }
}
public function QC1()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // هنا هتحط اللوجيك بتاع QC
        // مثال بسيط:

        $po_id = $_POST['po_id'] ?? null;

        if (!$po_id) {
            header("Location: " . BASE_URL . "index.php?url=Staff/qc&status=error");
            exit;
        }

        // بعد الحفظ أو المعالجة
        header("Location: " . BASE_URL . "index.php?url=Staff/qc&status=success");
        exit;
    }

    header("Location: " . BASE_URL . "index.php?url=Staff/qc");
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
