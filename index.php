<?php
session_start();
require_once 'app/helpers/SessionHelper.php';

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// ===== ĐỊNH TUYẾN API =====
if (strtolower($url[0]) === 'api' && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    $apiFile = 'app/controllers/' . $apiControllerName . '.php';

    if (file_exists($apiFile)) {
        require_once $apiFile;
        $controller = new $apiControllerName();
        $method = $_SERVER['REQUEST_METHOD'];
        $id = $url[2] ?? null;

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

        if (method_exists($controller, $action)) {
            $id ? call_user_func_array([$controller, $action], [$id])
                : call_user_func_array([$controller, $action], []);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Action not found']);
        }
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'API Controller not found']);
        exit;
    }
}

// ===== ĐỊNH TUYẾN THƯỜNG =====
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
