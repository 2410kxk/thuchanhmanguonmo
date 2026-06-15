<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/helpers/JwtHelper.php');

class CartApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function jsonHeader()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    private function getCart(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    private function saveCart(array $cart): void
    {
        $_SESSION['cart'] = $cart;
    }

    private function calcTotal(array $cart): float
    {
        return array_reduce($cart, fn($sum, $item) => $sum + $item['price'] * $item['quantity'], 0);
    }

    // GET /api/cart — xem giỏ hàng
    public function index()
    {
        $this->jsonHeader();
        JwtHelper::requireAuth();
        $cart = $this->getCart();
        echo json_encode([
            'items' => array_values($cart),
            'total' => $this->calcTotal($cart),
            'count' => count($cart),
        ]);
    }

    // POST /api/cart — thêm sản phẩm
    public function store()
    {
        $this->jsonHeader();
        JwtHelper::requireAuth();
        $data       = json_decode(file_get_contents('php://input'), true) ?? [];
        $product_id = intval($data['product_id'] ?? 0);
        $quantity   = intval($data['quantity']   ?? 1);

        if ($product_id <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'product_id không hợp lệ']);
            return;
        }
        if ($quantity <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Số lượng phải lớn hơn 0']);
            return;
        }

        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['message' => 'Sản phẩm không tồn tại']);
            return;
        }

        $cart = $this->getCart();
        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] += $quantity;
        } else {
            $cart[$product_id] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => (float)$product->price,
                'image'      => $product->image,
                'quantity'   => $quantity,
            ];
        }
        $this->saveCart($cart);
        http_response_code(201);
        echo json_encode([
            'message' => 'Đã thêm vào giỏ hàng',
            'cart'    => ['items' => array_values($cart), 'total' => $this->calcTotal($cart)],
        ]);
    }

    // PUT /api/cart/{id} — cập nhật số lượng
    public function update($product_id)
    {
        $this->jsonHeader();
        JwtHelper::requireAuth();
        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $quantity = intval($data['quantity'] ?? 0);

        if ($quantity <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Số lượng phải lớn hơn 0']);
            return;
        }

        $cart = $this->getCart();
        if (!isset($cart[$product_id])) {
            http_response_code(404);
            echo json_encode(['message' => 'Sản phẩm không có trong giỏ hàng']);
            return;
        }
        $cart[$product_id]['quantity'] = $quantity;
        $this->saveCart($cart);
        echo json_encode([
            'message' => 'Đã cập nhật số lượng',
            'cart'    => ['items' => array_values($cart), 'total' => $this->calcTotal($cart)],
        ]);
    }

    // DELETE /api/cart/{id} — xóa 1 sản phẩm
    public function destroy($product_id)
    {
        $this->jsonHeader();
        JwtHelper::requireAuth();
        $cart = $this->getCart();
        if (!isset($cart[$product_id])) {
            http_response_code(404);
            echo json_encode(['message' => 'Sản phẩm không có trong giỏ hàng']);
            return;
        }
        unset($cart[$product_id]);
        $this->saveCart($cart);
        echo json_encode([
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'cart'    => ['items' => array_values($cart), 'total' => $this->calcTotal($cart)],
        ]);
    }

    // DELETE /api/cart — xóa toàn bộ
    public function clear()
    {
        $this->jsonHeader();
        JwtHelper::requireAuth();
        $this->saveCart([]);
        echo json_encode(['message' => 'Đã xóa toàn bộ giỏ hàng']);
    }

    // GET /api/cart/total — tổng tiền
    public function total()
    {
        $this->jsonHeader();
        JwtHelper::requireAuth();
        $cart = $this->getCart();
        echo json_encode(['total' => $this->calcTotal($cart), 'count' => count($cart)]);
    }
}
