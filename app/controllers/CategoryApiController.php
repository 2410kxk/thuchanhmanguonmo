<?php
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    private function jsonHeader()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    // =========================
    // GET /api/category
    // Lấy danh sách tất cả danh mục
    // =========================
    public function index()
    {
        $this->jsonHeader();
        $categories = $this->categoryModel->getCategories();
        echo json_encode(array_values($categories));
    }

    // =========================
    // GET /api/category/{id}
    // Lấy 1 danh mục theo ID
    // =========================
    public function show($id)
    {
        $this->jsonHeader();
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy danh mục']);
        }
    }

    // =========================
    // POST /api/category
    // Thêm danh mục mới
    // =========================
    public function store()
    {
        $this->jsonHeader();

        $data        = json_decode(file_get_contents("php://input"), true) ?? [];
        $name        = $data['name']        ?? '';
        $description = $data['description'] ?? '';

        $result = $this->categoryModel->addCategory($name, $description);

        if (is_array($result)) {
            // Trả về lỗi validation 400
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            // Tạo thành công 201
            http_response_code(201);
            echo json_encode(['message' => 'Thêm danh mục thành công']);
        }
    }

    // =========================
    // PUT /api/category/{id}
    // Cập nhật danh mục
    // =========================
    public function update($id)
    {
        $this->jsonHeader();

        // Kiểm tra danh mục có tồn tại không
        $existing = $this->categoryModel->getCategoryById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy danh mục']);
            return;
        }

        $data        = json_decode(file_get_contents("php://input"), true) ?? [];
        $name        = $data['name']        ?? '';
        $description = $data['description'] ?? '';

        $result = $this->categoryModel->updateCategory($id, $name, $description);

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } elseif ($result) {
            echo json_encode(['message' => 'Cập nhật danh mục thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Cập nhật danh mục thất bại']);
        }
    }

    // =========================
    // DELETE /api/category/{id}
    // Xóa danh mục
    // =========================
    public function destroy($id)
    {
        $this->jsonHeader();

        // Kiểm tra danh mục có tồn tại không
        $existing = $this->categoryModel->getCategoryById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy danh mục']);
            return;
        }

        $result = $this->categoryModel->deleteCategory($id);

        if ($result) {
            echo json_encode(['message' => 'Xóa danh mục thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Xóa danh mục thất bại']);
        }
    }
}
?>
