<?php
session_start();
require_once 'app/helpers/SessionHelper.php';

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// ===== ĐỊNH TUYẾN API =====
if (strtolower($url[0]) === 'api' && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    $apiFile = 'app/controllers/' . $apiControllerName . '.php';

    if (file_exists($apiFile)) {
        require_once $apiFile;
        $controller = new $apiControllerName();
        $method = $_SERVER['REQUEST_METHOD'];

        // Xử lý OPTIONS (CORS preflight)
        if ($method === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(204);
            exit;
        }

        // Nếu có action cụ thể trong URL (url[2]), ưu tiên dùng nó
        // Ví dụ: /api/user/login  => action = 'login'
        //        /api/user/register => action = 'register'
        //        /api/user/me       => action = 'me'
        //        /api/product/123   => action = 'show', id = 123
        $segment = $url[2] ?? null;
        $id      = $url[3] ?? null;

        if ($segment !== null && !is_numeric($segment)) {
            // url[2] là tên action (không phải số) → gọi thẳng
            $action = $segment;
            $id     = $url[3] ?? null; // url[3] có thể là id nếu cần
        } else {
            // url[2] là số (hoặc không có) → route theo HTTP method
            $id = $segment; // segment là id nếu có
            switch ($method) {
                case 'GET':    $action = $id ? 'show'    : 'index'; break;
                case 'POST':   $action = 'store'; break;
                case 'PUT':    $action = $id ? 'update'  : 'index'; break;
                case 'DELETE': $action = $id ? 'destroy' : 'index'; break;
                default:
                    http_response_code(405);
                    echo json_encode(['message' => 'Method Not Allowed']);
                    exit;
            }
        }

        if (method_exists($controller, $action)) {
            $id
                ? call_user_func_array([$controller, $action], [$id])
                : call_user_func_array([$controller, $action], []);
        } else {
            http_response_code(404);
            echo json_encode(['message' => "Action '$action' not found in $apiControllerName"]);
        }
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'API Controller not found: ' . $apiControllerName]);
        exit;
    }
}

// ===== ĐỊNH TUYẾN THƯỜNG =====
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

$controllerFile = 'app/controllers/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
} else {
    die('Controller not found: ' . $controllerName);
}

if (method_exists($controller, $action)) {
    call_user_func_array([$controller, $action], array_slice($url, 2));
} else {
    die('Action not found: ' . $action);
}
?>
