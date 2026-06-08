<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function jsonHeader()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    public function index()
    {
        $this->jsonHeader();
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category_id'] ?? '';

        if ($search || $category_id) {
            $products = $this->productModel->searchProducts($search, $category_id);
        } else {
            $products = $this->productModel->getProducts();
        }

        if ($products === false || $products === null) {
            echo json_encode([]);
        } else {
            echo json_encode(array_values($products));
        }
    }

    public function show($id)
    {
        $this->jsonHeader();
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
        }
    }

    public function store()
    {
        $this->jsonHeader();

        $imagePath = null;

        // Hỗ trợ cả multipart/form-data (có file) và application/json
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'multipart/form-data') !== false) {
            // Form-data: đọc từ $_POST
            $name        = $_POST['name']        ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price']       ?? '';
            $category_id = $_POST['category_id'] ?? null;

            // Xử lý upload ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if (isset($uploadResult['error'])) {
                    http_response_code(400);
                    echo json_encode(['errors' => ['image' => $uploadResult['error']]]);
                    return;
                }
                $imagePath = $uploadResult['path'];
            }
        } else {
            // JSON body
            $data        = json_decode(file_get_contents("php://input"), true) ?? [];
            $name        = $data['name']        ?? '';
            $description = $data['description'] ?? '';
            $price       = $data['price']       ?? '';
            $category_id = $data['category_id'] ?? null;
        }

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $imagePath);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully']);
        }
    }

    public function update($id)
    {
        $this->jsonHeader();

        $imagePath = null;
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'multipart/form-data') !== false) {
            $name        = $_POST['name']        ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price']       ?? '';
            $category_id = $_POST['category_id'] ?? null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if (!isset($uploadResult['error'])) {
                    $imagePath = $uploadResult['path'];
                }
            }
        } else {
            $data        = json_decode(file_get_contents("php://input"), true) ?? [];
            $name        = $data['name']        ?? '';
            $description = $data['description'] ?? '';
            $price       = $data['price']       ?? '';
            $category_id = $data['category_id'] ?? null;
        }

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $imagePath);
        if ($result) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
        }
    }

    public function destroy($id)
    {
        $this->jsonHeader();
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }

    // ==============================
    // XỬ LÝ UPLOAD HÌNH ẢNH
    // ==============================
    private function handleImageUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)'];
        }

        if ($file['size'] > $maxSize) {
            return ['error' => 'File ảnh không được vượt quá 5MB'];
        }

        $uploadDir = 'uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . strtolower($ext);
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['path' => $destination];
        }

        return ['error' => 'Không thể lưu file ảnh'];
    }
}
?>
