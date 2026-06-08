<?php

class SessionHelper
{
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
        $_SESSION['user_id']     = $user->id;
        $_SESSION['user_name']   = $user->name;
        $_SESSION['user_email']  = $user->email;
        $_SESSION['user_role']   = $user->role;
        $_SESSION['user_avatar'] = $user->avatar ?? null;
        $_SESSION['user_phone']  = $user->phone  ?? null;
    }

    public static function logout()
    {
        self::start();
        unset(
            $_SESSION['user_id'],
            $_SESSION['user_name'],
            $_SESSION['user_email'],
            $_SESSION['user_role'],
            $_SESSION['user_avatar'],
            $_SESSION['user_phone']
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

    public static function getUserEmail(): string
    {
        self::start();
        return $_SESSION['user_email'] ?? '';
    }

    public static function getUserAvatar(): ?string
    {
        self::start();
        return $_SESSION['user_avatar'] ?? null;
    }

    // ===========================
    // PHÂN QUYỀN / BẢO VỆ ROUTE
    // ===========================
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /project1/User/login');
            exit();
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            include 'app/views/errors/403.php';
            exit();
        }
    }

    public static function redirectIfLoggedIn(): void
    {
        if (self::isLoggedIn()) {
            header('Location: /project1/Product');
            exit();
        }
    }
}
