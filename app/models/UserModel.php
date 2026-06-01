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
    // ĐĂNG KÝ USER MỚI
    // ===========================
    public function register(string $name, string $email, string $password, string $role = 'user')
    {
        $errors = [];

        // Validate tên
        if (empty(trim($name))) {
            $errors[] = 'Họ tên không được để trống.';
        } elseif (mb_strlen(trim($name)) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự.';
        }

        // Validate email
        if (empty(trim($email))) {
            $errors[] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không đúng định dạng.';
        } elseif ($this->findByEmail($email)) {
            $errors[] = 'Email này đã được đăng ký.';
        }

        // Validate mật khẩu
        if (empty($password)) {
            $errors[] = 'Mật khẩu không được để trống.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        // Hash mật khẩu
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

        return $user;
    }
}
