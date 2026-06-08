<?php

require_once 'app/config/database.php';
require_once 'app/models/UserModel.php';
require_once 'app/helpers/SessionHelper.php';

class AdminController
{
    private $userModel;

    public function __construct()
    {
        SessionHelper::start();
        SessionHelper::requireAdmin();
        $db = (new Database())->getConnection();
        $this->userModel = new UserModel($db);
    }

    // ===========================
    // TRANG QUẢN LÝ NGƯỜI DÙNG
    // ===========================
    public function users()
    {
        $search = $_GET['search'] ?? '';
        $role   = $_GET['role']   ?? '';
        $status = $_GET['status'] ?? '';
        $users  = $this->userModel->getAllUsers($search, $role, $status);

        $success = $_SESSION['flash_success'] ?? null;
        $errors  = $_SESSION['flash_errors']  ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_errors']);

        include 'app/views/admin/users.php';
    }

    // ===========================
    // KHÓA / MỞ KHÓA TÀI KHOẢN
    // ===========================
    public function toggleActive($userId = 0)
    {
        $userId = (int) $userId;

        // Không cho khóa chính mình
        if ($userId === SessionHelper::getUserId()) {
            $_SESSION['flash_errors'] = ['Bạn không thể khóa tài khoản của chính mình.'];
            header('Location: /project1/Admin/users');
            exit();
        }

        $this->userModel->toggleActive($userId);
        $_SESSION['flash_success'] = 'Đã cập nhật trạng thái tài khoản.';
        header('Location: /project1/Admin/users');
        exit();
    }

    // ===========================
    // ĐỔI QUYỀN USER
    // ===========================
    public function updateRole($userId = 0)
    {
        $userId = (int) $userId;
        $role   = $_POST['role'] ?? '';

        if ($userId === SessionHelper::getUserId()) {
            $_SESSION['flash_errors'] = ['Bạn không thể tự thay đổi quyền của mình.'];
            header('Location: /project1/Admin/users');
            exit();
        }

        $this->userModel->updateRole($userId, $role);
        $_SESSION['flash_success'] = 'Đã cập nhật quyền người dùng.';
        header('Location: /project1/Admin/users');
        exit();
    }

    // ===========================
    // XÓA USER
    // ===========================
    public function deleteUser($userId = 0)
    {
        $userId = (int) $userId;

        if ($userId === SessionHelper::getUserId()) {
            $_SESSION['flash_errors'] = ['Bạn không thể xóa tài khoản của chính mình.'];
            header('Location: /project1/Admin/users');
            exit();
        }

        $this->userModel->deleteUser($userId);
        $_SESSION['flash_success'] = 'Đã xóa người dùng.';
        header('Location: /project1/Admin/users');
        exit();
    }
}
