<?php

class SessionHelper
{
    // Khởi động session (gọi 1 lần duy nhất)
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ===========================
    // ĐĂNG NHẬP / ĐĂNG XUẤT
    // ===========================

    public static function login($user)
    {
        self::start();
        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email']= $user->email;
        $_SESSION['user_role'] = $user->role;  // 'admin' hoặc 'user'
    }

    public static function logout()
    {
        self::start();
        // Giữ lại giỏ hàng nếu muốn, xoá thông tin đăng nhập
        unset(
            $_SESSION['user_id'],
            $_SESSION['user_name'],
            $_SESSION['user_email'],
            $_SESSION['user_role']
        );
    }

    // ===========================
    // KIỂM TRA TRẠNG THÁI
    // ===========================

    public static function isLoggedIn(): bool
    {
        self::start();
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        self::start();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public static function isUser(): bool
    {
        self::start();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user';
    }

    public static function getRole(): string
    {
        self::start();
        return $_SESSION['user_role'] ?? 'guest';
    }

    public static function getUserName(): string
    {
        self::start();
        return $_SESSION['user_name'] ?? '';
    }

    public static function getUserId(): ?int
    {
        self::start();
        return $_SESSION['user_id'] ?? null;
    }

    // ===========================
    // PHÂN QUYỀN / BẢO VỆ ROUTE
    // ===========================

    /**
     * Yêu cầu phải đăng nhập, nếu chưa → chuyển hướng trang login
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /project1/User/login');
            exit();
        }
    }

    /**
     * Yêu cầu phải là Admin, nếu không → báo lỗi 403
     */
    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            include 'app/views/errors/403.php';
            exit();
        }
    }

    /**
     * Nếu đã đăng nhập rồi thì không cho vào trang login/register
     */
    public static function redirectIfLoggedIn(): void
    {
        if (self::isLoggedIn()) {
            header('Location: /project1/Product');
            exit();
        }
    }
}
