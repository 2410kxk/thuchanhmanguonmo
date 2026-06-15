<?php
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class JwtHelper
{
    private static $secret = 'COS340_2410KXK_SECRET_KEY_DO_NOT_SHARE';
    private static $algo   = 'HS256';
    private static $ttl    = 3600; // 1 giờ

    // Tạo token từ user object
    public static function createToken($user): string
    {
        $now = time();
        $payload = [
            'iss'  => 'my_store',
            'iat'  => $now,
            'exp'  => $now + self::$ttl,
            'sub'  => $user->id,
            'name' => $user->name,
            'email'=> $user->email,
            'role' => $user->role,
        ];
        return JWT::encode($payload, self::$secret, self::$algo);
    }

    // Giải mã token — trả về payload hoặc null
    public static function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::$secret, self::$algo));
        } catch (ExpiredException $e) {
            return null;
        } catch (SignatureInvalidException $e) {
            return null;
        } catch (BeforeValidException $e) {
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    // Lấy token từ header Authorization: Bearer <token>
    public static function getBearerToken(): ?string
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = $_SERVER['Authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $reqHeaders = apache_request_headers();
            if (isset($reqHeaders['Authorization'])) {
                $headers = $reqHeaders['Authorization'];
            }
        }
        if ($headers && preg_match('/Bearer\s+(.+)/i', $headers, $m)) {
            return $m[1];
        }
        return null;
    }

    // Xác thực token và trả về payload; nếu lỗi thì echo JSON và exit
    public static function requireAuth(): object
    {
        $token = self::getBearerToken();
        if (!$token) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized: Không có token']);
            exit;
        }
        $payload = self::decodeToken($token);
        if (!$payload) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized: Token không hợp lệ hoặc đã hết hạn']);
            exit;
        }
        return $payload;
    }

    // Yêu cầu role admin
    public static function requireAdmin(): object
    {
        $payload = self::requireAuth();
        if ($payload->role !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden: Chỉ Admin mới có quyền thực hiện thao tác này']);
            exit;
        }
        return $payload;
    }

    // Lấy payload nếu có token (không bắt buộc)
    public static function optionalAuth(): ?object
    {
        $token = self::getBearerToken();
        if (!$token) return null;
        return self::decodeToken($token);
    }
}
