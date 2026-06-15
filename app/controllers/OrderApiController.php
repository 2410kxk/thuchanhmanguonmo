<?php
require_once('app/config/database.php');
require_once('app/helpers/JwtHelper.php');

class OrderApiController
{
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->db = (new Database())->getConnection();
    }

    private function jsonHeader()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    private function getCart(): array { return $_SESSION['cart'] ?? []; }
    private function clearCart(): void { $_SESSION['cart'] = []; }

    // GET /api/order — danh sách đơn hàng
    public function index()
    {
        $this->jsonHeader();
        $payload = JwtHelper::requireAuth();

        if ($payload->role === 'admin') {
            $stmt = $this->db->query(
                "SELECT o.*, u.name as user_name FROM orders o
                 LEFT JOIN users u ON o.user_id = u.id
                 ORDER BY o.created_at DESC"
            );
        } else {
            $stmt = $this->db->prepare(
                "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC"
            );
            $stmt->execute([':uid' => $payload->sub]);
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_OBJ));
    }

    // GET /api/order/{id} — chi tiết đơn hàng
    public function show($id)
    {
        $this->jsonHeader();
        $payload = JwtHelper::requireAuth();

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy đơn hàng']);
            return;
        }
        // User chỉ xem đơn của mình
        if ($payload->role !== 'admin' && $order->user_id != $payload->sub) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden: Không có quyền xem đơn hàng này']);
            return;
        }

        $stmt2 = $this->db->prepare(
            "SELECT od.*, p.name as product_name, p.image
             FROM order_details od
             LEFT JOIN product p ON od.product_id = p.id
             WHERE od.order_id = :oid"
        );
        $stmt2->execute([':oid' => $id]);
        $details = $stmt2->fetchAll(PDO::FETCH_OBJ);

        echo json_encode(['order' => $order, 'details' => $details]);
    }

    // POST /api/order — tạo đơn hàng từ giỏ hàng
    public function store()
    {
        $this->jsonHeader();
        $payload = JwtHelper::requireAuth();

        $cart = $this->getCart();
        if (empty($cart)) {
            http_response_code(400);
            echo json_encode(['message' => 'Giỏ hàng trống, không thể đặt hàng']);
            return;
        }

        $data    = json_decode(file_get_contents('php://input'), true) ?? [];
        $name    = trim($data['name']    ?? '');
        $phone   = trim($data['phone']   ?? '');
        $address = trim($data['address'] ?? '');

        if (empty($name) || empty($phone) || empty($address)) {
            http_response_code(400);
            echo json_encode(['message' => 'Vui lòng nhập đầy đủ tên, số điện thoại và địa chỉ']);
            return;
        }

        $total = array_reduce($cart, fn($s, $i) => $s + $i['price'] * $i['quantity'], 0);

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "INSERT INTO orders (user_id, name, phone, address, total, status, payment_status)
                 VALUES (:uid, :name, :phone, :address, :total, 'pending', 'unpaid')"
            );
            $stmt->execute([
                ':uid'     => $payload->sub,
                ':name'    => $name,
                ':phone'   => $phone,
                ':address' => $address,
                ':total'   => $total,
            ]);
            $orderId = $this->db->lastInsertId();

            $stmt2 = $this->db->prepare(
                "INSERT INTO order_details (order_id, product_id, quantity, price)
                 VALUES (:oid, :pid, :qty, :price)"
            );
            foreach ($cart as $item) {
                $stmt2->execute([
                    ':oid'   => $orderId,
                    ':pid'   => $item['product_id'],
                    ':qty'   => $item['quantity'],
                    ':price' => $item['price'],
                ]);
            }

            $this->db->commit();
            $this->clearCart();

            http_response_code(201);
            echo json_encode(['message' => 'Đặt hàng thành công', 'order_id' => $orderId]);
        } catch (Exception $e) {
            $this->db->rollBack();
            http_response_code(500);
            echo json_encode(['message' => 'Đặt hàng thất bại: ' . $e->getMessage()]);
        }
    }

    // PUT /api/order/{id} — hủy đơn (user) hoặc cập nhật trạng thái (admin)
    public function update($id)
    {
        $this->jsonHeader();
        $payload = JwtHelper::requireAuth();

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy đơn hàng']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if ($payload->role === 'admin') {
            $status = $data['status'] ?? $order->status;
            $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($status, $allowed)) {
                http_response_code(400);
                echo json_encode(['message' => 'Trạng thái không hợp lệ']);
                return;
            }
            $this->db->prepare("UPDATE orders SET status = :s WHERE id = :id")
                     ->execute([':s' => $status, ':id' => $id]);
            echo json_encode(['message' => 'Cập nhật trạng thái thành công']);
        } else {
            // User chỉ được hủy đơn của mình khi đang pending
            if ($order->user_id != $payload->sub) {
                http_response_code(403);
                echo json_encode(['message' => 'Forbidden']);
                return;
            }
            if ($order->status !== 'pending') {
                http_response_code(400);
                echo json_encode(['message' => 'Chỉ hủy được đơn hàng đang chờ xử lý']);
                return;
            }
            $this->db->prepare("UPDATE orders SET status = 'cancelled' WHERE id = :id")
                     ->execute([':id' => $id]);
            echo json_encode(['message' => 'Đã hủy đơn hàng']);
        }
    }

    // DELETE /api/order/{id} — xóa đơn (admin only)
    public function destroy($id)
    {
        $this->jsonHeader();
        JwtHelper::requireAdmin();
        $this->db->prepare("DELETE FROM order_details WHERE order_id = :id")->execute([':id' => $id]);
        $this->db->prepare("DELETE FROM orders WHERE id = :id")->execute([':id' => $id]);
        echo json_encode(['message' => 'Đã xóa đơn hàng']);
    }
}
