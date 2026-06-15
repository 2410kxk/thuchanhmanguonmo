<?php
require_once('app/config/database.php');
require_once('app/helpers/JwtHelper.php');

class PaymentApiController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    private function jsonHeader()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    // POST /api/payment — tạo thanh toán cho đơn hàng
    // Body: { "order_id": 1, "method": "cod|bank_transfer|e_wallet" }
    public function store()
    {
        $this->jsonHeader();
        $payload  = JwtHelper::requireAuth();
        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $order_id = intval($data['order_id'] ?? 0);
        $method   = $data['method'] ?? 'cod';

        $allowed_methods = ['cod', 'bank_transfer', 'e_wallet'];
        if (!in_array($method, $allowed_methods)) {
            http_response_code(400);
            echo json_encode(['message' => 'Phương thức thanh toán không hợp lệ (cod, bank_transfer, e_wallet)']);
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([':id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy đơn hàng']);
            return;
        }
        // User chỉ thanh toán đơn của mình
        if ($payload->role !== 'admin' && $order->user_id != $payload->sub) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden']);
            return;
        }
        if ($order->payment_status === 'paid') {
            http_response_code(400);
            echo json_encode(['message' => 'Đơn hàng này đã được thanh toán']);
            return;
        }
        if ($order->status === 'cancelled') {
            http_response_code(400);
            echo json_encode(['message' => 'Không thể thanh toán đơn hàng đã hủy']);
            return;
        }

        // Mô phỏng thanh toán thành công
        $note = match($method) {
            'cod'           => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng — mô phỏng thành công',
            'e_wallet'      => 'Ví điện tử — mô phỏng thành công',
        };

        $this->db->prepare(
            "UPDATE orders SET payment_status = 'paid', payment_method = :method WHERE id = :id"
        )->execute([':method' => $method, ':id' => $order_id]);

        echo json_encode([
            'message'        => 'Thanh toán thành công',
            'order_id'       => $order_id,
            'method'         => $method,
            'note'           => $note,
            'amount'         => $order->total,
            'payment_status' => 'paid',
        ]);
    }

    // GET /api/payment/{id} — xem trạng thái thanh toán của đơn hàng
    public function show($order_id)
    {
        $this->jsonHeader();
        $payload = JwtHelper::requireAuth();

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([':id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy đơn hàng']);
            return;
        }
        if ($payload->role !== 'admin' && $order->user_id != $payload->sub) {
            http_response_code(403);
            echo json_encode(['message' => 'Forbidden']);
            return;
        }

        echo json_encode([
            'order_id'       => $order->id,
            'total'          => $order->total,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method ?? null,
            'status'         => $order->status,
        ]);
    }
}
