
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

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $this->view("staff/dashboard");
    }

    public function packing()
    {
        $this->view("staff/packing-station");
    }

    public function picking()
    {
        $this->view("staff/pick-list");
    }

    public function qc()
    {
        $this->view("staff/qc-inspection");
    }

    /* ======================
        TASKS (PickTasks)
    ====================== */

    public function listTasks()
    {
        require_once __DIR__ . "/../models/PickList.php";
        $pickListModel = new PickList();

        // جيب الـ pick lists المعينة للموظف اللي لوج إن
        $staff_id = $_SESSION['user_id'];
        $lists    = $pickListModel->getTasksByStaff($staff_id);

        $this->view("staff/pick-list", ['lists' => $lists]);
    }

    public function createTask()
    {
        // الموظف مش بيعمل task بنفسه، المدير بيعملها وبتتعين ليه
        // لو حاب تضيف حاجة هنا بعدين تعملها
        header('Location: index.php?url=Staff/listTasks');
        exit;
    }

    public function updateTask($task_id)
    {
        require_once __DIR__ . "/../models/PickTask.php";
        $taskModel = new PickTask();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'complete') {
                // بيخصم الكمية من المخزون ويغير الحالة لـ Picked
                $taskModel->completeTask($task_id);

            } elseif ($action === 'fail') {
                $reason = $_POST['reason'] ?? 'Item not found';
                // بيغير الحالة لـ Failed ويسجل السبب
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
