<?php

require_once 'app/config/database.php';
require_once 'app/models/UserModel.php';
require_once 'app/helpers/SessionHelper.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        SessionHelper::start();
        $db = (new Database())->getConnection();
        $this->userModel = new UserModel($db);
    }

    // ===========================
    // TRANG ĐĂNG NHẬP
    // ===========================
    public function login()
    {
        SessionHelper::redirectIfLoggedIn();
        include 'app/views/auth/login.php';
    }

    public function loginPost()
    {
        SessionHelper::redirectIfLoggedIn();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /project1/User/login');
            exit();
        }

        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->userModel->login($email, $password);

        if (is_array($result)) {
            // Trả về lỗi
            $errors = $result;
            include 'app/views/auth/login.php';
            return;
        }

        // Đăng nhập thành công
        SessionHelper::login($result);
        header('Location: /project1/Product');
        exit();
    }

    // ===========================
    // TRANG ĐĂNG KÝ
    // ===========================
    public function register()
    {
        SessionHelper::redirectIfLoggedIn();
        include 'app/views/auth/register.php';
    }

    public function registerPost()
    {
        SessionHelper::redirectIfLoggedIn();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /project1/User/register');
            exit();
        }

        $name     = $_POST['name']     ?? '';
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->userModel->register($name, $email, $password);

        if (is_array($result)) {
            $errors = $result;
            include 'app/views/auth/register.php';
            return;
        }

        // Đăng ký xong → tự động đăng nhập
        SessionHelper::login($result);
        header('Location: /project1/Product');
        exit();
    }

    // ===========================
    // ĐĂNG XUẤT
    // ===========================
    public function logout()
    {
        SessionHelper::logout();
        header('Location: /project1/User/login');
        exit();
    }
}
