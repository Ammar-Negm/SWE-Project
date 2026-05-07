<?php
// require_once "../app/helpers/Validator.php";
// require_once "../app/models/User.php";

// class UserController extends Controller
// {
//     public $userModel;
//     public function __construct()
//     {
//         $this->userModel = new User();
//     }

//     // READ ALL
//     public function index()
//     {
//         $users = $this->userModel->getAllUsers();
//         $this->view("users/index", ['users' => $users]);
//     }

//     // SHOW ONE USER
//     public function show($id)
//     {
//         $user = $this->userModel->getUserById($id);
//         $this->view("users/show", ['user' => $user]);
//     }

//     // SHOW CREATE FORM
//     public function create()
//     {
//         $this->view("users/create");
//     }

//     // STORE NEW USER
//     public function store()
//     {
//         $validator = new Validator();

//         $name  = $_POST['name'];
//         $age   = $_POST['age'];
//         $email = $_POST['email'];
//         $password = $_POST['password'];
//         $userType = "Student";


//         // Validation rules
//         $validator->required('name', $name);
//         $validator->required('age', $age);
//         $validator->email('email', $email);
//         $validator->minLength('password', $password, 8);

//         if ($validator->passes()) {
//             // Save to DB
//             $this->userModel->createUser($name, $age, $email, $password, $userType);
//             header("Location: " . BASE_URL . "User/index");
//         } else {
//             // Return errors to view
//             $this->view("users/create", [
//                 'errors' => $validator->getErrors(),
//                 'old'    => $_POST
//             ]);
//         }
//     }

//     // EDIT FORM
//     public function edit($id)
//     {
//         $user = $this->userModel->getUserById($id);
//         $this->view("users/edit", ['user' => $user]);
//     }

//     // UPDATE USER
//     public function update($id)
//     {
//         $name  = $_POST['name'];
//         $age   = $_POST['age'];
//         $email = $_POST['email'];
//         $password = $_POST['password'];
//         $userType = "Student";

//         $this->userModel->updateUser($id, $name, $age, $email, $password, $userType);

//         header("Location: " . BASE_URL . "User/index");
//     }

//     // DELETE USER
//     public function delete($id)
//     {
//         $this->userModel->deleteUser($id);

//         header("Location: " . BASE_URL . "User/index");
//     }
// } 

require_once "../app/helpers/Validator.php";
require_once "../app/models/User.php";

class UserController extends Controller
{
    public $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // READ ALL
    public function index()
    {
        $users = $this->userModel->getAllUsers();
        $this->view("users/index", ['users' => $users]);
    }

    // SHOW CREATE FORM
    public function create()
    {
        $this->view("users/create");
    }

    // STORE NEW USER (FIXED)
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view("errors/invalid_request");
            return;
        }

        try {
            $validator = new Validator();

            $name     = trim($_POST['full_name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $phone    = trim($_POST['phone'] ?? '');
            $role     = $_POST['role'] ?? 'staff';

            // validation
            $validator->required('full_name', $name);
            $validator->email('email', $email);
            $validator->minLength('password', $password, 6);

            if (!$validator->passes()) {
                $this->view("users/create", [
                    'errors' => $validator->getErrors(),
                    'old'    => $_POST
                ]);
                return;
            }

            // insert safely
            $this->userModel->createUser([
                'name'     => $name,
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'phone'    => $phone,
                'role'     => $role
            ]);

            header("Location: " . BASE_URL . "User/index");
            exit;

        } catch (PDOException $e) {
            // بدل ما يوقع fatal error
            $this->view("errors/invalid_request", [
                'message' => "User already exists or DB error"
            ]);
        }
    }

    // EDIT FORM (SAFE)
    public function edit($id)
    {
        if (!$id) {
            $this->view("errors/invalid_request");
            return;
        }

        $user = $this->userModel->getUserById($id);

        if (!$user) {
            $this->view("errors/invalid_request");
            return;
        }

        $this->view("users/edit", ['user' => $user]);
    }

    // UPDATE
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view("errors/invalid_request");
            return;
        }

        try {
            $this->userModel->updateUser(
                $id,
                $_POST['full_name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['role']
            );

            header("Location: " . BASE_URL . "User/index");
            exit;

        } catch (Exception $e) {
            $this->view("errors/invalid_request", [
                'message' => "Update failed"
            ]);
        }
    }

    // DELETE
    public function delete($id)
    {
        try {
            $this->userModel->deleteUser($id);
            header("Location: " . BASE_URL . "User/index");
            exit;
        } catch (Exception $e) {
            $this->view("errors/invalid_request");
        }
    }
}