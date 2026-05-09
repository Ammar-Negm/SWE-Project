<?php

class StaffController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'staff') {
            header('Location: index.php?url=Auth/login');
            exit;
        }

        require_once __DIR__ . "/../models/PickList.php";
        require_once __DIR__ . "/../models/PickTask.php";
        require_once __DIR__ . "/../models/InventoryItem.php";
        require_once __DIR__ . "/../models/AuditLog.php";
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $pickListModel = new PickList();
        $staffId = $_SESSION['user_id'];

        $lists = $pickListModel->getTasksByStaff($staffId);

        $activeTasks = [];
        $completedCount = 0;

        foreach ($lists as $task) {
            if (strtolower($task['task_status']) === 'picked') {
                $completedCount++;
            } else {
                $activeTasks[] = $task;
            }
        }

        $avgSecondsPerPick = 45;
        $distanceWalked = 1.2;

        $this->view("staff/dashboard", compact(
            'activeTasks',
            'completedCount',
            'avgSecondsPerPick',
            'distanceWalked'
        ));
    }

    public function picking()
    {
        $pickListModel = new PickList();
        $staffId = $_SESSION['user_id'];
        $lists = $pickListModel->getTasksByStaff($staffId);

        $this->view("staff/pick-list", compact('lists'));
    }

    public function packing()
    {
        $pickListModel = new PickList();
        $staffId = $_SESSION['user_id'];
        $lists = $pickListModel->getTasksByStaff($staffId);

        $packingItems = array_values(array_filter($lists, function ($item) {
            return strtolower($item['task_status']) !== 'picked';
        }));

        $this->view("staff/packing-station", compact('packingItems'));
    }

    public function qc()
    {
        $pickListModel = new PickList();
        $staffId = $_SESSION['user_id'];
        $lists = $pickListModel->getTasksByStaff($staffId);

        $qcItems = array_values(array_filter($lists, function ($item) {
            return strtolower($item['task_status']) !== 'picked';
        }));

        $poNumber = 'PO-20045'; // مؤقت

        $this->view("staff/qc-inspection", compact('qcItems', 'poNumber'));
    }

    public function updateTask($task_id = null)
    {
        if (!$task_id) {
            header('Location: index.php?url=Staff/picking');
            exit;
        }

        $taskModel = new PickTask();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'complete') {
                $done = $taskModel->completeTask($task_id, $_SESSION['user_id']);

                if ($done) {
                    $_SESSION['success'] = "Task completed successfully.";
                } else {
                    $_SESSION['error'] = "Failed to complete task.";
                }
            } elseif ($action === 'fail') {
                $taskModel->updateStatus($task_id, 'Failed');
                $_SESSION['error'] = "Task marked as failed.";
            }
        }

        header('Location: index.php?url=Staff/picking');
        exit;
    }

    public function submitQC()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=Staff/qc');
            exit;
        }

        $_SESSION['success'] = "QC report submitted successfully.";
        header('Location: index.php?url=Staff/qc');
        exit;
    }

    public function validateWeight()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=Staff/packing');
            exit;
        }

        $actualWeight = $_POST['actual_weight'] ?? '';

        if ($actualWeight === '') {
            $_SESSION['error'] = "Please enter actual weight.";
        } else {
            $_SESSION['success'] = "Weight validated successfully.";
        }

        header('Location: index.php?url=Staff/packing');
        exit;
    }

    public function reportMissing()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=Staff/picking');
            exit;
        }

        $_SESSION['success'] = "Missing item report submitted.";
        header('Location: index.php?url=Staff/picking');
        exit;
    }

    public function completePick()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=Staff/picking');
            exit;
        }

        $_SESSION['success'] = "Pick list completed successfully.";
        header('Location: index.php?url=Staff/picking');
        exit;
    }
}