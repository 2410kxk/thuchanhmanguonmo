<?php

class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================
    // LẤY DANH SÁCH SẢN PHẨM
    // =========================
    public function getProducts()
    {
        $query = "SELECT 
                    p.id,
                    p.name,
                    p.description,
                    p.price,
                    p.image,
                    c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c 
                  ON p.category_id = c.id";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

// =========================
// LẤY SẢN PHẨM THEO DANH MỤC
// =========================
public function getProductsByCategory($category_id)
{
    $query = "SELECT 
                p.id,
                p.name,
                p.description,
                p.price,
                p.image,
                c.name as category_name
              FROM " . $this->table_name . " p
              LEFT JOIN category c 
              ON p.category_id = c.id
              WHERE p.category_id = :category_id";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':category_id', $category_id);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

    // =========================
    // LẤY SẢN PHẨM THEO ID
    // =========================
    public function getProductById($id)
    {
        $query = "SELECT 
                    p.*,
                    c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c
                  ON p.category_id = c.id
                  WHERE p.id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // =========================
    // THÊM SẢN PHẨM
    // =========================
    public function addProduct(
        $name,
        $description,
        $price,
        $category_id,
        $image = null
    ) {

        $errors = [];

        // Validate
        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }

        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }

        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $query = "INSERT INTO " . $this->table_name . "
                (name, description, price, category_id, image)
                VALUES
                (:name, :description, :price, :category_id, :image)";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));

        // Bind dữ liệu
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // =========================
    // CẬP NHẬT SẢN PHẨM
    // =========================
    public function updateProduct(
        $id,
        $name,
        $description,
        $price,
        $category_id,
        $image = null
    ) {

        $query = "UPDATE " . $this->table_name . "
                  SET
                    name = :name,
                    description = :description,
                    price = :price,
                    category_id = :category_id,
                    image = :image
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));

        // Bind dữ liệu
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // =========================
    // XÓA SẢN PHẨM
    // =========================
    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // =========================
    // TÌM KIẾM SẢN PHẨM
    // =========================
    public function searchProducts($search = '', $category_id = '')
    {
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(p.name LIKE :search OR p.description LIKE :search2)";
            $params[':search']  = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        if (!empty($category_id)) {
            $where[] = "p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        $whereClause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT
                    p.id,
                    p.name,
                    p.description,
                    p.price,
                    p.image,
                    c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  $whereClause";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}

?>