<?php

require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        session_start();

        $this->db = (new Database())->getConnection();

        $this->productModel = new ProductModel($this->db);
    }

    // =========================
    // TRANG CHỦ / DANH SÁCH
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
    // CHI TIẾT SẢN PHẨM
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
    // THÊM SẢN PHẨM
    // =========================
    public function add()
    {
        $categories = (new CategoryModel($this->db))->getCategories();

        include_once 'app/views/product/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            // Upload ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

                $image = $this->uploadImage($_FILES['image']);

            } else {

                $image = "";
            }

            $result = $this->productModel->addProduct(
                $name,
                $description,
                $price,
                $category_id,
                $image
            );

            if (is_array($result)) {

                $errors = $result;

                $categories = (new CategoryModel($this->db))->getCategories();

                include 'app/views/product/add.php';

            } else {

                header('Location: /project1/Product');
                exit();
            }
        }
    }

    // =========================
    // SỬA SẢN PHẨM
    // =========================
    public function edit($id)
    {
        $product = $this->productModel->getProductById($id);

        $categories = (new CategoryModel($this->db))->getCategories();

        if ($product) {

            include 'app/views/product/edit.php';

        } else {

            echo "Không thấy sản phẩm.";
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];

            // Upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

                $image = $this->uploadImage($_FILES['image']);

            } else {

                $image = $_POST['existing_image'] ?? '';
            }

            $edit = $this->productModel->updateProduct(
                $id,
                $name,
                $description,
                $price,
                $category_id,
                $image
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
    // XÓA SẢN PHẨM
    // =========================
    public function delete($id)
    {
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

        // Tạo folder uploads nếu chưa có
        if (!is_dir($target_dir)) {

            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($file["name"]);

        $imageFileType = strtolower(
            pathinfo($target_file, PATHINFO_EXTENSION)
        );

        // Kiểm tra file ảnh
        $check = getimagesize($file["tmp_name"]);

        if ($check === false) {

            throw new Exception("File không phải là hình ảnh.");
        }

        // Kiểm tra dung lượng
        if ($file["size"] > 10 * 1024 * 1024) {

            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }

        // Kiểm tra định dạng
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {

            throw new Exception(
                "Chỉ cho phép JPG, JPEG, PNG và GIF."
            );
        }

        // Upload file
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {

            throw new Exception(
                "Có lỗi xảy ra khi tải ảnh lên."
            );
        }

        return $target_file;
    }

    // =========================
    // GIỎ HÀNG
    // =========================
    public function addToCart($id)
    {
        $product = $this->productModel->getProductById($id);

        if (!$product) {

            echo "Không tìm thấy sản phẩm.";

            return;
        }

        // Tạo cart nếu chưa có
        if (!isset($_SESSION['cart'])) {

            $_SESSION['cart'] = [];
        }

        // Nếu đã có thì tăng số lượng
        if (isset($_SESSION['cart'][$id])) {

            $_SESSION['cart'][$id]['quantity']++;

        } else {

            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }

        header('Location: /project1/Product/cart');
        exit();
    }
}

?>