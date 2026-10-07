<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../../config/database.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        global $pdo;

        $this->userModel = new UserModel($pdo);
    }

    private function checkPermission()
    {
        $role = $_SESSION['user']['role_name'] ?? '';

        if ($role !== 'Owner') {
            die("Access Denied.");
        }
    }

    public function handleRequest()
    {
        $this->checkPermission();

        $action = $_GET['action'] ?? 'index';

        switch ($action) {

            case 'index':

                $users = $this->userModel->getAll();

                include 'app/views/users/index.php';

                break;


            case 'create':

                $roles = $this->userModel->getRoles();

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    $full_name = trim($_POST['full_name'] ?? '');
                    $username = trim($_POST['username'] ?? '');
                    $password = $_POST['password'] ?? '';
                    $email = trim($_POST['email'] ?? '');
                    $role_id = intval($_POST['role_id'] ?? 0);

                    if (
                        $full_name === '' ||
                        $username === '' ||
                        $password === '' ||
                        $role_id <= 0
                    ) {
                        $_SESSION['user_error'] =
                            "Full name, username, password, and role are required.";

                        header("Location: users.php?action=create");
                        exit;
                    }

                    if ($this->userModel->usernameExists($username)) {

                        $_SESSION['user_error'] =
                            "Username already exists.";

                        header("Location: users.php?action=create");
                        exit;
                    }

                    $this->userModel->create(
                        $full_name,
                        $username,
                        $password,
                        $email,
                        $role_id
                    );

                    $_SESSION['user_success'] =
                        "User added successfully.";

                    header("Location: users.php");
                    exit;
                }

                include 'app/views/users/create.php';

                break;


            case 'edit':

                $id = intval($_GET['id'] ?? 0);

                $user = $this->userModel->getById($id);

                if (!$user) {
                    die("User not found.");
                }

                $roles = $this->userModel->getRoles();

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    $full_name = trim($_POST['full_name'] ?? '');
                    $username = trim($_POST['username'] ?? '');
                    $email = trim($_POST['email'] ?? '');
                    $role_id = intval($_POST['role_id'] ?? 0);

                    if (
                        $full_name === '' ||
                        $username === '' ||
                        $role_id <= 0
                    ) {
                        $_SESSION['user_error'] =
                            "Full name, username, and role are required.";

                        header("Location: users.php?action=edit&id=" . $id);
                        exit;
                    }

                    if (
                        $this->userModel->usernameExists(
                            $username,
                            $id
                        )
                    ) {
                        $_SESSION['user_error'] =
                            "Username already exists.";

                        header("Location: users.php?action=edit&id=" . $id);
                        exit;
                    }

                    $this->userModel->update(
                        $id,
                        $full_name,
                        $username,
                        $email,
                        $role_id
                    );

                    $_SESSION['user_success'] =
                        "User updated successfully.";

                    header("Location: users.php");
                    exit;
                }

                include 'app/views/users/edit.php';

                break;


            case 'toggle_status':

                $id = intval($_GET['id'] ?? 0);

                $user = $this->userModel->getById($id);

                if (!$user) {
                    die("User not found.");
                }

                // Prevent Owner from deactivating their own account
                $currentUserId =
                    $_SESSION['user']['user_id'] ?? 0;

                if ($id == $currentUserId) {

                    $_SESSION['user_error'] =
                        "You cannot deactivate your own account.";

                    header("Location: users.php");
                    exit;
                }

                $newStatus =
                    ($user['status'] === 'Active')
                    ? 'Inactive'
                    : 'Active';

                $this->userModel->updateStatus(
                    $id,
                    $newStatus
                );

                $_SESSION['user_success'] =
                    "User status updated successfully.";

                header("Location: users.php");
                exit;


            default:

                header("Location: users.php");
                exit;
        }
    }
}