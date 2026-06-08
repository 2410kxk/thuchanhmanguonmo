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
            $errors = $result;
            include 'app/views/auth/login.php';
            return;
        }

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

    // ===========================
    // HỒ SƠ CÁ NHÂN
    // ===========================
    public function profile()
    {
        SessionHelper::requireLogin();
        $userId = SessionHelper::getUserId();
        $user   = $this->userModel->findById($userId);
        $success = $_SESSION['flash_success'] ?? null;
        $errors  = $_SESSION['flash_errors']  ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_errors']);
        include 'app/views/user/profile.php';
    }

    public function profilePost()
    {
        SessionHelper::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /project1/User/profile');
            exit();
        }

        $userId  = SessionHelper::getUserId();
        $name    = $_POST['name']    ?? '';
        $phone   = $_POST['phone']   ?? '';
        $address = $_POST['address'] ?? '';

        $result = $this->userModel->updateProfile($userId, $name, $phone, $address);

        if (is_array($result)) {
            $_SESSION['flash_errors'] = $result;
        } else {
            // Cập nhật session
            $_SESSION['user_name'] = $result->name;
            $_SESSION['flash_success'] = 'Cập nhật hồ sơ thành công!';
        }
        header('Location: /project1/User/profile');
        exit();
    }

    // ===========================
    // TẢI LÊN ẢNH ĐẠI DIỆN
    // ===========================
    public function avatarPost()
    {
        SessionHelper::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['avatar'])) {
            header('Location: /project1/User/profile');
            exit();
        }

        $userId = SessionHelper::getUserId();
        $result = $this->userModel->updateAvatar($userId, $_FILES['avatar']);

        if (is_array($result)) {
            $_SESSION['flash_errors'] = $result;
        } else {
            $_SESSION['user_avatar']   = $result;
            $_SESSION['flash_success'] = 'Cập nhật ảnh đại diện thành công!';
        }
        header('Location: /project1/User/profile');
        exit();
    }

    // ===========================
    // ĐỔI MẬT KHẨU
    // ===========================
    public function changePassword()
    {
        SessionHelper::requireLogin();
        $success = $_SESSION['flash_success'] ?? null;
        $errors  = $_SESSION['flash_errors']  ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_errors']);
        include 'app/views/user/change_password.php';
    }

    public function changePasswordPost()
    {
        SessionHelper::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /project1/User/changePassword');
            exit();
        }

        $userId          = SessionHelper::getUserId();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password']     ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $result = $this->userModel->changePassword($userId, $currentPassword, $newPassword, $confirmPassword);

        if (is_array($result)) {
            $_SESSION['flash_errors'] = $result;
        } else {
            $_SESSION['flash_success'] = 'Đổi mật khẩu thành công!';
        }
        header('Location: /project1/User/changePassword');
        exit();
    }

    // ===========================
    // QUÊN MẬT KHẨU
    // ===========================
    public function forgotPassword()
    {
        SessionHelper::redirectIfLoggedIn();
        $success = $_SESSION['flash_success'] ?? null;
        $errors  = $_SESSION['flash_errors']  ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_errors']);
        include 'app/views/auth/forgot_password.php';
    }

    public function forgotPasswordPost()
    {
        SessionHelper::redirectIfLoggedIn();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /project1/User/forgotPassword');
            exit();
        }

        $email  = $_POST['email'] ?? '';
        $result = $this->userModel->createResetToken($email);

        // Không tiết lộ email có tồn tại hay không
        $_SESSION['flash_success'] = 'Nếu email tồn tại, link đặt lại mật khẩu đã được gửi. '
            . 'Trong môi trường demo, hãy dùng link bên dưới.';

        if ($result) {
            // Trong thực tế: gửi email. Ở đây lưu token vào session để demo
            $_SESSION['demo_reset_token'] = $result['token'];
        }

        header('Location: /project1/User/forgotPassword');
        exit();
    }

    // ===========================
    // ĐẶT LẠI MẬT KHẨU
    // ===========================
    public function resetPassword($token = '')
    {
        SessionHelper::redirectIfLoggedIn();

        if (empty($token)) {
            header('Location: /project1/User/forgotPassword');
            exit();
        }

        $success = $_SESSION['flash_success'] ?? null;
        $errors  = $_SESSION['flash_errors']  ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_errors']);
        include 'app/views/auth/reset_password.php';
    }

    public function resetPasswordPost()
    {
        SessionHelper::redirectIfLoggedIn();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /project1/User/forgotPassword');
            exit();
        }

        $token           = $_POST['token']            ?? '';
        $newPassword     = $_POST['new_password']     ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $result = $this->userModel->resetPassword($token, $newPassword, $confirmPassword);

        if (is_array($result)) {
            $_SESSION['flash_errors'] = $result;
            header('Location: /project1/User/resetPassword/' . urlencode($token));
        } else {
            unset($_SESSION['demo_reset_token']);
            $_SESSION['flash_success'] = 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.';
            header('Location: /project1/User/login');
        }
        exit();
    }
}
