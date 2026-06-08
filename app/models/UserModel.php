<?php

class UserModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // ===========================
    // TÌM USER THEO EMAIL
    // ===========================
    public function findByEmail(string $email)
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ===========================
    // TÌM USER THEO ID
    // ===========================
    public function findById(int $id)
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ===========================
    // TÌM USER THEO RESET TOKEN
    // ===========================
    public function findByResetToken(string $token)
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE reset_token = :token AND reset_expires > NOW() LIMIT 1'
        );
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ===========================
    // LẤY TẤT CẢ USERS (ADMIN)
    // ===========================
    public function getAllUsers(string $search = '', string $role = '', string $status = '')
    {
        $sql = 'SELECT * FROM users WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (name LIKE :search OR email LIKE :search2)';
            $params[':search']  = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        if ($role !== '') {
            $sql .= ' AND role = :role';
            $params[':role'] = $role;
        }
        if ($status !== '') {
            $sql .= ' AND is_active = :status';
            $params[':status'] = (int)$status;
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // ===========================
    // ĐĂNG KÝ USER MỚI
    // ===========================
    public function register(string $name, string $email, string $password, string $role = 'user')
    {
        $errors = [];

        if (empty(trim($name))) {
            $errors[] = 'Họ tên không được để trống.';
        } elseif (mb_strlen(trim($name)) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự.';
        }

        if (empty(trim($email))) {
            $errors[] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không đúng định dạng.';
        } elseif ($this->findByEmail($email)) {
            $errors[] = 'Email này đã được đăng ký.';
        }

        if (empty($password)) {
            $errors[] = 'Mật khẩu không được để trống.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role)
             VALUES (:name, :email, :password, :role)'
        );

        $stmt->execute([
            ':name'     => trim($name),
            ':email'    => strtolower(trim($email)),
            ':password' => $hashedPassword,
            ':role'     => $role,
        ]);

        return $this->findById((int) $this->db->lastInsertId());
    }

    // ===========================
    // ĐĂNG NHẬP
    // ===========================
    public function login(string $email, string $password)
    {
        $errors = [];

        if (empty(trim($email))) {
            $errors[] = 'Email không được để trống.';
        }
        if (empty($password)) {
            $errors[] = 'Mật khẩu không được để trống.';
        }
        if (!empty($errors)) {
            return $errors;
        }

        $user = $this->findByEmail(strtolower(trim($email)));

        if (!$user || !password_verify($password, $user->password)) {
            return ['Email hoặc mật khẩu không chính xác.'];
        }

        if (isset($user->is_active) && $user->is_active == 0) {
            return ['Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.'];
        }

        return $user;
    }

    // ===========================
    // ĐỔI MẬT KHẨU
    // ===========================
    public function changePassword(int $userId, string $currentPassword, string $newPassword, string $confirmPassword)
    {
        $errors = [];
        $user = $this->findById($userId);

        if (!$user) {
            return ['Không tìm thấy tài khoản.'];
        }

        if (!password_verify($currentPassword, $user->password)) {
            $errors[] = 'Mật khẩu hiện tại không đúng.';
        }
        if (empty($newPassword) || strlen($newPassword) < 6) {
            $errors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Xác nhận mật khẩu không khớp.';
        }

        if (!empty($errors)) return $errors;

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('UPDATE users SET password = :pw WHERE id = :id');
        $stmt->execute([':pw' => $hashed, ':id' => $userId]);
        return true;
    }

    // ===========================
    // QUÊN MẬT KHẨU - TẠO TOKEN
    // ===========================
    public function createResetToken(string $email)
    {
        $user = $this->findByEmail(strtolower(trim($email)));
        if (!$user) {
            return false;
        }

        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare(
            'UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id'
        );
        $stmt->execute([':token' => $token, ':expires' => $expires, ':id' => $user->id]);

        return ['token' => $token, 'user' => $user];
    }

    // ===========================
    // ĐẶT LẠI MẬT KHẨU
    // ===========================
    public function resetPassword(string $token, string $newPassword, string $confirmPassword)
    {
        $errors = [];

        if (empty($newPassword) || strlen($newPassword) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Xác nhận mật khẩu không khớp.';
        }
        if (!empty($errors)) return $errors;

        $user = $this->findByResetToken($token);
        if (!$user) {
            return ['Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'];
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            'UPDATE users SET password = :pw, reset_token = NULL, reset_expires = NULL WHERE id = :id'
        );
        $stmt->execute([':pw' => $hashed, ':id' => $user->id]);
        return true;
    }

    // ===========================
    // CẬP NHẬT HỒ SƠ
    // ===========================
    public function updateProfile(int $userId, string $name, string $phone, string $address)
    {
        $errors = [];

        if (empty(trim($name))) {
            $errors[] = 'Họ tên không được để trống.';
        } elseif (mb_strlen(trim($name)) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự.';
        }
        if (!empty($phone) && !preg_match('/^[0-9\+\-\s]{8,15}$/', $phone)) {
            $errors[] = 'Số điện thoại không hợp lệ.';
        }

        if (!empty($errors)) return $errors;

        $stmt = $this->db->prepare(
            'UPDATE users SET name = :name, phone = :phone, address = :address WHERE id = :id'
        );
        $stmt->execute([
            ':name'    => trim($name),
            ':phone'   => trim($phone),
            ':address' => trim($address),
            ':id'      => $userId,
        ]);

        return $this->findById($userId);
    }

    // ===========================
    // CẬP NHẬT ẢNH ĐẠI DIỆN
    // ===========================
    public function updateAvatar(int $userId, array $file)
    {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            return ['Chỉ chấp nhận file ảnh JPG, PNG, GIF, WEBP.'];
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['Kích thước ảnh tối đa là 2MB.'];
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $uploadDir = 'uploads/avatars/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Xóa avatar cũ
        $old = $this->findById($userId);
        if ($old && !empty($old->avatar) && file_exists($old->avatar)) {
            unlink($old->avatar);
        }

        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);

        $stmt = $this->db->prepare('UPDATE users SET avatar = :avatar WHERE id = :id');
        $stmt->execute([':avatar' => $uploadDir . $filename, ':id' => $userId]);

        return $uploadDir . $filename;
    }

    // ===========================
    // ADMIN: KHÓA / MỞ KHÓA TÀI KHOẢN
    // ===========================
    public function toggleActive(int $userId)
    {
        $user = $this->findById($userId);
        if (!$user) return false;

        $newStatus = $user->is_active ? 0 : 1;
        $stmt = $this->db->prepare('UPDATE users SET is_active = :status WHERE id = :id');
        $stmt->execute([':status' => $newStatus, ':id' => $userId]);
        return $newStatus;
    }

    // ===========================
    // ADMIN: ĐỔI ROLE
    // ===========================
    public function updateRole(int $userId, string $role)
    {
        if (!in_array($role, ['admin', 'user'])) return false;
        $stmt = $this->db->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->execute([':role' => $role, ':id' => $userId]);
        return true;
    }

    // ===========================
    // ADMIN: XÓA USER
    // ===========================
    public function deleteUser(int $userId)
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([':id' => $userId]);
        return true;
    }
}
