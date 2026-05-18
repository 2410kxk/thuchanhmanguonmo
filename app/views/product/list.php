<?php include 'app/views/shares/header.php'; ?>

<div class="page-header">

    <h1>
        <i class="fa-solid fa-computer"></i>
        Danh sách sản phẩm
    </h1>

    <a href="/project1/Product/add"
       class="btn btn-danger add-product-btn">

        <i class="fa-solid fa-plus"></i>
        Thêm sản phẩm mới

    </a>

</div>

<!-- PRODUCT GRID -->
<div class="product-grid">

<?php foreach ($products as $product): ?>

    <div class="product-card">

        <!-- IMAGE -->
        <div class="product-image">

            <?php if (!empty($product->image)): ?>

                <img src="/project1/<?php echo $product->image; ?>"
                     alt="Product Image">

            <?php else: ?>

                <img src="/project1/public/images/default-product.jpg"
                     alt="Default Product">

            <?php endif; ?>

        </div>

        <!-- CONTENT -->
        <div class="product-content">

            <h2>
                <a href="/project1/Product/show/<?php echo $product->id; ?>">

                    <?php
                        echo htmlspecialchars(
                            $product->name,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>

                </a>
            </h2>

            <p class="product-description">

                <?php
                    echo htmlspecialchars(
                        $product->description,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>

            </p>

            <div class="product-category">

                <i class="fa-solid fa-layer-group"></i>

                <?php
                    echo htmlspecialchars(
                        $product->category_name,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>

            </div>

            <div class="product-price">

                <?php
                    echo number_format(
                        $product->price,
                        0,
                        ',',
                        '.'
                    );
                ?> đ

            </div>

            <!-- BUTTONS -->
            <div class="product-actions">

                <a href="/project1/Product/edit/<?php echo $product->id; ?>"
                   class="btn btn-warning">

                    <i class="fa-solid fa-pen"></i>
                    Sửa

                </a>

                <a href="/project1/Product/delete/<?php echo $product->id; ?>"
                   class="btn btn-danger"
                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">

                    <i class="fa-solid fa-trash"></i>
                    Xóa

                </a>

            </div>

        </div>

    </div>

<?php endforeach; ?>

</div>

<?php include 'app/views/shares/footer.php'; ?>