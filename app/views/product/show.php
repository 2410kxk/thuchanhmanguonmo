<?php include 'app/views/shares/header.php'; ?>

<?php if ($product): ?>

<div class="product-detail-container">

    <!-- IMAGE -->
    <div class="product-detail-image">

        <?php if (!empty($product->image)): ?>

            <img src="/project1/<?php echo htmlspecialchars(
                $product->image,
                ENT_QUOTES,
                'UTF-8'
            ); ?>"
                 alt="<?php echo htmlspecialchars(
                     $product->name,
                     ENT_QUOTES,
                     'UTF-8'
                 ); ?>">

        <?php else: ?>

            <img src="/project1/public/images/default-product.jpg"
                 alt="Không có ảnh">

        <?php endif; ?>

    </div>

    <!-- INFO -->
    <div class="product-detail-info">

        <div class="product-badge">
            HOT PRODUCT
        </div>

        <h1 class="detail-title">

            <?php echo htmlspecialchars(
                $product->name,
                ENT_QUOTES,
                'UTF-8'
            ); ?>

        </h1>

        <div class="detail-category">

            <i class="fa-solid fa-layer-group"></i>

            Danh mục:

            <span>

                <?php echo !empty($product->category_name)
                    ? htmlspecialchars(
                        $product->category_name,
                        ENT_QUOTES,
                        'UTF-8'
                    )
                    : 'Chưa có danh mục'; ?>

            </span>

        </div>

        <div class="detail-price">

            <?php
                echo number_format(
                    $product->price,
                    0,
                    ',',
                    '.'
                );
            ?> đ

        </div>

        <div class="detail-description">

            <h3>
                <i class="fa-solid fa-file-lines"></i>
                Mô tả sản phẩm
            </h3>

            <p>

                <?php echo nl2br(htmlspecialchars(
                    $product->description,
                    ENT_QUOTES,
                    'UTF-8'
                )); ?>

            </p>

        </div>

        <!-- ACTION BUTTON -->
        <div class="detail-actions">

            <a href="/project1/Product/addToCart/<?php echo $product->id; ?>"
               class="btn btn-success">

                <i class="fa-solid fa-cart-shopping"></i>
                Thêm vào giỏ hàng

            </a>

            <a href="/project1/Product/edit/<?php echo $product->id; ?>"
               class="btn btn-warning">

                <i class="fa-solid fa-pen"></i>
                Sửa sản phẩm

            </a>

            <a href="/project1/Product/delete/<?php echo $product->id; ?>"
               class="btn btn-danger"
               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">

                <i class="fa-solid fa-trash"></i>
                Xóa sản phẩm

            </a>

        </div>

        <!-- BACK BUTTON -->
        <a href="/project1/Product/list"
           class="back-btn">

            <i class="fa-solid fa-arrow-left"></i>

            Quay lại danh sách sản phẩm

        </a>

    </div>

</div>

<?php else: ?>

<div class="alert alert-danger text-center">

    <h4>Không tìm thấy sản phẩm!</h4>

</div>

<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>