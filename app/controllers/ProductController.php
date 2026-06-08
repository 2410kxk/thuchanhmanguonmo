<?php

require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/helpers/SessionHelper.php');

class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        SessionHelper::start();

        $this->db = (new Database())->getConnection();

        $this->productModel = new ProductModel($this->db);
    }

    // =========================
    // TRANG CHỦ / DANH SÁCH  (tất cả đều xem được)
    // =========================
    public function index()
    {
        $products = $this->productModel->getProducts();
        include 'app/views/product/list.php';
    }

    public function category($id)
    {
        $products = $this->productModel->getProductsByCategory($id);
        include 'app/views/product/list.php';
    }

    public function list()
    {
        $products = $this->productModel->getProducts();
        include 'app/views/product/list.php';
    }

    // =========================
    // CHI TIẾT SẢN PHẨM  (tất cả đều xem được)
    // =========================
    public function show($id)
    {
        $product = $this->productModel->getProductById($id);

        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    // =========================
    // THÊM SẢN PHẨM  ← CHỈ ADMIN
    // =========================
    public function add()
    {
        SessionHelper::requireAdmin();

        $categories = (new CategoryModel($this->db))->getCategories();
        include_once 'app/views/product/add.php';
    }

    public function save()
    {
        SessionHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $name        = $_POST['name']        ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price']       ?? '';
            $category_id = $_POST['category_id'] ?? null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
            } else {
                $image = "";
            }

            $result = $this->productModel->addProduct(
                $name, $description, $price, $category_id, $image
            );

            if (is_array($result)) {
                $errors     = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                header('Location: /project1/Product');
                exit();
            }
        }
    }

    // =========================
    // SỬA SẢN PHẨM  ← CHỈ ADMIN
    // =========================
    public function edit($id)
    {
        SessionHelper::requireAdmin();

        $product    = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();

        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function update()
    {
        SessionHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id          = $_POST['id'];
            $name        = $_POST['name'];
            $description = $_POST['description'];
            $price       = $_POST['price'];
            $category_id = $_POST['category_id'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
            } else {
                $image = $_POST['existing_image'] ?? '';
            }

            $edit = $this->productModel->updateProduct(
                $id, $name, $description, $price, $category_id, $image
            );

            if ($edit) {
                header('Location: /project1/Product');
                exit();
            } else {
                echo "Đã xảy ra lỗi khi lưu sản phẩm.";
            }
        }
    }

    // =========================
    // XÓA SẢN PHẨM  ← CHỈ ADMIN
    // =========================
    public function delete($id)
    {
        SessionHelper::requireAdmin();

        if ($this->productModel->deleteProduct($id)) {
            header('Location: /project1/Product');
            exit();
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
    }

    // =========================
    // UPLOAD ẢNH
    // =========================
    private function uploadImage($file)
    {
        $target_dir = "uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file   = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);

        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }

        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }

        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Chỉ cho phép JPG, JPEG, PNG và GIF.");
        }

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải ảnh lên.");
        }

        return $target_file;
    }

    // =========================
    // GIỎ HÀNG  ← Phải đăng nhập
    // =========================
    public function addToCart($id)
    {
        SessionHelper::requireLogin();

        $product = $this->productModel->getProductById($id);

        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $qty = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
            $_SESSION['cart'][$id]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1,
                'image'    => $product->image
            ];
        }

        $totalQuantity = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalQuantity += $item['quantity'];
        }

        echo json_encode([
            'success'       => true,
            'quantity'      => $_SESSION['cart'][$id]['quantity'],
            'totalQuantity' => $totalQuantity
        ]);
        exit();
    }

    // =========================
    // TRANG GIỎ HÀNG  ← Phải đăng nhập
    // =========================
    public function cart()
    {
        SessionHelper::requireLogin();

        $cart = $_SESSION['cart'] ?? [];
        include 'app/views/product/cart.php';
    }

    // =========================
    // CHECKOUT  ← Phải đăng nhập
    // =========================
    public function checkout()
    {
        SessionHelper::requireLogin();

        $cart = $_SESSION['cart'] ?? [];
        include 'app/views/product/checkout.php';
    }

    public function orders()
    {
        SessionHelper::requireLogin();

        $userId = SessionHelper::getUserId();
        $orders = $_SESSION['orders'][$userId] ?? [];
        include 'app/views/product/orders.php';
    }

    public function placeOrder()
    {
        SessionHelper::requireLogin();

        $userId = SessionHelper::getUserId();

        if (!isset($_SESSION['orders'][$userId])) {
            $_SESSION['orders'][$userId] = [];
        }

        $cart = $_SESSION['cart'] ?? [];

        do {
            $orderCode = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (isset($_SESSION['orders'][$userId][$orderCode]));

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $_SESSION['orders'][$userId][$orderCode] = [
            'code'       => $orderCode,
            'items'      => $cart,
            'total'      => $total,
            'status'     => 'Đang giao hàng',
            'created_at' => date('d/m/Y H:i:s')
        ];

        unset($_SESSION['cart']);

        echo json_encode([
            'success'   => true,
            'orderCode' => $orderCode
        ]);
        exit();
    }

    // =========================
    // UPDATE CART
    // =========================
    public function updateCart()
    {
        SessionHelper::requireLogin();

        $id     = $_GET['id']     ?? 0;
        $action = $_GET['action'] ?? '';

        if (isset($_SESSION['cart'][$id])) {
            if ($action == 'plus') {
                $_SESSION['cart'][$id]['quantity']++;
            }
            if ($action == 'minus') {
                $_SESSION['cart'][$id]['quantity']--;
                if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$id]);
                }
            }
        }

        $totalQuantity = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalQuantity += $item['quantity'];
        }

        echo json_encode(['success' => true, 'totalQuantity' => $totalQuantity]);
        exit();
    }

    // =========================
    // REMOVE CART
    // =========================
    public function removeCart()
    {
        SessionHelper::requireLogin();

        $id = $_GET['id'] ?? 0;

        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        $totalQuantity = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalQuantity += $item['quantity'];
        }

        echo json_encode(['success' => true, 'totalQuantity' => $totalQuantity]);
        exit();
    }

    // =========================
    // DELETE ORDER
    // =========================
    public function deleteOrder()
    {
        SessionHelper::requireLogin();

        $userId = SessionHelper::getUserId();
        $code   = $_GET['code'] ?? '';

        if (isset($_SESSION['orders'][$userId][$code])) {
            unset($_SESSION['orders'][$userId][$code]);
        }

        header('Location: /project1/Product/orders');
        exit();
    }
}
?>
