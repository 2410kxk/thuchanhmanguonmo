<?php
require_once('app/config/database.php');
require_once('app/models/UserModel.php');
require_once('app/helpers/JwtHelper.php');

class UserApiController
{
    private $userModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->userModel = new UserModel($this->db);
    }

    private function jsonHeader()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    // =========================
    // POST /api/user/register
    // Đăng ký tài khoản mới
    // =========================
    public function register()
    {
        $this->jsonHeader();
        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $name     = trim($data['name']     ?? '');
        $email    = trim($data['email']    ?? '');
        $password = $data['password']      ?? '';

        $result = $this->userModel->register($name, $email, $password);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            http_response_code(201);
            echo json_encode(['message' => 'Đăng ký thành công']);
        }
    }

    // =========================
    // POST /api/user/login
    // Đăng nhập — trả về JWT token
    // =========================
    public function login()
    {
        $this->jsonHeader();
        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $email    = trim($data['email']    ?? '');
        $password = $data['password']      ?? '';

        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['message' => 'Email và mật khẩu không được để trống']);
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user->password)) {
            http_response_code(401);
            echo json_encode(['message' => 'Email hoặc mật khẩu không đúng']);
            return;
        }
        if (!$user->is_active) {
            http_response_code(403);
            echo json_encode(['message' => 'Tài khoản đã bị khóa']);
            return;
        }

        $token = JwtHelper::createToken($user);
        echo json_encode([
            'message' => 'Đăng nhập thành công',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                'avatar'=> $user->avatar,
            ],
        ]);
    }

    // =========================
    // POST /api/user/logout
    // Đăng xuất (phía client xóa token)
    // =========================
    public function logout()
    {
        $this->jsonHeader();
        // Với JWT stateless, logout thực chất là client tự xóa token.
        // Endpoint này xác nhận token hợp lệ rồi trả về thông báo thành công.
        $payload = JwtHelper::requireAuth();
        echo json_encode([
            'message' => 'Đăng xuất thành công. Vui lòng xóa token ở phía client.',
            'user_id' => $payload->sub,
        ]);
    }

    // =========================
    // GET /api/user/me
    // Xem thông tin bản thân
    // =========================
    public function me()
    {
        $this->jsonHeader();
        $payload = JwtHelper::requireAuth();
        $user = $this->userModel->findById($payload->sub);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy người dùng']);
            return;
        }
        echo json_encode([
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'phone'   => $user->phone,
            'address' => $user->address,
            'avatar'  => $user->avatar,
            'role'    => $user->role,
        ]);
    }

    // =========================
    // PUT /api/user/profile
    // Cập nhật hồ sơ cá nhân
    // =========================
    public function profile()
    {
        $this->jsonHeader();
        $payload = JwtHelper::requireAuth();
        $data    = json_decode(file_get_contents('php://input'), true) ?? [];
        $name    = trim($data['name']    ?? '');
        $phone   = trim($data['phone']   ?? '');
        $address = trim($data['address'] ?? '');

        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['message' => 'Tên không được để trống']);
            return;
        }

        $result = $this->userModel->updateProfile($payload->sub, $name, $phone, $address);
        if ($result) {
            echo json_encode(['message' => 'Cập nhật hồ sơ thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Cập nhật thất bại']);
        }
    }

    // =========================
    // PUT /api/user/password
    // Đổi mật khẩu
    // =========================
    public function password()
    {
        $this->jsonHeader();
        $payload     = JwtHelper::requireAuth();
        $data        = json_decode(file_get_contents('php://input'), true) ?? [];
        $oldPassword = $data['old_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword)) {
            http_response_code(400);
            echo json_encode(['message' => 'Vui lòng nhập đầy đủ mật khẩu cũ và mới']);
            return;
        }
        if (strlen($newPassword) < 6) {
            http_response_code(400);
            echo json_encode(['message' => 'Mật khẩu mới phải có ít nhất 6 ký tự']);
            return;
        }

        $user = $this->userModel->findById($payload->sub);
        if (!password_verify($oldPassword, $user->password)) {
            http_response_code(400);
            echo json_encode(['message' => 'Mật khẩu cũ không đúng']);
            return;
        }

        $result = $this->userModel->changePassword($payload->sub, $newPassword);
        if ($result) {
            echo json_encode(['message' => 'Đổi mật khẩu thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Đổi mật khẩu thất bại']);
        }
    }

    // =========================
    // GET /api/user  (Admin)
    // Lấy danh sách tất cả user
    // =========================
    public function index()
    {
        $this->jsonHeader();
        JwtHelper::requireAdmin();
        $users = $this->userModel->getAllUsers();
        $result = array_map(function($u) {
            return [
                'id'        => $u->id,
                'name'      => $u->name,
                'email'     => $u->email,
                'role'      => $u->role,
                'is_active' => $u->is_active,
                'created_at'=> $u->created_at,
            ];
        }, $users);
        echo json_encode(array_values($result));
    }
}
